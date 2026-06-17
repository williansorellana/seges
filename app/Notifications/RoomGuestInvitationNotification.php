<?php

namespace App\Notifications;

use App\Models\RoomReservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RoomGuestInvitationNotification extends Notification
{
    use Queueable;

    protected RoomReservation $reservation;
    protected string $guestName;

    public function __construct(RoomReservation $reservation, string $guestName)
    {
        $this->reservation = $reservation;
        $this->guestName = $guestName;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $room = $this->reservation->meetingRoom;
        $user = $this->reservation->user;

        return (new MailMessage)
            ->subject('📅 Invitación a reunión: ' . $this->reservation->purpose)
            ->greeting('Hola ' . $this->guestName . ',')
            ->line('Has sido invitado/a a una reunión reservada en el sistema.')
            ->line('Sala: ' . ($room->name ?? 'Sala no disponible'))
            ->line('Solicitante: ' . ($user->name ?? 'Usuario') . ' ' . ($user->last_name ?? ''))
            ->line('Inicio: ' . $this->reservation->start_time->format('d/m/Y H:i'))
            ->line('Término: ' . $this->reservation->end_time->format('d/m/Y H:i'))
            ->line('Motivo: ' . $this->reservation->purpose)
            ->line('Puedes agregar esta reunión manualmente a tu calendario usando los datos anteriores.')
            ->salutation('Atte, Equipo de Gerencia');
    }
}
