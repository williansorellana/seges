<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ReservationCancelled extends Notification
{
    use Queueable;

    public $reservation;
    public $reason;

    public function __construct($reservation, $reason)
    {
        $this->reservation = $reservation;
        $this->reason = $reason;
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
                    ->error()
                    ->subject('❌ Reserva Cancelada')
                    ->greeting('Hola ' . $firstName . ' ' . $firstLastName . ',')
                    ->line('Lamentamos informarte que tu reserva en ' . $this->reservation->meetingRoom->name . ' ha sido cancelada.')
                    ->line('Motivo de la cancelación: ' . $this->reason)
                    ->action('Revisar estado', route('reservations.my_reservations'))
                    ->line('Si tienes dudas, contacta con administración.')
                    ->salutation('Atte, Equipo Dimak');
    }


    public function toArray($notifiable)
    {
        return [
            'message' => 'Reserva cancelada: ' . $this->reservation->meetingRoom->name,
            'reason' => $this->reason, 
            'action_url' => route('reservations.my_reservations'),
            'icon' => 'x', 
            'color' => 'red',
            'type' => 'danger'
        ];
    }
}