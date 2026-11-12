<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cost_centers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('area');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        foreach (require config_path('dimak_cost_centers.php') as $code => $area) {
            DB::table('cost_centers')->insert(['code' => $code, 'area' => $area, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
        }
        Schema::table('rut_cost_centers', function (Blueprint $table) {
            $table->foreignId('cost_center_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->restrictOnDelete();
        });
        DB::table('rut_cost_centers')->orderBy('id')->each(function ($mapping) {
            $center = DB::table('cost_centers')->where('code', $mapping->cost_center)->first();
            $centerId = $center?->id ?? DB::table('cost_centers')->insertGetId([
                'code' => $mapping->cost_center, 'area' => $mapping->description ?: $mapping->cost_center,
                'is_active' => $mapping->is_active, 'created_at' => now(), 'updated_at' => now(),
            ]);
            $rut = preg_replace('/[^0-9K]/', '', strtoupper($mapping->rut));
            $matches = DB::table('users')->whereNull('deleted_at')->get(['id', 'rut'])->filter(
                fn ($user) => preg_replace('/[^0-9K]/', '', strtoupper((string) $user->rut)) === $rut
            );
            $userId = $matches->count() === 1 ? $matches->first()->id : null;
            if ($userId && DB::table('rut_cost_centers')->where('user_id', $userId)->exists()) {
                $userId = null;
            }
            DB::table('rut_cost_centers')->where('id', $mapping->id)->update(['cost_center_id' => $centerId, 'user_id' => $userId]);
        });
        Schema::table('renditions', function (Blueprint $table) {
            $table->unsignedInteger('attempt_number')->default(1);
            $table->foreignId('replaces_rendition_id')->nullable()->unique()->constrained('renditions')->restrictOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->json('rejection_snapshot')->nullable();
            $table->unique(['route_planning_id', 'attempt_number'], 'renditions_planning_attempt_unique');
        });
        Schema::table('renditions', fn (Blueprint $table) => $table->dropUnique('renditions_route_planning_id_unique'));
        DB::table('renditions')->where('status', 'rejected')->orderBy('id')->each(function ($rendition) {
            $last = DB::table('workflow_histories')->where('workflowable_type', \App\Models\Rendition::class)
                ->where('workflowable_id', $rendition->id)->where('to_status', 'rejected')->orderByDesc('id')->first();
            DB::table('renditions')->where('id', $rendition->id)->update([
                'closed_at' => $rendition->last_rejected_at ?: ($last?->created_at ?? now()),
                'rejection_reason' => $last?->observation ?? 'Rechazo anterior: revisar historial de observaciones.',
                'correction_deadline_at' => null,
                'rejection_snapshot' => json_encode(['legacy_snapshot_at' => now()->toIso8601String(),
                    'rendition' => $rendition, 'expenses' => DB::table('rendition_expenses')->where('rendition_id', $rendition->id)->get()]),
            ]);
        });
        Schema::table('route_plannings', function (Blueprint $table) {
            $table->unsignedInteger('lodging_nights')->nullable();
            $table->decimal('lodging_excess_amount', 12, 2)->default(0);
            $table->string('lodging_excess_status')->default('not_required');
            $table->foreignId('lodging_authorized_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('lodging_authorized_at')->nullable();
        });
        // Approved historical requests retain their original approvals. New and
        // pending requests must explicitly authorize any additional budget.
        DB::table('route_plannings')->where('funds_alojamiento', '>', 0)->orderBy('id')->each(function ($plan) {
            $nights = max(1, (int) \Carbon\Carbon::parse($plan->start_date)->diffInDays(\Carbon\Carbon::parse($plan->end_date)));
            $excess = max(0, (float) $plan->funds_alojamiento - 41650 * $nights);
            DB::table('route_plannings')->where('id', $plan->id)->update(['lodging_nights' => $nights,
                'lodging_excess_amount' => $excess,
                'lodging_excess_status' => $excess > 0 ? (in_array($plan->status, ['approved', 'rejected']) ? 'legacy' : 'pending') : 'not_required']);
        });
    }

    public function down(): void
    {
        if (DB::table('renditions')->where('attempt_number', '>', 1)->exists()) {
            throw new RuntimeException('No se puede revertir sin perder intentos. Conserve el historial y use una migración correctiva.');
        }
        Schema::table('route_plannings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lodging_authorized_by');
            $table->dropColumn(['lodging_nights', 'lodging_excess_amount', 'lodging_excess_status', 'lodging_authorized_at']);
        });
        Schema::table('renditions', function (Blueprint $table) {
            $table->dropUnique('renditions_planning_attempt_unique');
            $table->dropForeign(['replaces_rendition_id']);
            $table->dropUnique(['replaces_rendition_id']);
            $table->dropColumn('replaces_rendition_id');
            $table->dropColumn(['attempt_number', 'closed_at', 'rejection_reason', 'rejection_snapshot']);
            $table->unique('route_planning_id', 'renditions_route_planning_id_unique');
        });
        Schema::table('rut_cost_centers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cost_center_id');
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id']);
            $table->dropColumn('user_id');
        });
        Schema::dropIfExists('cost_centers');
    }
};
