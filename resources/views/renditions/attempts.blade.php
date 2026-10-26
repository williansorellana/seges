<div class="my-4 p-4 border border-slate-600 rounded-xl text-slate-200 space-y-2">
    <p class="font-bold">Rendición #{{ $rendition->id }} / intento {{ $rendition->attempt_number }}</p>
    @if($rendition->status === 'rejected')
        <p class="text-rose-300">Intento rechazado y cerrado el {{ $rendition->closed_at?->format('d/m/Y H:i') }}. No se pueden modificar sus datos ni comprobantes.</p>
        <p>Motivo: {{ $rendition->rejection_reason }}</p>
        @if(auth()->id() === $rendition->user_id && !$rendition->replacement)
            <form method="POST" action="{{ route('renditions.new-attempt', $rendition) }}">@csrf<button class="bg-blue-600 px-4 py-2 rounded">Crear nueva rendición del mismo viaje</button></form>
            <p class="text-sm">El nuevo intento comienza vacío y conserva los fondos y el vencimiento originales. No genera otro anticipo. Debes registrar nuevamente los comprobantes.</p>
        @endif
    @endif
    <p>Intentos del viaje:</p>
    @foreach($rendition->routePlanning->renditions->sortBy('attempt_number') as $attempt)
        <a class="block text-blue-300 underline" href="{{ route('renditions.show', $attempt) }}">Intento {{ $attempt->attempt_number }} / RND-{{ $attempt->id }} / {{ \App\Helpers\WorkflowHelper::statusLabel($attempt->status) }}</a>
    @endforeach
</div>
