<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Inventario de Activos</title>
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
            border-bottom: 3px solid #2563eb;
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
        }

        .logo-placeholder {
            width: 100px;
            height: 60px;
            border: 2px dashed #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8pt;
            color: #94a3b8;
            text-align: center;
            padding: 5px;
        }

        .title-section {
            display: table-cell;
            vertical-align: top;
            padding-left: 15px;
        }

        h1 {
            margin: 0 0 5px 0;
            color: #1e293b;
            font-size: 18pt;
            font-weight: bold;
        }

        .meta-date {
            color: #64748b;
            font-size: 8pt;
            margin: 0;
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
            font-size: 14pt;
            font-weight: bold;
            color: #1e293b;
        }

        .stat-value.total {
            color: #2563eb;
        }

        .stat-value.disponible {
            color: #10b981;
        }

        .stat-value.asignado {
            color: #3b82f6;
        }

        .stat-value.mantenimiento {
            color: #f59e0b;
        }

        .stat-value.baja {
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

        tbody tr:hover {
            background-color: #f1f5f9;
        }

        /* Badges de estado */
        .status-badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 7pt;
            font-weight: bold;
            color: white;
            text-transform: uppercase;
            display: inline-block;
            text-align: center;
        }

        .status-available {
            background-color: #10b981;
        }

        .status-assigned {
            background-color: #3b82f6;
        }

        .status-maintenance {
            background-color: #f59e0b;
        }

        .status-written_off {
            background-color: #ef4444;
        }

        /* Metadatos */
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

        /* Ajuste para contenido principal */
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
            <div class="logo-section" style="padding-top: 10px;">
                @php
                    $logoPath = public_path('images/dimak-logo.png');
                    $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
                @endphp
                @if($logoData)
                    <img src="data:image/png;base64,{{ $logoData }}" alt="Dimak Logo"
                        style="max-width: 100px; max-height: 60px; object-fit: contain;">
                @else
                    <div class="logo-placeholder">
                        Logo<br>Empresa
                    </div>
                @endif
            </div>
            <div class="title-section">
                <h1>Inventario de Activos</h1>
                <p class="meta-date">Generado el {{ $generatedDate }}</p>
            </div>
        </div>
    </header>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-left">
                Sistema de Gestión de Activos
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
                    <div class="stat-label">Total Activos</div>
                    <div class="stat-value total">{{ $totalActivos }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Disponibles</div>
                    <div class="stat-value disponible">{{ $totalDisponibles }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Asignados</div>
                    <div class="stat-value asignado">{{ $totalAsignados }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Mantenimiento</div>
                    <div class="stat-value mantenimiento">{{ $totalMantenimiento }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Dados de Baja</div>
                    <div class="stat-value baja">{{ $totalBaja }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Valor Total</div>
                    <div class="stat-value total">${{ number_format($valorTotal, 0, ',', '.') }}</div>
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

        <!-- Tabla de Activos -->
        <table>
            <thead>
                <tr>
                    <th style="width: 10%">Código</th>
                    <th style="width: 20%">Nombre / Marca / Modelo</th>
                    <th style="width: 12%">Categoría</th>
                    <th style="width: 10%">Estado</th>
                    <th style="width: 13%">Ubicación</th>
                    <th style="width: 10%">Valor Ref.</th>
                    <th style="width: 25%">Detalle / Asignación</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assets as $asset)
                    <tr>
                        <td>
                            <strong>{{ $asset->codigo_interno }}</strong><br>
                            <span class="meta-info">{{ $asset->codigo_barra }}</span>
                        </td>
                        <td>
                            <strong>{{ $asset->nombre }}</strong><br>
                            <span class="meta-info">
                                {{ $asset->marca }} {{ $asset->modelo }}
                                @if($asset->numero_serie)
                                    <br>SN: {{ $asset->numero_serie }}
                                @endif
                            </span>
                        </td>
                        <td>{{ $asset->category->nombre ?? 'N/A' }}</td>
                        <td>
                            @php
                                $statusMap = [
                                    'available' => ['label' => 'Disponible', 'class' => 'status-available'],
                                    'assigned' => ['label' => 'Asignado', 'class' => 'status-assigned'],
                                    'maintenance' => ['label' => 'En Mantención', 'class' => 'status-maintenance'],
                                    'written_off' => ['label' => 'Dado de Baja', 'class' => 'status-written_off'],
                                ];
                                $status = $statusMap[$asset->estado] ?? ['label' => $asset->estado, 'class' => ''];
                            @endphp
                            <span class="status-badge {{ $status['class'] }}">
                                {{ $status['label'] }}
                            </span>
                        </td>
                        <td>
                            {{ $asset->ubicacion ?? 'No definida' }}
                            @if($asset->fecha_adquisicion)
                                <br><span class="meta-info">Adq:
                                    {{ \Carbon\Carbon::parse($asset->fecha_adquisicion)->format('d/m/Y') }}</span>
                            @endif
                        </td>
                        <td>
                            @if($asset->valor_referencial)
                                ${{ number_format($asset->valor_referencial, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($asset->estado === 'assigned' && $asset->activeAssignment)
                                <strong>Asignado a:</strong><br>
                                {{ $asset->activeAssignment->assigned_to_name }}
                                <br>
                                <span class="meta-info">Desde:
                                    {{ \Carbon\Carbon::parse($asset->activeAssignment->fecha_entrega)->format('d/m/Y') }}</span>
                            @elseif($asset->estado === 'maintenance')
                                <strong>En taller</strong>
                            @elseif($asset->estado === 'written_off' && $asset->writeOff)
                                <strong>Motivo Baja:</strong><br>
                                {{ Str::limit($asset->writeOff->motivo, 50) }}
                                <br>
                                <span class="meta-info">Fecha:
                                    {{ \Carbon\Carbon::parse($asset->writeOff->fecha)->format('d/m/Y') }}</span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-state">
                            No se encontraron activos con los filtros seleccionados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </main>

</body>

</html>