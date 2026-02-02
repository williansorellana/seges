<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Historial de Asignaciones</title>
    <style>
        @page {
            margin: 100px 50px 80px 50px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            color: #333;
        }

        /* Header fijo en todas las páginas */
        header {
            position: fixed;
            top: -100px;
            left: 0;
            right: 0;
            height: 90px;
            border-bottom: 3px solid: 2563eb;
            padding-bottom: 10px;
        }

        .header-content {
            display: table;
            width: 100%;
        }

        .logo-section {
            display: table-cell;
            width: 120px;
            vertical-align: top;
            padding-top: 10px;
        }

        .title-section {
            display: table-cell;
            vertical-align: top;
            padding-left: 15px;
        }

        h1 {
            margin: 0 0 5px 0;
            color: #1e293b;
            font-size: 16pt;
            font-weight: bold;
        }

        .meta-date {
            color: #64748b;
            font-size: 8pt;
            margin: 0;
        }

        .asset-info {
            color: #475569;
            font-size: 9pt;
            margin-top: 3px;
        }

        /* Sección de estadísticas */
        .stats-section {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 10px 15px;
            margin: 15px 0;
        }

        .stats-grid {
            display: table;
            width: 100%;
        }

        .stat-item {
            display: table-cell;
            text-align: center;
            padding: 5px 10px;
            border-right: 1px solid #cbd5e1;
        }

        .stat-item:last-child {
            border-right: none;
        }

        .stat-label {
            font-size: 7pt;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .stat-value {
            font-size: 13pt;
            font-weight: bold;
            color: #1e293b;
        }

        .stat-value.total {
            color: #2563eb;
        }

        .stat-value.activas {
            color: #3b82f6;
        }

        .stat-value.devueltas {
            color: #64748b;
        }

        .stat-value.bueno {
            color: #10b981;
        }

        .stat-value.regular {
            color: #f59e0b;
        }

        .stat-value.malo {
            color: #ef4444;
        }

        /* Filtros aplicados */
        .filters-section {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 8px 12px;
            margin: 10px 0;
            font-size: 8pt;
        }

        .filters-title {
            font-weight: bold;
            color: #92400e;
            margin-bottom: 3px;
        }

        .filters-list {
            color: #78350f;
            margin: 0;
            padding-left: 15px;
        }

        /* Tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #1e293b;
            color: #ffffff;
            font-weight: bold;
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        /* Badges de estado */
        .status-badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 7pt;
            font-weight: bold;
            color: white;
            display: inline-block;
        }

        .text-green {
            color: #15803d;
            font-weight: bold;
        }

        .text-yellow {
            color: #a16207;
            font-weight: bold;
        }

        .text-orange {
            color: #c2410c;
            font-weight: bold;
        }

        .text-red {
            color: #b91c1c;
            font-weight: bold;
        }

        .text-blue {
            color: #2563eb;
            font-weight: bold;
        }

        .text-gray {
            color: #64748b;
        }

        .meta-info {
            font-size: 7pt;
            color: #64748b;
            line-height: 1.3;
        }

        /* Footer fijo en todas las páginas */
        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 50px;
            border-top: 2px solid #e2e8f0;
            padding-top: 10px;
            font-size: 8pt;
            color: #64748b;
        }

        .footer-content {
            display: table;
            width: 100%;
        }

        .footer-left {
            display: table-cell;
            width: 50%;
            text-align: left;
        }

        .footer-right {
            display: table-cell;
            width: 50%;
            text-align: right;
        }

        .page-number:before {
            content: "Página " counter(page);
        }

        main {
            margin-top: 0;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #94a3b8;
            font-style: italic;
        }
    </style>
</head>

<body>
    <!-- Header -->
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
                <h1>Historial de Asignaciones</h1>
                <div class="asset-info">
                    <strong>{{ $asset->nombre }}</strong> ({{ $asset->codigo_interno }})
                </div>
                <p class="meta-date">Generado el {{ $generatedDate }}</p>
            </div>
        </div>
    </header>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-left">
                Sistema de Gestión de Activos - Historial de Asignaciones
            </div>
            <div class="footer-right">
                <span class="page-number"></span>
            </div>
        </div>
    </footer>

    <!-- Contenido Principal -->
    <main>
        <!-- Estadísticas -->
        <div class="stats-section">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-label">Total</div>
                    <div class="stat-value total">{{ $totalAsignaciones }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Activas</div>
                    <div class="stat-value activas">{{ $totalActivas }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Devueltas</div>
                    <div class="stat-value devueltas">{{ $totalDevueltas }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Bueno</div>
                    <div class="stat-value bueno">{{ $totalBueno }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Regular</div>
                    <div class="stat-value regular">{{ $totalRegular }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Malo</div>
                    <div class="stat-value malo">{{ $totalMalo }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Dañado</div>
                    <div class="stat-value malo">{{ $totalDanado }}</div>
                </div>
            </div>
        </div>

        <!-- Filtros Aplicados -->
        @if(count($filtrosAplicados) > 0)
            <div class="filters-section">
                <div class="filters-title">⚡ Filtros Aplicados:</div>
                <ul class="filters-list">
                    @foreach($filtrosAplicados as $filtro)
                        <li>{{ $filtro }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Tabla -->
        <table>
            <thead>
                <tr>
                    <th style="width: 18%">Asignado A</th>
                    <th style="width: 12%">Fecha Entrega</th>
                    <th style="width: 14%">Fecha Devolución</th>
                    <th style="width: 12%">Estado</th>
                    <th style="width: 26%">Comentarios / Incidentes</th>
                    <th style="width: 18%">Responsable</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assignments as $assignment)
                    <tr>
                        <td>
                            @if($assignment->user)
                                <strong>{{ $assignment->user->name }}</strong><br>
                                <span class="meta-info">{{ $assignment->user->rut }}</span>
                            @elseif($assignment->worker)
                                <strong>{{ $assignment->worker->nombre }}</strong><br>
                                <span class="meta-info">{{ $assignment->worker->rut }} (Externo)</span>
                            @else
                                <strong>{{ $assignment->trabajador_nombre ?? 'N/A' }}</strong><br>
                                <span class="meta-info">{{ $assignment->trabajador_rut }}</span>
                            @endif
                        </td>
                        <td>
                            {{ $assignment->fecha_entrega ? $assignment->fecha_entrega->format('d/m/Y') : '-' }}
                        </td>
                        <td>
                            {{ $assignment->fecha_devolucion ? $assignment->fecha_devolucion->format('d/m/Y H:i') : 'En curso' }}
                        </td>
                        <td>
                            @if($assignment->fecha_devolucion)
                                                <span class="status-badge 
                                                            @if($assignment->estado_devolucion == 'good') text-green
                                                            @elseif($assignment->estado_devolucion == 'regular') text-yellow
                                                            @elseif($assignment->estado_devolucion == 'bad') text-orange
                                                            @elseif($assignment->estado_devolucion == 'damaged') text-red
                                                            @else text-gray @endif">
                                                    {{ match ($assignment->estado_devolucion) {
                                    'good' => 'Bueno',
                                    'regular' => 'Regular',
                                    'bad' => 'Malo',
                                    'damaged' => 'Dañado',
                                    default => $assignment->estado_devolucion ?? ''
                                } }}
                                                </span>
                            @else
                                <span class="text-blue">Activo</span>
                            @endif
                        </td>
                        <td>
                            @if($assignment->comentarios_devolucion)
                                <div><strong>Devol:</strong> {{ $assignment->comentarios_devolucion }}</div>
                            @endif
                            @if($assignment->observaciones)
                                <div class="meta-info" style="margin-top:4px;"><strong>Obs:</strong>
                                    {{ $assignment->observaciones }}</div>
                            @endif
                            @if($assignment->photos && $assignment->photos->count() > 0)
                                <div style="margin-top:4px; font-size:8pt; color:#3b82f6;">
                                    <strong>📷 Fotos:</strong> {{ $assignment->photos->count() }} adjunta(s)
                                </div>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $assignment->creator->name ?? 'N/A' }}</strong><br>
                            <span class="meta-info">{{ $assignment->creator->rut ?? '' }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-state">
                            No hay registros para este periodo.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </main>

</body>

</html>