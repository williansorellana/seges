<?php

namespace App\Notifications;

use App\Models\VehicleRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewVehicleRequestNotification extends Notification
{
    use Queueable;

    protected $vehicleRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(VehicleRequest $vehicleRequest)
    {
        $this->vehicleRequest = $vehicleRequest;
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
        return [
            'message' => 'Nueva solicitud de vehículo pendiente: ' . $this->vehicleRequest->vehicle->brand . ' ' . $this->vehicleRequest->vehicle->model,
            'action_url' => route('vehicles.index', ['open_requests' => 'true']),
            'type' => 'info',
            'icon' => 'calendar',
            'request_id' => $this->vehicleRequest->id,
            'user' => $this->vehicleRequest->user->short_name,
        ];
    }
}
