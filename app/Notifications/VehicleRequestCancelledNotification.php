<?php

namespace App\Notifications;

use App\Models\VehicleRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VehicleRequestCancelledNotification extends Notification
{
    use Queueable;

    protected $vehicleRequest;

    public function __construct(VehicleRequest $vehicleRequest)
    {
        $this->vehicleRequest = $vehicleRequest;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('❌ Solicitud de Vehículo Cancelada')
            ->greeting('Hola ' . $notifiable->short_name . ',')
            ->line('Una solicitud de vehículo fue cancelada.')
            ->line('Solicitante: ' . $this->vehicleRequest->user->short_name)
            ->line('Vehículo: ' . $this->vehicleRequest->vehicle->brand . ' ' . $this->vehicleRequest->vehicle->model)
            ->line('Periodo: ' .
                $this->vehicleRequest->start_date->format('d/m/Y H:i') .
                ' hasta ' .
                $this->vehicleRequest->end_date->format('d/m/Y H:i'))
            ->action('Ver Solicitudes', route('vehicles.index'))
            ->salutation('Atte, Equipo de Gerencia');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Solicitud cancelada: ' .
                $this->vehicleRequest->vehicle->brand . ' ' .
                $this->vehicleRequest->vehicle->model,

            'action_url' => route('vehicles.index'),

            'type' => 'warning',

            'icon' => 'x-circle',

            'request_id' => $this->vehicleRequest->id,

            'user' => $this->vehicleRequest->user->short_name,
        ];
    }
}