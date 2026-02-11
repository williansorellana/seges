<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\VehicleRequest;

class VehicleEarlyTerminationNotification extends Notification
{
    use Queueable;

    public $request;

    /**
     * Create a new notification instance.
     */
    public function __construct(VehicleRequest $request)
    {
        $this->request = $request;
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
        return (new MailMessage)
            ->subject('⚠️ Asignación Finalizada: ' . $this->request->vehicle->brand . ' ' . $this->request->vehicle->model)
            ->greeting('Hola ' . $notifiable->short_name . ',')
            ->line('Tu asignación del vehículo ha sido finalizada anticipadamente.')
            ->line('**Motivo:** ' . $this->request->early_termination_reason)
            ->line('**Fecha Original de Término:** ' . $this->request->original_end_date->format('d/m/Y H:i'))
            ->line('**Fecha Real de Término:** ' . $this->request->end_date->format('d/m/Y H:i'))
            ->action('Ver Mis Reservas', route('requests.index', ['tab' => 'completed']))
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
            'vehicle_id' => $this->request->vehicle_id,
            'plate' => $this->request->vehicle->plate,
            'brand_model' => $this->request->vehicle->brand . ' ' . $this->request->vehicle->model,
            'image_path' => $this->request->vehicle->image_path,
            'message' => 'Asignación terminada anticipadamente: ' . $this->request->early_termination_reason,
            'type' => 'warning',
            'request_id' => $this->request->id,
        ];
    }
}
