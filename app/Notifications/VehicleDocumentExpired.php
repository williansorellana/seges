<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VehicleDocumentExpired extends Notification
{
    use Queueable;

    public $document;
    public $vehicle;
    public $daysRemaining;

    /**
     * Create a new notification instance.
     */
    public function __construct($document, $daysRemaining)
    {
        $this->document = $document;
        $this->vehicle = $document->vehicle;
        $this->daysRemaining = $daysRemaining;
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
        $typeLabels = [
            'insurance' => 'Seguro',
            'permit' => 'Permiso de Circulación',
            'technical_review' => 'Revisión Técnica',
        ];

        $docName = $typeLabels[$this->document->type] ?? 'Documento';
        $status = $this->daysRemaining <= 0 ? 'VENCIDO' : 'por vencer';
        $subject = ($this->daysRemaining <= 0 ? '⛔ URGENTE: ' : '⚠️ Aviso: ') . "{$docName} {$status}";

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Estimado/a ' . $notifiable->short_name . ',');

        $messageTop = $this->daysRemaining <= 0
            ? "El documento **{$docName}** del vehículo ha **VENCIDO**."
            : "El documento **{$docName}** del vehículo vencerá en **{$this->daysRemaining} días**.";

        $mail->line($messageTop)
            ->line('Detalles del Vehículo:')
            ->line('Marca/Modelo: ' . $this->vehicle->brand . ' ' . $this->vehicle->model)
            ->line('Patente: ' . $this->vehicle->plate);

        if ($this->daysRemaining <= 0) {
            $mail->error(); // Muestra el botón en rojo si es crítico
        }

        return $mail->action('Gestionar Documentos', route('vehicles.index')) // Asumiendo que dirige al index donde se gestionan
            ->salutation('Atte, Administración de Flota')
            ->line('Por favor regularice esta situación a la brevedad.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $typeLabels = [
            'insurance' => 'Seguro',
            'permit' => 'Permiso de Circulación',
            'technical_review' => 'Revisión Técnica',
        ];

        $docName = $typeLabels[$this->document->type] ?? 'Documento';
        $status = $this->daysRemaining <= 0 ? 'VENCIDO' : 'por vencer';
        $message = "{$docName} {$status}";

        if ($this->daysRemaining > 0) {
            $message .= " en {$this->daysRemaining} días";
        }

        return [
            'message' => $message,
            'vehicle_id' => $this->vehicle->id,
            'plate' => $this->vehicle->plate,
            'brand_model' => "{$this->vehicle->brand} {$this->vehicle->model}",
            'type' => $this->daysRemaining <= 0 ? 'danger' : 'warning',
            'document_type' => $this->document->type,
        ];
    }
}
