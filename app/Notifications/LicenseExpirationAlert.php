<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LicenseExpirationAlert extends Notification
{
    use Queueable;

    public $daysRemaining;
    public $isExpired;

    /**
     * Create a new notification instance.
     *
     * @param int $daysRemaining Days remaining until expiration (can be negative if expired).
     */
    public function __construct($daysRemaining)
    {
        $this->daysRemaining = $daysRemaining;
        $this->isExpired = $daysRemaining <= 0;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)->subject('Aviso de Vencimiento de Licencia de Conducir');

        if ($this->isExpired) {
            $mail->greeting('¡Su licencia ha vencido!')
                ->line('Su licencia de conducir ha expirado. Por favor, renuévela lo antes posible.')
                ->line('Importante: No podrá solicitar vehículos hasta que actualice su licencia.')
                ->action('Actualizar Licencia', route('profile.edit', ['active_tab' => 'license']));
        } else {
            $mail->greeting('Su licencia está por vencer')
                ->line("Su licencia de conducir vencerá en {$this->daysRemaining} días.")
                ->line('Le recomendamos iniciar los trámites de renovación.')
                ->action('Ver Perfil', route('profile.edit', ['active_tab' => 'license']));
        }

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        if ($this->isExpired) {
            return [
                'title' => 'Licencia Vencida',
                'message' => 'Su licencia de conducir ha expirado. Debe actualizarla para solicitar vehículos.',
                'action_url' => route('profile.edit', ['active_tab' => 'license']) . '#license-section',
                'icon' => 'exclamation-triangle',
                'type' => 'danger',
            ];
        }

        return [
            'title' => 'Licencia por Vencer',
            'message' => "Su licencia de conducir vence en {$this->daysRemaining} días.",
            'action_url' => route('profile.edit', ['active_tab' => 'license']) . '#license-section',
            'icon' => 'clock',
            'type' => 'warning',
        ];
    }
}
