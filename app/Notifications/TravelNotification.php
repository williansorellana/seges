<?php

namespace App\Notifications;

use App\Models\RoutePlanning;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TravelNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $planning;

    /**
     * Create a new notification instance.
     */
    public function __construct(RoutePlanning $planning)
    {
        $this->planning = $planning;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $workerName = $this->planning->user ? $this->planning->user->name . ' ' . $this->planning->user->last_name : 'Colaborador';
        $workerRut = $this->planning->user ? $this->planning->user->rut : 'N/A';
        $workerEmail = $this->planning->user ? $this->planning->user->email : 'N/A';

        $destinationsText = $this->planning->destination;
        if (!empty($this->planning->destinations)) {
            $destinationsList = [];
            foreach ($this->planning->destinations as $dest) {
                if (!empty($dest['destination'])) {
                    $destinationsList[] = $dest['destination'] . (!empty($dest['region']) ? ' (' . $dest['region'] . ')' : '');
                }
            }
            if (!empty($destinationsList)) {
                $destinationsText .= ' y destinos adicionales: ' . implode(', ', $destinationsList);
            }
        }

        $startDate = $this->planning->start_date ? $this->planning->start_date->format('d/m/Y') : 'N/A';
        $endDate = $this->planning->end_date ? $this->planning->end_date->format('d/m/Y') : 'N/A';

        $mailMessage = (new MailMessage)
            ->subject('Notificación de Viaje a Terreno - ' . $workerName)
            ->greeting('Estimado/a,')
            ->line('Se ha registrado una nueva planificación de viaje/actividad de terreno en el sistema Seges y se solicita notificar a este correo para fines administrativos (por ejemplo, gestión de anexo de contrato).')
            ->line('A continuación, se detallan los datos básicos del viaje:')
            ->line('**Colaborador:** ' . $workerName . ' (RUT: ' . $workerRut . ', Correo: ' . $workerEmail . ')')
            ->line('**Tipo de Actividad:** ' . ucfirst($this->planning->trip_type))
            ->line('**Motivo / Actividad:** ' . $this->planning->motive)
            ->line('**Destino(s):** ' . $destinationsText)
            ->line('**Fecha Inicio:** ' . $startDate)
            ->line('**Fecha Término:** ' . $endDate)
            ->line('**Acompañantes:** ' . ($this->planning->companions ?: 'Ninguno'));

        if ($this->planning->requires_funds) {
            $mailMessage->line('**Fondos Solicitados:** $' . number_format($this->planning->requested_funds, 0, ',', '.'));
        }

        return $mailMessage
            ->line('Por favor, realice las gestiones administrativas correspondientes.')
            ->salutation('Atentamente, Sistema Seges.');
    }
}
