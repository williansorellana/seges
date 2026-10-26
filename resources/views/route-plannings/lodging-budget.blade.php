@if($plan->funds_alojamiento > 0)
    <div class="mt-2 text-xs text-amber-300">
        <p>Alojamiento con IVA: ${{ number_format($plan->funds_alojamiento, 0, ',', '.') }} / {{ $plan->lodging_nights }} noche(s). Tope diario $41.650.</p>
        <p>Adicional: ${{ number_format($plan->lodging_excess_amount, 0, ',', '.') }} / {{ ['pending' => 'Pendiente de jefatura', 'approved' => 'Autorizado por jefatura', 'not_required' => 'No requiere', 'legacy' => 'Aprobación histórica conservada'][$plan->lodging_excess_status] ?? $plan->lodging_excess_status }}.</p>
        @if($plan->lodging_excess_status === 'pending' && auth()->user()->role === 'jefatura' && $plan->user->jefatura_id === auth()->id() && $plan->user_id !== auth()->id())
            <form method="POST" action="{{ route('route-plannings.authorize-lodging', $plan) }}">@csrf<button class="bg-amber-600 text-white p-2 rounded">Autorizar adicional con IVA</button></form>
        @endif
    </div>
@endif
