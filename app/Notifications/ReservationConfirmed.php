<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationConfirmed extends Notification
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
        $firstName = explode(' ', trim($notifiable->name))[0];
        $firstLastName = explode(' ', trim($notifiable->last_name))[0];
        return (new MailMessage)
                    ->subject('✅ Reserva Confirmada')
                    ->greeting('¡Hola ' . $firstName . ' ' . $firstLastName . '!')
                    ->line('Tu reserva en ' . $this->reservation->meetingRoom->name . ' ha sido confirmada exitosamente.')
                    ->line('Detalles del horario:')
                    ->line('Inicio: ' . $this->reservation->start_time->format('d/m/Y H:i'))
                    ->line('Fin:    ' . $this->reservation->end_time->format('d/m/Y H:i'))
                    ->action('Ver mis reservas', route('reservations.my_reservations'))
                    ->line('Te recomendamos llegar puntual a la sala.')
                    ->salutation('Atte, Equipo Dimak');
                    
    }

    public function toArray($notifiable)
    {
        return [
            'message' => '¡Aprobada! Tu reserva en ' . $this->reservation->meetingRoom->name . ' ha sido confirmada.',
            'action_url' => route('reservations.my_reservations'),
            'icon' => 'check',
            'color' => 'green'
        ];
    }
}