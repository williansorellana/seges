<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Historial del Vehículo</title>
    <style>
        @page { margin: 100px 50px 80px 50px; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #333;
        }
        header { position: fixed; top: -100px; left: 0; right: 0; height: 90px; border-bottom: 2px solid #CA8A04; padding-bottom: 10px; } /* Yellow-600 */
        .header-content { width: 100%; display: table; }
        .logo-section { display: table-cell; width: 120px; vertical-align: top; padding-top: 5px; }
        .title-section { display: table-cell; vertical-align: top; padding-left: 15px; }
        h1 { margin: 0 0 5px 0; color: #111827; font-size: 16pt; font-weight: bold; }
        .subtitle { font-size: 10pt; color: #4B5563; margin-bottom: 2px; }
        .meta-date { color: #6B7280; font-size: 8pt; margin: 0; }
        
        .filters-section { background-color: #FEF3C7; border-left: 4px solid #F59E0B; padding: 8px 12px; margin: 15px 0; font-size: 8pt; }
        .filters-title { font-weight: bold; color: #92400E; margin-bottom: 3px; }
        .filters-list { color: #78350F; margin: 0; padding-left: 15px; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #E5E7EB; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background-color: #1F2937; color: #ffffff; font-weight: bold; font-size: 8pt; text-transform: uppercase; }
        tbody tr:nth-child(even) { background-color: #F9FAFB; }
        
        .badge { padding: 2px 6px; border-radius: 4px; font-size: 7pt; font-weight: bold; display: inline-block; color: white; }
        .bg-green { background-color: #059669; }
        .bg-yellow { background-color: #D97706; }
        .bg-blue { background-color: #2563EB; }
        .bg-red { background-color: #DC2626; }
        .bg-gray { background-color: #4B5563; }

        footer { position: fixed; bottom: -60px; left: 0; right: 0; height: 50px; border-top: 1px solid #E5E7EB; padding-top: 10px; font-size: 8pt; color: #6B7280; }
        .page-number:before { content: "Página " counter(page); }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo-section">
                @php
                    $logoPath = public_path('images/dimak-logo.png');
                    $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
                @endphp
                @if($logoData)
                    <img src="data:image/png;base64,{{ $logoData }}" alt="Dimak Logo"
                        style="max-width: 100px; max-height: 60px; object-fit: contain;">
                @endif
            </div>
            <div class="title-section">
                <h1>Historial del Vehículo</h1>
                <div class="subtitle">
                    <strong>{{ $vehicle->brand }} {{ $vehicle->model }}</strong> - Patente: <strong>{{ $vehicle->plate }}</strong>
                </div>
                <div class="subtitle">Año: {{ $vehicle->year }} | Km Actual: {{ number_format($vehicle->mileage, 0, '', '.') }}</div>
                <p class="meta-date">Generado el {{ $generatedDate }}</p>
            </div>
        </div>
    </header>

    <footer>
        <div style="width: 100%; display: table;">
            <div style="display: table-cell; text-align: left;">Sistema de Gestión - Dimak</div>
            <div style="display: table-cell; text-align: right;"><span class="page-number"></span></div>
        </div>
    </footer>

    <main>
        <h3>
            @if($tab === 'maintenance') Historial de Mantenimiento
            @elseif($tab === 'usage') Historial de Uso
            @elseif($tab === 'returns') Historial de Devoluciones
            @endif
        </h3>

        @if(count($filters) > 0)
            <div class="filters-section">
                <div class="filters-title">Filtros Aplicados:</div>
                <ul class="filters-list">
                    @foreach($filters as $filtro) <li>{{ $filtro }}</li> @endforeach
                </ul>
            </div>
        @endif

        @if($tab === 'maintenance')
            <table>
                <thead>
                    <tr>
                        <th style="width: 15%">Fecha</th>
                        <th style="width: 15%">Tipo</th>
                        <th style="width: 40%">Descripción</th>
                        <th style="width: 15%">Estado</th>
                        <th style="width: 15%">Completado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        <tr>
                            <td>{{ $req->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @switch($req->type)
                                    @case('oil') Aceite @break
                                    @case('tires') Neumáticos @break
                                    @case('mechanics') Mecánica @break
                                    @default General
                                @endswitch
                            </td>
                            <td>{{ $req->description }}</td>
                            <td>
                                @switch($req->status)
                                    @case('pending') <span class="badge bg-yellow">Pendiente</span> @break
                                    @case('in_progress') <span class="badge bg-blue">En Taller</span> @break
                                    @case('completed') <span class="badge bg-green">Finalizado</span> @break
                                @endswitch
                            </td>
                            <td>{{ $req->status === 'completed' ? $req->updated_at->format('d/m/Y H:i') : '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align: center; padding: 20px;">No hay registros.</td></tr>
                    @endforelse
                </tbody>
            </table>

        @elseif($tab === 'usage')
            <table>
                <thead>
                    <tr>
                        <th style="width: 25%">Solicitado Por</th>
                        <th style="width: 15%">Inicio</th>
                        <th style="width: 15%">Término</th>
                        <th style="width: 18%">Origen</th>
                        <th style="width: 25%">Destino</th>
                        <th style="width: 20%">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usageHistory as $usage)
                        <tr>
                            <td>
                                {{ $usage->conductor ? $usage->conductor->nombre . ' (C)' : ($usage->user ? $usage->user->name : 'N/A') }}
                            </td>
                            <td>{{ $usage->start_date ? $usage->start_date->format('d/m/Y H:i') : '-' }}</td>
                            <td>{{ $usage->end_date ? $usage->end_date->format('d/m/Y H:i') : '-' }}</td>
                            <td>{{ $usage->origin ?? '-' }}</td>
                            <td>
                                @if($usage->destination)
                                    {{ $usage->destination }}
                                @else
                                    {{ $usage->destination_type === 'outside' ? 'Fuera de la ciudad' : 'Local' }}
                                @endif
                            </td>
                            <td>
                                @switch($usage->status)
                                    @case('approved') <span class="badge bg-blue">En Curso</span> @break
                                    @case('completed') <span class="badge bg-green">Finalizado</span> @break
                                    @case('pending') <span class="badge bg-yellow">Pendiente</span> @break
                                    @case('rejected') <span class="badge bg-red">Rechazado</span> @break
                                    @case('cancelled') <span class="badge bg-gray">Cancelado</span> @break
                                    @default {{ ucfirst($usage->status) }}
                                @endswitch
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align: center; padding: 20px;">No hay registros.</td></tr>
                    @endforelse
                </tbody>
            </table>

        @elseif($tab === 'returns')
            <table>
                <thead>
                    <tr>
                        <th style="width: 15%">Fecha</th>
                        <th style="width: 25%">Responsable</th>
                        <th style="width: 20%">Estado</th>
                        <th style="width: 10%">Km</th>
                        <th style="width: 30%">Observaciones / Daños</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $usage)
                        <tr>
                            <td>{{ $usage->vehicleReturn->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                {{ $usage->conductor ? $usage->conductor->nombre : ($usage->user ? $usage->user->name : 'N/A') }}
                            </td>
                            <td>
                                @if($usage->vehicleReturn->body_damage_reported)
                                    <span class="badge bg-red">⚠ Con Daños</span>
                                @else
                                    <span class="badge bg-green">✓ Sin Daños</span>
                                @endif
                                <br>
                                <span style="font-size: 8pt; color: #666;">Comb: {{ $usage->vehicleReturn->fuel_level }}%</span>
                            </td>
                            <td>{{ number_format($usage->vehicleReturn->return_mileage, 0, '', '.') }}</td>
                            <td>
                                {{ $usage->vehicleReturn->comments ?: 'Sin observaciones' }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align: center; padding: 20px;">No hay registros de devoluciones.</td></tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    </main>
</body>
</html>
