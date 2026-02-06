<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VehicleRequestStatusNotification extends Notification
{
    use Queueable;

    protected $vehicleRequest;
    protected $status;
    protected $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct($vehicleRequest, $status, $reason = null)
    {
        $this->vehicleRequest = $vehicleRequest;
        $this->status = $status;
        $this->reason = $reason;
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
        $subject = $this->status === 'approved'
            ? '✅ Solicitud Aprobada: ' . $this->vehicleRequest->vehicle->brand
            : '❌ Solicitud Rechazada: ' . $this->vehicleRequest->vehicle->brand;

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Hola ' . $notifiable->short_name . ',');

        if ($this->status === 'approved') {
            $mail->line('Tu solicitud para el vehículo ' . $this->vehicleRequest->vehicle->brand . ' ' . $this->vehicleRequest->vehicle->model . ' ha sido APROBADA.')
                ->line('Periodo: ' . $this->vehicleRequest->start_date->format('d/m/Y H:i') . ' hasta ' . $this->vehicleRequest->end_date->format('d/m/Y H:i'));
        } else {
            $mail->line('Tu solicitud para el vehículo ' . $this->vehicleRequest->vehicle->brand . ' ' . $this->vehicleRequest->vehicle->model . ' ha sido RECHAZADA.')
                ->line('Motivo: ' . $this->reason);
        }

        return $mail->action('Ver Detalles', route('requests.index', ['highlight_id' => $this->vehicleRequest->id]))
            ->salutation('Atte, Equipo de Gerencia')
            ->line('Gracias por utilizar nuestro sistema de gestión.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $title = $this->status === 'approved'
            ? 'Solicitud Aprobada'
            : 'Solicitud Rechazada';

        $message = $this->status === 'approved'
            ? 'Tu solicitud para el vehículo ' . $this->vehicleRequest->vehicle->brand . ' ha sido aprobada.'
            : 'Tu solicitud para el vehículo ' . $this->vehicleRequest->vehicle->brand . ' ha sido rechazada.';

        return [
            'message' => $message,
            'reason' => $this->reason,
            'action_url' => route('requests.index', ['highlight_id' => $this->vehicleRequest->id]),
            'type' => $this->status === 'approved' ? 'success' : 'danger',
            'icon' => $this->status === 'approved' ? 'check' : 'x',
            'request_id' => $this->vehicleRequest->id,
            'vehicle_id' => $this->vehicleRequest->vehicle_id,
            'brand_model' => $this->vehicleRequest->vehicle->brand . ' ' . $this->vehicleRequest->vehicle->model,
            'plate' => $this->vehicleRequest->vehicle->plate,
        ];
    }
}
