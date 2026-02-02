<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Asset;

class AssetConditionNotification extends Notification
{
    use Queueable;

    protected $asset;
    protected $condition;
    protected $notes;

    /**
     * Create a new notification instance.
     */
    public function __construct(Asset $asset, $condition, $notes = null)
    {
        $this->asset = $asset;
        $this->condition = $condition;
        $this->notes = $notes;
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
        $conditionLabels = [
            'good' => 'bueno',
            'fair' => 'regular',
            'poor' => 'malo',
            'damaged' => 'dañado',
        ];
        $translatedCondition = $conditionLabels[$this->condition] ?? $this->condition;

        return [
            'title' => 'Activo en Mal Estado',
            'message' => "El activo {$this->asset->nombre} ({$this->asset->codigo_interno}) ha sido reportado como: {$translatedCondition}. " . ($this->notes ? "Notas: {$this->notes}" : ""),
            'asset_id' => $this->asset->id,
            'asset_code' => $this->asset->codigo_interno,
            'icon' => 'tool',
            // 'action_url' removed
            'color' => 'orange'
        ];
    }
}
