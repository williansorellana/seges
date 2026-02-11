<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaintenanceAlert extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $vehicle;
    public $message;
    public $type;
    public $nextKm;

    /**
     * Create a new notification instance.
     */
    public function __construct($vehicle, $message, $type, $nextKm)
    {
        $this->vehicle = $vehicle;
        $this->message = $message;
        $this->type = $type;
        $this->nextKm = $nextKm;
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
        $subject = ($this->type === 'danger' ? '⛔ URGENTE: ' : '⚠️ Alerta: ') . $this->message;

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Estimado/a ' . $notifiable->short_name . ',')
            ->line($this->message)
            ->line('Detalles del Vehículo:')
            ->line('Marca/Modelo: ' . $this->vehicle->brand . ' ' . $this->vehicle->model)
            ->line('Patente: ' . $this->vehicle->plate)
            ->line('Próximo cambio de aceite sugerido a los: ' . number_format($this->nextKm, 0, ',', '.') . ' km');

        if ($this->type === 'danger') {
            $mail->error();
        }

        return $mail->action('Ver Historial', route('vehicles.maintenance.history', ['vehicle' => $this->vehicle->id]))
            ->salutation('Atte, Administración de Flota');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'vehicle_id' => $this->vehicle->id,
            'plate' => $this->vehicle->plate,
            'brand_model' => $this->vehicle->brand . ' ' . $this->vehicle->model,
            'image_path' => $this->vehicle->image_path,
            'message' => $this->message,
            'type' => $this->type, // 'danger' or 'warning'
            'next_oil_change_km' => $this->nextKm,
        ];
    }


}
