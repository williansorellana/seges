<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_calendar_years', function (Blueprint $table) {
            $table->unsignedInteger('year')->primary();
            $table->timestamps();
        });
        Schema::create('business_holidays', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->string('name');
            $table->timestamps();
        });
        foreach (config('business_calendar') as $year => $holidays) {
            DB::table('business_calendar_years')->insert(['year' => $year, 'created_at' => now(), 'updated_at' => now()]);
            foreach ($holidays as $date => $name) {
                DB::table('business_holidays')->insert(['date' => $date, 'name' => $name, 'created_at' => now(), 'updated_at' => now()]);
            }
        }
        Schema::create('planning_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // daily_extra or temporary_unlock
            $table->date('valid_on');
            $table->string('status')->default('pending');
            $table->text('request_reason');
            $table->text('decision_reason')->nullable();
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'type', 'valid_on']);
        });
        Schema::table('route_plannings', function (Blueprint $table) {
            $table->date('requested_on')->nullable()->index();
        });
        // Laravel writes timestamps in the application timezone. Snapshot the
        // creation day so daily limits do not depend on SQL date conversion.
        DB::table('route_plannings')->select('id', 'created_at')->orderBy('id')->each(function ($planning) {
            if ($planning->created_at) {
                DB::table('route_plannings')->where('id', $planning->id)->update([
                    'requested_on' => \Carbon\Carbon::parse($planning->created_at, config('app.timezone'))->toDateString(),
                ]);
            }
        });
        Schema::table('renditions', function (Blueprint $table) {
            $table->timestamp('first_submitted_at')->nullable();
            $table->timestamp('last_submitted_at')->nullable();
            $table->timestamp('last_rejected_at')->nullable();
            $table->date('correction_deadline_at')->nullable();
        });
        // Recover historical submission/rejection dates without changing the
        // deadline previously communicated to the worker.
        DB::table('renditions')->select('id', 'status')->orderBy('id')->each(function ($rendition) {
            $history = DB::table('workflow_histories')->where('workflowable_type', \App\Models\Rendition::class)
                ->where('workflowable_id', $rendition->id);
            $first = (clone $history)->where('action', 'submitted_by_worker')->min('created_at');
            $last = (clone $history)->where('action', 'submitted_by_worker')->max('created_at');
            $rejected = (clone $history)->whereIn('action', ['rejected_by_jefatura', 'rejected_by_controlling', 'rejected_by_finances'])->max('created_at');
            DB::table('renditions')->where('id', $rendition->id)->update([
                'first_submitted_at' => $first, 'last_submitted_at' => $last, 'last_rejected_at' => $rejected,
                // Existing returned documents receive a fresh correction window
                // at rollout rather than an immediate retroactive penalty.
                'correction_deadline_at' => $rendition->status === 'rejected'
                    ? app(\App\Services\RenditionDeadlineService::class)->addBusinessDays(now(), 2)->toDateString() : null,
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('renditions', fn (Blueprint $table) => $table->dropColumn(['first_submitted_at', 'last_submitted_at', 'last_rejected_at', 'correction_deadline_at']));
        Schema::table('route_plannings', function (Blueprint $table) {
            $table->dropIndex(['requested_on']);
            $table->dropColumn('requested_on');
        });
        Schema::dropIfExists('planning_exceptions');
        Schema::dropIfExists('business_holidays');
        Schema::dropIfExists('business_calendar_years');
    }
};
