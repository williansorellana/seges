<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailCustom extends VerifyEmail
{
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verifica tu correo electrónico')
            ->greeting('¡Hola!')
            ->line('Por favor, haz clic en el botón para verificar tu cuenta.')
            ->action('Verificar Correo', $verificationUrl)
            ->line('Si no creaste esta cuenta, puedes ignorar este mensaje.');
            ->salutation('App de Secretaría y Gerencia');


    }
}