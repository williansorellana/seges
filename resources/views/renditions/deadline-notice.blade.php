@php
    $due = app(\App\Services\PlanningEligibilityService::class)->effectiveDeadline($item);
    $editable = in_array($item->status, ['draft', 'rejected'], true);
    $expired = $due && now()->startOfDay()->gt($due);
@endphp
<div class="my-3 p-3 rounded-lg border border-slate-600 text-sm text-slate-200">
    @if($due)
        <p>{{ $item->status === 'rejected' ? 'Plazo para corregir' : 'Plazo original para rendir' }}: {{ $due->format('d/m/Y') }}.</p>
        @if($item->correction_deadline_at)<p>Vencimiento original: {{ $item->deadline_at?->format('d/m/Y') ?? 'No registrado' }}.</p>@endif
        @if($editable)
            @if($expired)
                <p class="text-amber-300">Fuera de plazo. Puedes completar y enviar esta rendición para regularizarla. Las nuevas planificaciones quedan bloqueadas mientras tengas rendiciones vencidas pendientes.</p>
            @else
                <p>Quedan {{ app(\App\Services\RenditionDeadlineService::class)->remainingBusinessDays($due) }} días hábiles después de hoy. {{ now()->isSameDay($due) ? 'El plazo vence hoy.' : '' }}</p>
            @endif
        @endif
    @endif
    @if($editable)
        <p>Borrador de gastos del viaje: cada gasto se guarda al agregarlo. Puedes subir fotos y corregirlas antes del envío oficial.</p>
        @if(!$item->expenses()->exists())<p class="text-amber-300">Pendiente: agregar al menos un comprobante válido.</p>@endif
        @if($item->expenses()->where('is_valid', false)->exists())<p class="text-amber-300">Pendiente: corregir los comprobantes observados.</p>@endif
        @if($item->expenses()->where('lodging_excess_status', 'pending')->exists())<p class="text-amber-300">Pendiente: autorización de excedentes de alojamiento.</p>@endif
    @endif
    @if($item->first_submitted_at)
        <p>Primer envío: {{ $item->first_submitted_at->format('d/m/Y H:i') }}.
            {{ $item->deadline_at && $item->first_submitted_at->copy()->startOfDay()->gt($item->deadline_at) ? 'Enviado fuera del plazo original.' : '' }}</p>
        <p>Último envío: {{ $item->last_submitted_at?->format('d/m/Y H:i') }}.</p>
    @endif
</div>
