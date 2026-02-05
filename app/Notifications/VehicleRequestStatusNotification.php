<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VehicleRequestStatusNotification extends Notification
{
    use Queueable;

    protected $vehicleRequest;
    protected $status;
    protected $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct($vehicleRequest, $status, $reason = null)
    {
        $this->vehicleRequest = $vehicleRequest;
        $this->status = $status;
        $this->reason = $reason;
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
        $title = $this->status === 'approved'
            ? 'Solicitud Aprobada'
            : 'Solicitud Rechazada';

        $message = $this->status === 'approved'
            ? 'Tu solicitud para el vehículo ' . $this->vehicleRequest->vehicle->brand . ' ha sido aprobada.'
            : 'Tu solicitud para el vehículo ' . $this->vehicleRequest->vehicle->brand . ' ha sido rechazada.';

        return [
            'message' => $message,
            'reason' => $this->reason,
            'action_url' => route('requests.index', ['highlight_id' => $this->vehicleRequest->id]),
            'type' => $this->status === 'approved' ? 'success' : 'danger',
            'icon' => $this->status === 'approved' ? 'check' : 'x',
            'request_id' => $this->vehicleRequest->id,
            'vehicle_id' => $this->vehicleRequest->vehicle_id,
            'brand_model' => $this->vehicleRequest->vehicle->brand . ' ' . $this->vehicleRequest->vehicle->model,
            'plate' => $this->vehicleRequest->vehicle->plate,
        ];
    }
}
