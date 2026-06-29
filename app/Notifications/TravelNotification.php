<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\RoutePlanning;
use Carbon\Carbon;

class TravelNotification extends Notification
{
    use Queueable;

    public $planning;

    public function __construct(RoutePlanning $planning)
    {
        $this->planning = $planning;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $startDate = Carbon::parse($this->planning->start_date)->format('d/m/Y');
        $endDate = Carbon::parse($this->planning->end_date)->format('d/m/Y');
        
        $destinations = $this->planning->destination;
        if (!empty($this->planning->destinations)) {
            $extras = collect($this->planning->destinations)->pluck('destination')->filter()->implode(', ');
            if ($extras) {
                $destinations .= ', ' . $extras;
            }
        }

        $tipo = $this->planning->trip_type === 'terreno' ? 'Trabajo en Terreno' : 'Reunión de Negocios';
        $horaSalida = $this->planning->amipass_start_time ? ' a las ' . $this->planning->amipass_start_time : '';
        $horaRegreso = $this->planning->amipass_end_time ? ' a las ' . $this->planning->amipass_end_time : '';

        return (new MailMessage)
            ->subject('Notificación de Viaje a Terreno - ' . $this->planning->user->name . ' ' . $this->planning->user->last_name)
            ->greeting('Hola,')
            ->line('Se informa sobre una nueva actividad fuera de la oficina para efectos administrativos / anexos de contrato:')
            ->line('**Trabajador:** ' . $this->planning->user->name . ' ' . $this->planning->user->last_name)
            ->line('**Tipo de Actividad:** ' . $tipo)
            ->line('**Destino(s):** ' . $destinations)
            ->line('**Fecha y Hora de Salida:** ' . $startDate . $horaSalida)
            ->line('**Fecha y Hora de Retorno:** ' . $endDate . $horaRegreso)
            ->line('**Motivo / Labor a realizar:** ' . $this->planning->motive)
            ->line('**Acompañantes:** ' . ($this->planning->companions ?: 'Ninguno registrado'))
            ->line('Este es un correo automático generado por el sistema SEGES.')
            ->salutation(' '); 
    }
}