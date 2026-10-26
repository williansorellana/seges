<x-app-layout>
    <x-slot name="header"><h2 class="text-xl text-white">Centros de costo y asignaciones</h2></x-slot>
    <div class="max-w-7xl mx-auto p-6 text-slate-200 space-y-6">
        @if(session('success'))<p class="text-emerald-300">{{ session('success') }}</p>@endif
        @foreach($errors->all() as $error)<p class="text-rose-300">{{ $error }}</p>@endforeach
        <p>Catálogo inicial: 21 códigos distintos del Excel DIMAK. OPERACIONES se cargó una vez. PERSONAL Y RR.HH. está pendiente porque no se proporcionó su código. No se asignaron personas automáticamente.</p>
        <h3 class="text-lg font-bold">Agregar centro</h3>
        <form method="POST" action="{{ route('cost-centers.save') }}" class="flex flex-wrap gap-3">
            @csrf
            <label>Código <input class="text-slate-900" name="code" maxlength="30" required></label>
            <label>Área <input class="text-slate-900" name="area" maxlength="255" required></label>
            <input type="hidden" name="is_active" value="1">
            <button class="bg-blue-600 rounded px-4 py-2">Agregar</button>
        </form>
        <h3 class="text-lg font-bold">Catálogo</h3>
        @foreach($centers as $center)
            <form method="POST" action="{{ route('cost-centers.save') }}" class="flex flex-wrap items-end gap-3 border-b border-slate-700 py-2">
                @csrf <input type="hidden" name="id" value="{{ $center->id }}">
                <label>Código <input class="text-slate-900" name="code" value="{{ $center->code }}" maxlength="30" required></label>
                <label>Área <input class="text-slate-900" name="area" value="{{ $center->area }}" maxlength="255" required></label>
                <label>Estado <select class="text-slate-900" name="is_active"><option value="1" @selected($center->is_active)>Activo</option><option value="0" @selected(!$center->is_active)>Inactivo</option></select></label>
                <button class="bg-blue-600 rounded px-4 py-2">Guardar</button>
            </form>
        @endforeach
        <h3 class="text-lg font-bold">Asignar o cambiar centro de una persona</h3>
        <p>La persona debe existir en Usuarios y tener su RUT registrado. Las asignaciones se usan al crear nuevas planificaciones. Para agregar una persona nueva, Administración debe crearla en Usuarios.</p>
        @if(auth()->user()->role === 'admin')<a class="text-blue-300 underline" href="{{ route('users.index') }}">Administrar personas en Usuarios</a>@endif
        <form method="POST" action="{{ route('cost-centers.assign') }}" class="flex flex-wrap gap-3">
            @csrf
            <label>Persona <select class="text-slate-900" name="user_id" required><option value="">Seleccionar</option>@foreach($people as $person)<option value="{{ $person->id }}">{{ $person->name }} {{ $person->last_name }} / {{ $person->rut ?: 'Sin RUT' }}</option>@endforeach</select></label>
            <label>Centro <select class="text-slate-900" name="cost_center_id" required><option value="">Seleccionar</option>@foreach($centers as $center)<option value="{{ $center->id }}">{{ $center->code }} / {{ $center->area }}{{ !$center->is_active ? ' (inactivo)' : '' }}</option>@endforeach</select></label>
            <label>Estado <select class="text-slate-900" name="is_active"><option value="1">Activa</option><option value="0">Inactiva</option></select></label>
            <button class="bg-blue-600 rounded px-4 py-2">Guardar asignación</button>
        </form>
        <table class="w-full text-left"><thead><tr><th>Persona</th><th>RUT</th><th>Centro</th><th>Estado</th></tr></thead><tbody>
            @foreach($assignments as $mapping)<tr class="border-b border-slate-700"><td class="py-2">{{ $mapping->user?->name ?? 'Asignación antigua sin usuario vinculado' }} {{ $mapping->user?->last_name }}</td><td>{{ $mapping->rut }}</td><td>{{ $mapping->center?->code ?? $mapping->cost_center }} / {{ $mapping->center?->area ?? $mapping->description }}</td><td>{{ $mapping->is_active && ($mapping->center?->is_active ?? false) ? 'Activa' : 'Inactiva' }}</td></tr>@endforeach
        </tbody></table>
    </div>
</x-app-layout>
