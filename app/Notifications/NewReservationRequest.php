<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReservationRequest extends Notification
{
    use Queueable;

    public $reservation;

    public function __construct($reservation)
    {
        $this->reservation = $reservation;
    }

    public function via($notifiable)
    {
        return ['database', 'mail']; 
    }

    public function toMail($notifiable)
    {
        $firstNameNotifiable = explode(' ', trim($notifiable->name))[0];
        $firstLastNameNotifiable = explode(' ', trim($notifiable->last_name))[0];

        $firstNameUser = explode(' ', trim($this->reservation->user->name))[0];
        $firstLastNameUser = explode(' ', trim($this->reservation->user->last_name))[0];
        return (new MailMessage)
                    ->subject('🔔 Nueva Solicitud de Sala')
                    ->greeting('Hola ' . $firstNameNotifiable . ' ' . $firstLastNameNotifiable . ',')
                    ->line('Se ha recibido una nueva solicitud de reserva.')
                    ->line('Sala: ' . $this->reservation->meetingRoom->name)
                    ->line('Solicitante: ' . $firstNameUser . ' ' . $firstLastNameUser)
                    ->line('Inicio: ' . $this->reservation->start_time->format('d/m/Y H:i'))
                    ->line('Fin:    ' . $this->reservation->end_time->format('d/m/Y H:i'))
                    ->action('Gestionar Solicitudes', route('rooms.index'))
                    ->line('Por favor, revisa la solicitud lo antes posible.')
                    ->salutation('Gestión de Reservas');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Nueva solicitud: ' . $this->reservation->meetingRoom->name . ' (' . $this->reservation->start_time->format('d/m H:i') . ')',
            'action_url' => route('rooms.index'),
            'icon' => 'bell',
            'color' => 'yellow'
        ];
    }
}