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
        return ['database'];
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
