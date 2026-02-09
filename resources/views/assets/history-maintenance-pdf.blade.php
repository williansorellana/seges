<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Historial de Mantenciones</title>
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
            border-bottom: 3px solid #ea580c;
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
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
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
            border-right: 1px solid #e2e8f0;
        }

        .stat-item:last-child {
            border-right: none;
        }

        .stat-label {
            font-size: 7pt;
            color: #92400e;
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
            color: #ea580c;
        }

        .stat-value.preventivas {
            color: #2563eb;
        }

        .stat-value.correctivas {
            color: #dc2626;
        }

        .stat-value.completadas {
            color: #10b981;
        }

        .stat-value.enproceso {
            color: #f59e0b;
        }

        .stat-value.costo {
            color: #7c3aed;
        }

        /* Filtros aplicados */
        .filters-section {
            background-color: #dbeafe;
            border-left: 4px solid #3b82f6;
            padding: 8px 12px;
            margin: 10px 0;
            font-size: 8pt;
        }

        .filters-title {
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 3px;
        }

        .filters-list {
            color: #1e40af;
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

        tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        /* Badges */
        .badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 7pt;
            font-weight: bold;
            display: inline-block;
        }

        .badge-preventiva {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-correctiva {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-ongoing {
            color: #d97706;
            font-weight: bold;
        }

        .status-completed {
            color: #059669;
            font-weight: bold;
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

        .cost-highlight {
            color: #7c3aed;
            font-weight: bold;
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
                <h1>Historial de Mantenciones</h1>
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
                Sistema de Gestión de Activos - Historial de Mantenciones
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
                    <div class="stat-value total">{{ $totalMantenciones }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Preventivas</div>
                    <div class="stat-value preventivas">{{ $totalPreventivas }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Correctivas</div>
                    <div class="stat-value correctivas">{{ $totalCorrectivas }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Completadas</div>
                    <div class="stat-value completadas">{{ $totalCompletadas }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">En Proceso</div>
                    <div class="stat-value enproceso">{{ $totalEnProceso }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Costo Total</div>
                    <div class="stat-value costo">${{ number_format($costoTotal, 0, ',', '.') }}</div>
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
                    <th style="width: 10%">Tipo</th>
                    <th style="width: 11%">Inicio</th>
                    <th style="width: 11%">Término</th>
                    <th style="width: 24%">Descripción / Motivo</th>
                    <th style="width: 24%">Solución / Resultado</th>
                    <th style="width: 10%">Costo</th>
                    <th style="width: 10%">Responsable</th>
                </tr>
            </thead>
            <tbody>
                @forelse($maintenances as $maintenance)
                    <tr>
                        <td>
                            <span
                                class="badge {{ $maintenance->tipo === 'preventiva' ? 'badge-preventiva' : 'badge-correctiva' }}">
                                {{ ucfirst($maintenance->tipo) }}
                            </span>
                        </td>
                        <td>{{ $maintenance->fecha ? $maintenance->fecha->format('d/m/Y') : '-' }}</td>
                        <td>
                            @if($maintenance->fecha_termino)
                                <span class="status-completed">{{ $maintenance->fecha_termino->format('d/m/Y') }}</span>
                            @else
                                <span class="status-ongoing">En Proceso</span>
                            @endif
                        </td>
                        <td>{{ $maintenance->descripcion }}</td>
                        <td>
                            @if($maintenance->detalles_solucion)
                                {{ $maintenance->detalles_solucion }}
                                @if($maintenance->photos && $maintenance->photos->count() > 0)
                                    <div style="margin-top:4px; font-size:8pt; color:#10b981;">
                                        <strong>📷 Fotos:</strong> {{ $maintenance->photos->count() }} adjunta(s)
                                    </div>
                                @endif
                            @else
                                <span style="color: #94a3b8; font-style: italic;">Sin detalles aún</span>
                            @endif
                        </td>
                        <td>
                            @if($maintenance->costo)
                                <span class="cost-highlight">${{ number_format($maintenance->costo, 0, ',', '.') }}</span>
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $maintenance->creator->name ?? 'N/A' }}</strong><br>
                            <span class="meta-info">{{ $maintenance->creator->rut ?? '' }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-state">
                            No hay registros de mantención para este activo.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </main>

</body>

</html>