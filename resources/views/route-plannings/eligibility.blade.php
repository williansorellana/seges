<div class="mb-6 p-5 rounded-xl border border-slate-700 bg-slate-800 text-slate-200 space-y-3">
    <p>Planificaciones creadas hoy: <strong>{{ $dailyCount }}/2</strong>. Una tercera requiere autorización de tu jefatura; el máximo absoluto es tres.</p>
    <p>Puedes guardar comprobantes durante el viaje y enviar la rendición desde el día de término. El plazo es de cinco días hábiles, excluyendo feriados. Los rechazos dan dos días hábiles para corregir.</p>
    @if($overdueRenditions->isNotEmpty())
        <p class="text-amber-300">Tienes rendiciones fuera de plazo. Envía todas las pendientes para habilitar nuevas planificaciones; no necesitas esperar su aprobación.</p>
        @foreach($overdueRenditions as $pending)
            <a class="block text-blue-300 underline" href="{{ route('renditions.show', $pending) }}">Regularizar rendición #{{ $pending->id }} — {{ $pending->routePlanning?->destination }}</a>
        @endforeach
    @endif
    @if($overdueRenditions->isNotEmpty() || $dailyCount === 2)
        <form method="POST" action="{{ route('route-plannings.exceptions.store') }}" class="space-y-2">
            @csrf
            <input type="hidden" name="type" value="{{ $overdueRenditions->isNotEmpty() ? 'temporary_unlock' : 'daily_extra' }}">
            <label class="block">Motivo de la excepción para hoy
                <textarea class="w-full bg-slate-900 text-white" name="reason" minlength="10" maxlength="1000" required></textarea>
            </label>
            <button class="bg-blue-600 px-4 py-2 rounded">{{ $overdueRenditions->isNotEmpty() ? 'Solicitar desbloqueo excepcional' : 'Solicitar tercera planificación' }}</button>
            <p class="text-sm">Cada autorización permite una planificación y vence al terminar el día. Un desbloqueo no elimina la obligación de rendir.</p>
        </form>
    @endif
    @foreach($exceptions as $exception)
        <div class="border-t border-slate-600 pt-3">
            <p>{{ $exception->user->name }}: {{ $exception->type === 'daily_extra' ? 'Tercera planificación' : 'Desbloqueo excepcional' }} — {{ ['pending' => 'Pendiente', 'approved' => 'Autorizada', 'rejected' => 'Rechazada'][$exception->status] }}{{ $exception->used_at ? ' (utilizada)' : '' }}</p>
            <p>Solicitud: {{ $exception->request_reason }}</p>
            @if($exception->decision_reason)<p>Decisión: {{ $exception->decision_reason }} — {{ $exception->decider?->name }}</p>@endif
            @if($exception->status === 'pending' && auth()->user()->role === 'jefatura' && $exception->user_id !== auth()->id())
                <form method="POST" action="{{ route('route-plannings.exceptions.decide', $exception) }}" class="space-y-2">
                    @csrf
                    <label>Motivo de la decisión<textarea class="w-full bg-slate-900 text-white" name="reason" minlength="10" maxlength="1000" required></textarea></label>
                    <button class="bg-emerald-700 px-4 py-2 rounded" name="decision" value="approved">Autorizar</button>
                    <button class="bg-rose-700 px-4 py-2 rounded" name="decision" value="rejected">Rechazar</button>
                </form>
            @endif
        </div>
    @endforeach
</div>
