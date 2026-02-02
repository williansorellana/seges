<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Reporte: Top Usuarios y Trabajadores</title>
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
            border-bottom: 3px solid #14b8a6;
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

        h2 {
            font-size: 11pt;
            margin-top: 20px;
            margin-bottom: 10px;
            color: #0f766e;
            background-color: #ccfbf1;
            padding: 8px 12px;
            border-left: 4px solid #14b8a6;
        }

        .meta-date {
            color: #64748b;
            font-size: 8pt;
            margin: 0;
        }

        /* Estadísticas */
        .stats-section {
            background-color: #ccfbf1;
            border: 1px solid #5eead4;
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
            padding: 5px 8px;
            border-right: 1px solid #5eead4;
        }

        .stat-item:last-child {
            border-right: none;
        }

        .stat-label {
            font-size: 7pt;
            color: #0f766e;
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
            color: #14b8a6;
        }

        .stat-value.internos {
            color: #0891b2;
        }

        .stat-value.externos {
            color: #06b6d4;
        }

        .top-name {
            font-size: 8pt;
            color: #134e4a;
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
            margin-bottom: 20px;
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
            background-color: #f0fdfa;
        }

        tbody tr:nth-child(odd) {
            background-color: #ccfbf1;
        }

        .rank-cell {
            text-align: center;
            font-weight: bold;
            color: #14b8a6;
        }

        .total-cell {
            text-align: center;
            font-weight: bold;
            color: #14b8a6;
            font-size: 10pt;
        }

        .meta-info {
            font-size: 7pt;
            color: #64748b;
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
            padding: 30px 20px;
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
                <h1>Reporte: Top Usuarios y Trabajadores</h1>
                <p class="meta-date">Generado el {{ $generatedDate }}</p>
            </div>
        </div>
    </header>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-left">
                Sistema de Gestión de Activos - Reporte de Usuarios
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
                    <div class="stat-label">Usuarios Int.</div>
                    <div class="stat-value internos">{{ $totalUsuarios }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Trabajadores Ext.</div>
                    <div class="stat-value externos">{{ $totalTrabajadores }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Asig. Internos</div>
                    <div class="stat-value internos">{{ $totalAsignacionesUsuarios }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Asig. Externos</div>
                    <div class="stat-value externos">{{ $totalAsignacionesTrabajadores }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Total Global</div>
                    <div class="stat-value total">{{ $totalAsignaciones }}</div>
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

        <!-- Usuarios Internos -->
        <h2>Usuarios Internos</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 6%">#</th>
                    <th style="width: 44%">Nombre</th>
                    <th style="width: 30%">RUT</th>
                    <th style="width: 20%">Asignaciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $index => $user)
                    <tr>
                        <td class="rank-cell">{{ $index + 1 }}</td>
                        <td><strong>{{ $user->user->name ?? 'Usuario Eliminado' }}</strong></td>
                        <td class="meta-info">{{ $user->user->rut ?? '-' }}</td>
                        <td class="total-cell">{{ $user->total }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty-state">
                            No hay datos de usuarios internos.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Trabajadores Externos -->
        <h2>Trabajadores Externos</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 6%">#</th>
                    <th style="width: 44%">Nombre</th>
                    <th style="width: 30%">RUT</th>
                    <th style="width: 20%">Asignaciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($workers as $index => $worker)
                    <tr>
                        <td class="rank-cell">{{ $index + 1 }}</td>
                        <td><strong>{{ $worker->worker->nombre ?? 'Trabajador Eliminado' }}</strong></td>
                        <td class="meta-info">{{ $worker->worker->rut ?? '-' }}</td>
                        <td class="total-cell">{{ $worker->total }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty-state">
                            No hay datos de trabajadores externos.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </main>

</body>

</html>