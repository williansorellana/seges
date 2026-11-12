<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-bold text-white">Controlling · Revisión de rendiciones</h2></x-slot>
    <div class="max-w-7xl mx-auto p-6 text-slate-200 space-y-6">
        <p>Revisa las boletas, facturas y demás comprobantes de las rendiciones aprobadas por Jefatura. Al aprobar, la rendición pasa a Finanzas.</p>
        <p class="text-sm text-slate-400">Las planificaciones de viaje pasan directamente de Jefatura a Finanzas y no requieren revisión de Controlling.</p>
        @if(session('success'))<p role="status" class="text-emerald-300">{{ session('success') }}</p>@endif
        @foreach($errors->all() as $error)<p role="alert" class="text-rose-300">{{ $error }}</p>@endforeach
        <div class="overflow-x-auto rounded-xl border border-slate-700 bg-slate-800">
            <table class="w-full text-left">
                <thead><tr class="border-b border-slate-700">
                    <th class="p-4">Rendición / trabajador</th><th class="p-4">Viaje</th>
                    <th class="p-4">Gastos y comprobantes</th><th class="p-4">Acciones</th>
                </tr></thead>
                <tbody>
                    @forelse($renditions as $ren)
                        <tr class="border-b border-slate-700 align-top">
                            <td class="p-4"><p class="font-bold">RND-{{ str_pad($ren->id, 4, '0', STR_PAD_LEFT) }} / intento {{ $ren->attempt_number }}</p><p>{{ $ren->user->name }} {{ $ren->user->last_name }}</p></td>
                            <td class="p-4"><p>{{ $ren->routePlanning->destination }}</p><p class="text-sm text-slate-400">{{ $ren->routePlanning->start_date->format('d/m/Y') }} al {{ $ren->routePlanning->end_date->format('d/m/Y') }}</p></td>
                            <td class="p-4"><p>Rendido: ${{ number_format($ren->expenses_sum_amount ?? 0, 0, ',', '.') }}</p><p>{{ $ren->expenses_count }} comprobante(s)</p>
                                @if($ren->observed_expenses_count > 0)<p class="text-rose-300">{{ $ren->observed_expenses_count }} documento(s) observado(s). Debes resolver las observaciones o rechazar la rendición.</p>@endif
                            </td>
                            <td class="p-4 space-y-3">
                                <a class="inline-block rounded-lg bg-blue-600 px-4 py-2 text-white" href="{{ route('renditions.show', $ren) }}">Revisar comprobantes</a>
                                @if($ren->user_id !== auth()->id())
                                    <form method="POST" action="{{ route('renditions.approve-controlling-rendition', $ren) }}">@csrf
                                        <button type="submit" @disabled($ren->observed_expenses_count > 0) class="rounded-lg bg-emerald-700 px-4 py-2 text-white disabled:opacity-50">Aprobar y enviar a Finanzas</button>
                                    </form>
                                    <details>
                                        <summary class="cursor-pointer text-rose-300">Rechazar rendición</summary>
                                        <form method="POST" action="{{ route('renditions.reject-controlling-rendition', $ren) }}" class="mt-3 space-y-3">@csrf
                                            <label class="block">Motivo del rechazo
                                                <textarea name="observation" required maxlength="1000" rows="3" class="block w-full rounded-lg border border-slate-600 bg-slate-900 p-3 text-white"></textarea>
                                            </label>
                                            <button type="submit" class="rounded-lg bg-rose-700 px-4 py-2 text-white">Confirmar rechazo</button>
                                        </form>
                                    </details>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-8 text-center">No hay rendiciones pendientes de revisión en Controlling.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $renditions->links() }}
    </div>
</x-app-layout>
