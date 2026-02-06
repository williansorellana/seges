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
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🚙 Nueva Solicitud de Vehículo: ' . $this->vehicleRequest->vehicle->brand)
            ->greeting('Hola ' . $notifiable->short_name . ',')
            ->line('Se ha recibido una nueva solicitud de reserva de vehículo que requiere revisión.')
            ->line('Solicitante: ' . $this->vehicleRequest->user->short_name)
            ->line('Vehículo: ' . $this->vehicleRequest->vehicle->brand . ' ' . $this->vehicleRequest->vehicle->model)
            ->line('Periodo: ' . $this->vehicleRequest->start_date->format('d/m/Y H:i') . ' hasta ' . $this->vehicleRequest->end_date->format('d/m/Y H:i'))
            ->line('Destino: ' . ($this->vehicleRequest->destination_type === 'local' ? 'Local' : 'Fuera de ciudad'))
            ->action('Revisar Solicitud', route('vehicles.index', [
                'open_requests' => 'true',
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
