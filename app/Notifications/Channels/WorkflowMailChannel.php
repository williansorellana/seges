<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Channels\MailChannel;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class WorkflowMailChannel extends MailChannel
{
    public function send($notifiable, Notification $notification)
    {
        try {
            return parent::send($notifiable, $notification);
        } catch (TransportExceptionInterface $exception) {
            // The database notification has already been stored. A transport
            // outage must not undo a submission, approval or rejection.
            Log::warning('No se pudo entregar el correo de workflow; el aviso permanece en SEGES.', [
                'notification_id' => $notification->id,
                'recipient_id' => $notifiable->getKey(),
                'transport_error' => $exception::class,
            ]);
            if (request()->hasSession()) {
                request()->session()->flash('mail_warning', 'La operación se guardó y el aviso está en SEGES, pero no se pudo entregar el correo.');
            }

            return null;
        }
    }
}
