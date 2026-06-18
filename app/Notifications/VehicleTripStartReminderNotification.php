<?php

namespace App\Notifications;

use App\Models\VehicleRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VehicleTripStartReminderNotification extends Notification
{
    use Queueable;

    protected VehicleRequest $vehicleRequest;

    public function __construct(VehicleRequest $vehicleRequest)
    {
        $this->vehicleRequest = $vehicleRequest;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $vehicle = $this->vehicleRequest->vehicle;

        return (new MailMessage)
            ->subject('🚗 Recordatorio: debe iniciar su viaje')
            ->greeting('Hola ' . $notifiable->short_name . ',')
            ->line('Su reserva de vehículo ya se encuentra aprobada y llegó la hora de inicio.')
            ->line('Vehículo: ' . $vehicle->brand . ' ' . $vehicle->model . ' (' . $vehicle->plate . ')')
            ->line('Inicio programado: ' . $this->vehicleRequest->start_date->format('d/m/Y H:i'))
            ->line('Término programado: ' . $this->vehicleRequest->end_date->format('d/m/Y H:i'))
            ->line('Por favor ingrese al sistema y presione la opción para iniciar el viaje.')
            ->action('Ver Mis Reservas', route('requests.index'))
            ->salutation('Atte, Equipo de Gerencia');
    }
}
