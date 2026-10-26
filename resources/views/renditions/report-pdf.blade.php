<!doctype html><html lang="es"><head><meta charset="UTF-8"><style>
@page { margin: 22px; } body { font-family: DejaVu Sans, sans-serif; font-size: 8px; color: #243247; }
h1 { font-size: 16px; } table { border-collapse: collapse; width: 100%; table-layout: fixed; } thead { display: table-header-group; }
th { background: #0f172a; color: white; text-align: left; } td, th { padding: 5px; border-bottom: 1px solid #cbd5e1; overflow-wrap: break-word; } tr { page-break-inside: avoid; } .money { text-align: right; } .reason { color: #9f1239; background: #fff1f2; }
</style></head><body>
<h1>DIMAK / Reporte consolidado de rendiciones</h1>
<p>Generado: {{ now()->format('d/m/Y H:i') }}. Incluye todos los intentos y motivos de rechazo del período filtrado.</p>
@php($filterLabels = ['user_id' => 'Colaborador (ID)', 'month' => 'Mes', 'year' => 'Año', 'trip_type' => 'Tipo de viaje', 'status' => 'Estado de planificación', 'date_from' => 'Desde', 'date_to' => 'Hasta', 'project' => 'Proyecto', 'section' => 'Sección', 'cost_center' => 'Centro de costo'])
<p>Filtros: @forelse($filters as $key => $value){{ $filterLabels[$key] ?? $key }}: {{ $key === 'status' ? \App\Helpers\WorkflowHelper::statusLabel($value) : $value }}; @empty Sin filtros @endforelse</p>
<p><strong>Fondos sin duplicar: ${{ number_format($totalFunds, 0, ',', '.') }} / Gastos vigentes sin duplicar: ${{ number_format($totalExpenses, 0, ',', '.') }}</strong></p>
<p>Los importes de intentos rechazados son respaldo histórico y no se suman como gastos vigentes. Los fondos se cuentan una vez por viaje, sin nuevos anticipos por reemplazo.</p>
<table><thead><tr><th style="width:8%">Plan / RND / intento</th><th style="width:15%">Persona / RUT</th><th style="width:14%">Centro / proyecto</th><th style="width:13%">Destino / fechas</th><th style="width:12%">Estado / último</th><th style="width:11%">Declarado (referencia)</th><th style="width:11%">Fondos sin duplicar</th><th style="width:11%">Gastos vigentes</th></tr></thead><tbody>
@forelse($rows as $row)
<tr><td>{{ $row['plan'] }} / {{ $row['rendition'] ?? '-' }} / {{ $row['attempt'] ?? '-' }}</td><td>{{ $row['name'] }}<br>{{ $row['rut'] }}</td><td>{{ $row['cost_center'] ?: 'Sin asignar' }}<br>{{ $row['project'] }}</td><td>{{ $row['destination'] }}<br>{{ $row['start'] }}<br>{{ $row['end'] }}</td><td>{{ $row['status'] }}<br>Último: {{ $row['current'] }}</td><td class="money">${{ number_format($row['declared'], 0, ',', '.') }}</td><td class="money">${{ number_format($row['counted_funds'], 0, ',', '.') }}</td><td class="money">${{ number_format($row['counted_expenses'], 0, ',', '.') }}</td></tr>
@if($row['rejection_reason'])<tr class="reason"><td colspan="8">RND-{{ $row['rendition'] }} cerrado {{ $row['closed'] }}. Auditado por: {{ $row['auditor'] }}. Motivo: {{ $row['rejection_reason'] }}</td></tr>@endif
@empty<tr><td colspan="8">No hay registros para los filtros seleccionados.</td></tr>@endforelse
</tbody></table></body></html>
