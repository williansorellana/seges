<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            foreach (['route_plannings' => App\Models\RoutePlanning::class, 'renditions' => App\Models\Rendition::class] as $table => $type) {
                foreach (DB::table($table)->where('status', 'pending_controlling')->lockForUpdate()->get() as $row) {
                    DB::table($table)->where('id', $row->id)->update(['status' => 'pending_finances', 'updated_at' => now()]);
                    DB::table('workflow_histories')->insert([
                        'workflowable_type' => $type, 'workflowable_id' => $row->id,
                        'user_id' => $row->user_id, 'action' => 'rerouted_to_finances',
                        'from_status' => 'pending_controlling', 'to_status' => 'pending_finances',
                        'observation' => 'Actualización automática del flujo: Jefatura deriva directamente a Finanzas. No corresponde a una aprobación del trabajador.',
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                }
            }
        });
    }

    public function down(): void
    {
        // Business transitions and their audit trail remain intact on rollback.
        // They cannot be reversed after a reviewer has acted on the new queue.
    }
};
