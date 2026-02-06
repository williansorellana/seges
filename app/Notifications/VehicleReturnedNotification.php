<?php

namespace App\Notifications;

use App\Models\VehicleRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VehicleReturnedNotification extends Notification
{
    use Queueable;

    protected $vehicleRequest;
    protected $hasDamage;

    /**
     * Create a new notification instance.
     */
    public function __construct(VehicleRequest $vehicleRequest, bool $hasDamage = false)
    {
        $this->vehicleRequest = $vehicleRequest;
        $this->hasDamage = $hasDamage;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->hasDamage
            ? '⚠️ ALERTA: Vehículo devuelto con DAÑOS'
            : '✅ Vehículo devuelto correctamente';

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Estimado Administrador,');

        $vehicle = $this->vehicleRequest->vehicle;
        $driver = $this->vehicleRequest->user;

        $mail->line('Detalles de la devolución:')
            ->line('Vehículo: ' . $vehicle->brand . ' ' . $vehicle->model . ' (' . $vehicle->plate . ')')
            ->line('Conductor: ' . $driver->name . ' ' . $driver->last_name);

        if ($this->hasDamage) {
            $mail->error()
                ->line('⛔ SE HAN REPORTADO DAÑOS EN EL VEHÍCULO.')
                ->line('Por favor revise el historial de mantenimiento para más detalles y evidencia.');
        } else {
            $mail->line('El vehículo se ha recibido sin novedades.');
        }

        return $mail->action('Ver Historial', route('vehicles.maintenance.history', [
            'vehicle' => $this->vehicleRequest->vehicle_id,
            'tab' => 'returns',
            'highlight_id' => $this->vehicleRequest->id
        ]))
            ->salutation('Atte, Equipo de Gerencia');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $message = $this->hasDamage
            ? 'ALERTA: Vehículo devuelto con DAÑOS - ' . $this->vehicleRequest->vehicle->brand . ' ' . $this->vehicleRequest->vehicle->model
            : 'Vehículo devuelto correctamente - ' . $this->vehicleRequest->vehicle->brand . ' ' . $this->vehicleRequest->vehicle->model;

        return [
            'message' => $message,
            'action_url' => route('vehicles.maintenance.history', [
                'vehicle' => $this->vehicleRequest->vehicle_id,
                'tab' => 'returns',
                'highlight_id' => $this->vehicleRequest->id
            ]),
            'type' => $this->hasDamage ? 'danger' : 'success',
            'icon' => $this->hasDamage ? 'exclamation' : 'check',
            'request_id' => $this->vehicleRequest->id,
            'damage_reported' => $this->hasDamage,
        ];
    }
}
