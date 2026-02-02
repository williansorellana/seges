<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Reporte: Activos Dados de Baja</title>
    <style>
        @page {
            margin: 100px 50px 80px 50px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            color: #333;
        }

        /* Header fijo */
        header {
            position: fixed;
            top: -100px;
            left: 0;
            right: 0;
            height: 90px;
            border-bottom: 3px solid #dc2626;
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

        /* Estadísticas */
        .stats-section {
            background-color: #fee2e2;
            border: 1px solid #fca5a5;
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
            border-right: 1px solid #fca5a5;
        }

        .stat-item:last-child {
            border-right: none;
        }

        .stat-label {
            font-size: 7pt;
            color: #991b1b;
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
            color: #dc2626;
        }

        .stat-value.valor {
            color: #b91c1c;
        }

        .motivo-principal {
            font-size: 8pt;
            color: #7f1d1d;
            margin-top: 3px;
            font-weight: normal;
        }

        /* Filtros */
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
            background-color: #fef2f2;
        }

        tbody tr:nth-child(odd) {
            background-color: #fee2e2;
        }

        .meta-info {
            font-size: 7pt;
            color: #64748b;
        }

        .price-highlight {
            color: #b91c1c;
            font-weight: bold;
        }

        /* Footer fijo */
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
                <h1>Reporte: Activos Dados de Baja</h1>
                <p class="meta-date">Generado el {{ $generatedDate }}</p>
            </div>
        </div>
    </header>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-left">
                Sistema de Gestión de Activos - Reporte de Bajas
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
                    <div class="stat-label">Total Bajas</div>
                    <div class="stat-value total">{{ $totalBajas }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Valor Total Perdido</div>
                    <div class="stat-value valor">${{ number_format($valorTotal, 0, ',', '.') }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Motivo Principal</div>
                    @if($motivoPrincipal)
                        <div class="stat-value total">{{ $casosPrincipal }}</div>
                        <div class="motivo-principal">{{ Str::limit($motivoPrincipal, 25) }}</div>
                    @else
                        <div class="stat-value">-</div>
                    @endif
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
                    <th style="width: 10%">Fecha</th>
                    <th style="width: 12%">Código</th>
                    <th style="width: 22%">Activo</th>
                    <th style="width: 10%">Categoría</th>
                    <th style="width: 10%">Valor Ref.</th>
                    <th style="width: 16%">Responsable</th>
                    <th style="width: 20%">Motivo</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>{{ $item->fecha ? $item->fecha->format('d/m/Y') : '-' }}</td>
                        <td class="meta-info">{{ $item->asset->codigo_interno }}</td>
                        <td>
                            <strong>{{ $item->asset->nombre }}</strong>
                            @if($item->asset->marca)
                                <div class="meta-info">{{ $item->asset->marca }}</div>
                            @endif
                        </td>
                        <td class="meta-info">{{ $item->asset->category->nombre ?? 'N/A' }}</td>
                        <td>
                            @if($item->asset->precio)
                                <span class="price-highlight">${{ number_format($item->asset->precio, 0, ',', '.') }}</span>
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $item->user->name ?? 'N/A' }}</strong>
                            @if($item->user && $item->user->rut)
                                <div class="meta-info">{{ $item->user->rut }}</div>
                            @endif
                        </td>
                        <td class="meta-info">{{ $item->motivo }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-state">
                            No hay activos dados de baja en este periodo.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </main>

</body>

</html>