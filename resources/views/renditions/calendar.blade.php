<x-app-layout>
    <x-slot name="header"><h2 class="text-xl text-white">Calendario de días hábiles</h2></x-slot>
    <div class="max-w-5xl mx-auto p-6 text-slate-200 space-y-6">
        <p>Los plazos excluyen sábados, domingos y las fechas de este calendario. Los vencimientos ya asignados se conservan. Se precargaron los feriados nacionales de 2026 y 2027; agrega aquí días adicionales aplicables a la empresa.</p>
        <p>Años habilitados: {{ $years->implode(', ') }}. Antes de habilitar otro año, completa y revisa sus feriados.</p>
        @if(session('success'))<p class="text-emerald-400">{{ session('success') }}</p>@endif
        @foreach($errors->all() as $error)<p class="text-rose-400">{{ $error }}</p>@endforeach
        <form method="POST" action="{{ route('renditions.calendar.store') }}" class="flex flex-wrap gap-3">
            @csrf
            <label>Fecha <input class="text-slate-900" type="date" name="date" required></label>
            <label>Nombre <input class="text-slate-900" name="name" maxlength="255" required></label>
            <button class="bg-blue-600 rounded px-4 py-2">Guardar feriado</button>
        </form>
        <form method="POST" action="{{ route('renditions.calendar.confirm') }}" class="flex flex-wrap gap-3">
            @csrf
            <label>Año <input class="text-slate-900 w-28" type="number" name="year" min="2026" max="2100" required></label>
            <label><input type="checkbox" name="reviewed" value="1" required> Revisé todos los feriados del año</label>
            <button class="bg-blue-600 rounded px-4 py-2">Habilitar año</button>
        </form>
        <table class="w-full"><thead><tr><th class="text-left">Fecha</th><th class="text-left">Feriado</th><th></th></tr></thead>
            <tbody>@foreach($holidays as $holiday)<tr class="border-b border-slate-700">
                <td class="py-2">{{ $holiday->date }}</td><td>{{ $holiday->name }}</td>
                <td><form method="POST" action="{{ route('renditions.calendar.destroy', $holiday->id) }}">@csrf @method('DELETE')<button class="text-rose-400">Eliminar</button></form></td>
            </tr>@endforeach</tbody>
        </table>
        <a class="text-blue-400" href="{{ route('renditions.index') }}">Volver a Rendiciones</a>
    </div>
</x-app-layout>
