<?php

namespace App\Console\Commands;

use App\Models\VehicleRequest;
use App\Notifications\VehicleTripStartReminderNotification;
use App\Notifications\VehicleTripEndReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendVehicleTripReminders extends Command
{
    protected $signature = 'vehicles:send-trip-reminders';

    protected $description = 'Envía correos recordatorios para iniciar y finalizar viajes de vehículos.';

    public function handle(): int
    {
        $now = now();

        $startReminders = VehicleRequest::with(['user', 'vehicle'])
            ->where('status', 'approved')
            ->where('start_date', '<=', $now)
            ->whereNull('start_reminder_sent_at')
            ->get();

        foreach ($startReminders as $vehicleRequest) {
            try {
                $vehicleRequest->user->notify(
                    new VehicleTripStartReminderNotification($vehicleRequest)
                );

                $vehicleRequest->update([
                    'start_reminder_sent_at' => $now,
                ]);

                $this->info("Recordatorio de inicio enviado para solicitud ID {$vehicleRequest->id}");
            } catch (\Exception $e) {
                Log::error('Error al enviar recordatorio de inicio de viaje', [
                    'vehicle_request_id' => $vehicleRequest->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $endReminders = VehicleRequest::with(['user', 'vehicle'])
            ->where('status', 'in_trip')
            ->where('end_date', '<=', $now)
            ->whereNull('end_reminder_sent_at')
            ->get();

        foreach ($endReminders as $vehicleRequest) {
            try {
                $vehicleRequest->user->notify(
                    new VehicleTripEndReminderNotification($vehicleRequest)
                );

                $vehicleRequest->update([
                    'end_reminder_sent_at' => $now,
                ]);

                $this->info("Recordatorio de término enviado para solicitud ID {$vehicleRequest->id}");
            } catch (\Exception $e) {
                Log::error('Error al enviar recordatorio de término de viaje', [
                    'vehicle_request_id' => $vehicleRequest->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info('Proceso de recordatorios de vehículos finalizado.');

        return Command::SUCCESS;
    }
}
