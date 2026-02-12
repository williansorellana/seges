<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Historial de Uso - {{ $recipient->name ?? $recipient->nombre }}</title>
    <style>
        @page { margin: 100px 50px 80px 50px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10pt; color: #333; }
        header { position: fixed; top: -100px; left: 0; right: 0; height: 90px; border-bottom: 2px solid #2563EB; padding-bottom: 10px; }
        .header-content { width: 100%; display: table; }
        .logo-section { display: table-cell; width: 120px; vertical-align: top; padding-top: 5px; }
        .title-section { display: table-cell; vertical-align: top; padding-left: 15px; }
        h1 { margin: 0 0 5px 0; color: #111827; font-size: 16pt; font-weight: bold; }
        .subtitle { font-size: 10pt; color: #4B5563; margin-bottom: 2px; }
        .meta-date { color: #6B7280; font-size: 8pt; margin: 0; }
        .filters-section { background-color: #F1F5F9; border-left: 4px solid #2563EB; padding: 8px 12px; margin: 15px 0; font-size: 8pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #E5E7EB; padding: 6px 8px; text-align: left; vertical-align: top; font-size: 8pt; }
        th { background-color: #1F2937; color: #ffffff; font-weight: bold; text-transform: uppercase; }
        .badge { padding: 2px 6px; border-radius: 4px; font-size: 7pt; font-weight: bold; display: inline-block; color: white; }
        .bg-green { background-color: #059669; }
        .bg-blue { background-color: #2563EB; }
        .bg-indigo { background-color: #4F46E5; }
        .bg-yellow { background-color: #D97706; }
        .bg-red { background-color: #DC2626; }
        .bg-gray { background-color: #6B7280; }
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
                    <img src="data:image/png;base64,{{ $logoData }}" style="max-width: 100px; max-height: 60px;">
                @endif
            </div>
            <div class="title-section">
                <h1>Historial de Uso de Vehículos</h1>
                <div class="subtitle"><strong>Usuario:</strong> {{ $recipient->name ?? $recipient->nombre }} {{ $recipient->last_name ?? '' }}</div>
                <div class="subtitle"><strong>RUT:</strong> {{ $recipient->rut }}</div>
                <p class="meta-date">Generado el {{ $generatedDate }}</p>
            </div>
        </div>
    </header>

    <footer>
        <div style="width: 100%; display: table;">
            <div style="display: table-cell; text-align: left;">Sistema Dimak - Control de Flota</div>
            <div style="display: table-cell; text-align: right;"><span class="page-number"></span></div>
        </div>
    </footer>

    <main>
        @if(count($filters) > 0)
            <div class="filters-section">
                <strong>Filtros Aplicados:</strong> {{ implode(' | ', $filters) }}
            </div>
        @endif

        <table>
            <thead>
                <tr>
                    <th style="width: 20%">Vehículo</th>
                    <th style="width: 15%">Desde</th>
                    <th style="width: 15%">Hasta</th>
                    <th style="width: 18%">Origen</th>
                    <th style="width: 18%">Destino / Uso</th>
                    <th style="width: 14%">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usageHistory as $usage)
                    <tr>
                        <td>
                            <strong>{{ $usage->vehicle->brand }} {{ $usage->vehicle->model }}</strong><br>
                            <small>{{ $usage->vehicle->plate }}</small>
                        </td>
                        <td>{{ $usage->start_date->format('d/m/Y H:i') }}</td>
                        <td>{{ $usage->end_date->format('d/m/Y H:i') }}</td>
                        <td>{{ $usage->origin ?? '-' }}</td>
                        <td>
                            @if($usage->destination)
                                {{ $usage->destination }}
                            @else
                                {{ $usage->destination_type === 'outside' ? 'Fuera de la ciudad' : 'Local' }}
                            @endif
                        </td>
                        <td>
                            @php
                                $pdfStatus = match($usage->status) {
                                    'completed' => ['l' => 'Finalizado', 'c' => 'bg-green'],
                                    'approved' => ['l' => 'Aprobado', 'c' => 'bg-blue'],
                                    'in_trip' => ['l' => 'En Viaje', 'c' => 'bg-indigo'],
                                    'pending' => ['l' => 'Pendiente', 'c' => 'bg-yellow'],
                                    'rejected' => ['l' => 'Rechazado', 'c' => 'bg-red'],
                                    'cancelled' => ['l' => 'Cancelado', 'c' => 'bg-gray'],
                                    default => ['l' => $usage->status, 'c' => 'bg-gray']
                                };
                            @endphp
                            <span class="badge {{ $pdfStatus['c'] }}">{{ strtoupper($pdfStatus['l']) }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</body>
</html>