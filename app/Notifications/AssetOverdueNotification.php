<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\AssetAssignment;

class AssetOverdueNotification extends Notification
{
    use Queueable;

    protected $assignment;

    /**
     * Create a new notification instance.
     */
    public function __construct(AssetAssignment $assignment)
    {
        $this->assignment = $assignment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $daysOverdue = (int) abs(now()->diffInDays($this->assignment->fecha_estimada_devolucion));
        // Asegurar número entero positivo
        $assignedTo = $this->assignment->user
            ? $this->assignment->user->name
            : ($this->assignment->worker ? $this->assignment->worker->nombre : 'Desconocido');

        return [
            'title' => 'Activo Atrasado',
            'message' => "El activo {$this->assignment->asset->nombre} ({$this->assignment->asset->codigo_interno}) asignado a {$assignedTo} tiene {$daysOverdue} días de atraso.",
            'asset_id' => $this->assignment->activo_id,
            'asset_code' => $this->assignment->asset->codigo_interno,
            'icon' => 'exclamation-triangle', // Keep icon
            // 'action_url' removed to force controller handling
            'color' => 'red'
        ];
    }
}
