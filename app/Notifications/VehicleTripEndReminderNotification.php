<?php

namespace App\Notifications;

use App\Models\VehicleRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VehicleTripEndReminderNotification extends Notification
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
            ->subject('⏰ Recordatorio: debe finalizar su viaje')
            ->greeting('Hola ' . $notifiable->short_name . ',')
            ->line('Se ha cumplido el horario de término de su reserva.')
            ->line('Vehículo: ' . $vehicle->brand . ' ' . $vehicle->model . ' (' . $vehicle->plate . ')')
            ->line('Hora programada de devolución: ' . $this->vehicleRequest->end_date->format('d/m/Y H:i'))
            ->line('Por favor ingrese al sistema y finalice el viaje para registrar correctamente la devolución del vehículo.')
            ->action('Ver Mis Reservas', route('requests.index'))
            ->salutation('Atte, Equipo de Gerencia');
    }
}
