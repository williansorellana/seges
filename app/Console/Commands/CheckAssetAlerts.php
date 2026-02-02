<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AssetAssignment;
use App\Models\User;
use App\Models\Worker;
use App\Notifications\AssetOverdueNotification;
use Carbon\Carbon;

class CheckAssetAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assets:check-alerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica asignaciones atrasadas y envía notificaciones.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando verificación de activos atrasados...');

        $now = Carbon::now();

        // Buscar asignaciones activas (sin devolución)
        // Que hayan pasado su fecha estimada de devolución
        // Que no hayan sido alertadas
        $overdueAssignments = AssetAssignment::with(['asset', 'user', 'worker'])
            ->whereNull('fecha_devolucion')
            ->whereNotNull('fecha_estimada_devolucion')
            ->where('fecha_estimada_devolucion', '<', $now->subDay()) // Al menos 1 día de atraso
            ->where('alerted_overdue', false)
            ->get();

        foreach ($overdueAssignments as $assignment) {
            $this->info("Procesando asignación ID: {$assignment->id}");

            // Notificar a los administradores (o usuarios con rol adecuado)
            // En este caso, notificamos al propio usuario si es interno? 
            // O a todos los admins? "necesito un sistema de notificaciones por activos... enviar una notificacion"
            // Asumo que la notificación es para el usuario actual del sistema (Admin/Encargado).

            $admins = User::all(); // En un sistema real filtraríamos por rol. Aquí enviamos a todos.
            // Ojo: Enviar a todos puede ser spam. Enviemos solo al usuario logueado? No, esto es consola.
            // Enviemos a los usuarios que tengan permiso de gestionar activos.

            // Por simplicidad y seguridad, enviamos a todos los usuarios del sistema por ahora, 
            // o idealmente a un usuario específico si supiéramos quién es el encargado.
            // Para "no quiero errores", enviamos a todos los usuarios.

            foreach ($admins as $admin) {
                $admin->notify(new AssetOverdueNotification($assignment));
            }

            // Marcar como alertado
            $assignment->alerted_overdue = true;
            $assignment->save();
        }

        $this->info('Verificación completada. ' . $overdueAssignments->count() . ' notificaciones enviadas.');
    }
}
