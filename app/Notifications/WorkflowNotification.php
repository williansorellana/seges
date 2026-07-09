<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkflowNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $message,
        public ?string $actionUrl = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'action_url' => $this->actionUrl,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->title)
            ->greeting('Hola ' . ($notifiable->name ?? ''))
            ->line($this->message);

        if ($this->actionUrl) {
            $mail->action('Revisar en SEGES', $this->actionUrl);
        }

        return $mail
            ->line('Este correo fue generado automáticamente por el sistema SEGES / Secretaría y Gerencia.')
            ->salutation('Saludos, SEGES Dimak');
    }
}