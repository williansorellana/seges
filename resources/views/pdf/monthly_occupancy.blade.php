<!DOCTYPE html>
<html>
<head>
    <title>Informe Mensual de Ocupación</title>
    <style>
        body { font-family: sans-serif; color: #333; }
        h1 { text-align: center; color: #1a202c; margin-bottom: 5px; }
        .meta { text-align: center; margin-bottom: 20px; font-size: 14px; color: #555; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #2d3748; color: white; padding: 8px; font-size: 10px; text-align: left; }
        td { border: 1px solid #e2e8f0; padding: 6px; font-size: 10px; vertical-align: top; }
        
        tr:nth-child(even) { background-color: #f7fafc; }
        
        .status-finished { color: #718096; font-weight: bold; }
        .status-scheduled { color: #38a169; font-weight: bold; }
        .center { text-align: center; }
    </style>
</head>
<body>
    <h1>Informe de Ocupación de Salas</h1>
    
    <div class="meta">
        <strong>Periodo:</strong> {{ $month }} <br>
        <strong>Total Reservas:</strong> {{ $reservations->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th width="12%">Fecha</th>
                <th width="13%">Horario</th>
                <th width="15%">Sala</th>
                <th width="15%">Solicitante</th>
                <th width="8%" class="center">Asist.</th> <th width="25%">Propósito</th>
                <th width="12%">Estado</th>
            </tr>
        </thead>
        <tbody>
            @if ($reservations->isEmpty())
                <tr>
                    <td colspan="7" style="text-align:center; padding:20px;">
                        No se encontraron reservas para este periodo.
                    </td>
                </tr>
            @else
                @foreach($reservations as $res)
                    <tr>
                        <td>{{ $res->start_time->format('d/m/Y') }}</td>
                        <td>{{ $res->start_time->format('H:i') }} - {{ $res->end_time->format('H:i') }}</td>
                        <td>{{ $res->meetingRoom->name ?? 'Sala Eliminada' }}</td>
                        <td>{{ $res->user->name ?? 'Usuario Eliminado' }}</td>
                    
                        <td class="center">{{ $res->attendees }}</td>
                    
                        <td>{{ $res->purpose }}</td>
                        <td>
                            @if($res->end_time->isPast())
                                <span class="status-finished">Finalizada</span>
                            @else
                                <span class="status-scheduled">Programada</span>
                         @endif
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</body>
</html>