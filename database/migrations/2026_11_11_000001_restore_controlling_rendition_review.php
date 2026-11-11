<?php

use App\Models\Rendition;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            foreach (DB::table('renditions')->whereNull('deleted_at')->where('status', 'pending_finances')->lockForUpdate()->get() as $row) {
                // Review only the current attempt's latest approval chain.
                $lastStep = DB::table('workflow_histories')->where('workflowable_type', Rendition::class)
                    ->where('workflowable_id', $row->id)->whereIn('action', [
                        'submitted_by_worker', 'approved_by_jefatura', 'approved_by_controlling', 'rerouted_to_finances',
                    ])->orderByDesc('id')->first();
                if ($lastStep?->action === 'approved_by_controlling') {
                    continue;
                }
                DB::table('renditions')->where('id', $row->id)->update(['status' => 'pending_controlling', 'updated_at' => now()]);
                DB::table('workflow_histories')->insert([
                    'workflowable_type' => Rendition::class, 'workflowable_id' => $row->id,
                    'user_id' => $row->user_id, 'action' => 'restored_controlling_review',
                    'from_status' => 'pending_finances', 'to_status' => 'pending_controlling',
                    'observation' => 'Derivación automática por aclaración de supervisión: los comprobantes requieren revisión de Controlling antes de Finanzas. No es una acción del trabajador.',
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        });
    }

    public function down(): void
    {
        // Preserve decisions and the audit trail after the restored review.
    }
};
