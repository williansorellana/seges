<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Planificación de Ruta</title>
    <style>
        @page {
            margin: 18px 22px;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 8px;
            color: #222;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            table-layout: fixed;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
            vertical-align: middle;
            word-wrap: break-word;
        }

        th {
            background-color: #f3f4f6;
            font-weight: bold;
        }

        .company-header {
            margin-bottom: 8px;
        }

        .company-header td {
            border: 1px solid #000;
            padding: 4px;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            background-color: #e5e7eb;
            border: 1px solid #000;
            padding: 5px;
            margin-bottom: 0;
        }

        .total-row {
            font-weight: bold;
            background-color: #e0e7ff;
        }

        .badge-approved {
            color: green;
            font-weight: bold;
        }

        .badge-rejected {
            color: red;
            font-weight: bold;
        }

        .hash {
            font-family: monospace;
            font-size: 7px;
            color: #777;
            text-align: center;
            margin-top: 22px;
            border-top: 1px dashed #999;
            padding-top: 6px;
        }
    </style>
</head>
<body>

    @php
        $logoPath = public_path('images/dimak-logo.png');

        $user = $planning->user;

        $fullName = trim(($user->name ?? '') . ' ' . ($user->last_name ?? ''));
        $department = $user->departamento ?? 'No registrado';
        $rut = $user->rut ?? 'No registrado';
        $email = $user->email ?? 'No registrado';
        $phone = $user->phone ?? $user->telefono ?? 'No registrado';
        $cellphone = $user->cellphone ?? $user->celular ?? 'No registrado';

        $startDate = $planning->start_date ? \Carbon\Carbon::parse($planning->start_date)->format('d/m/Y') : 'No registrado';
        $endDate = $planning->end_date ? \Carbon\Carbon::parse($planning->end_date)->format('d/m/Y') : 'No registrado';

        $requestedFunds = $planning->requested_funds ?? 0;
        $amipassAmount = $planning->amipass_amount ?? 0;
        $totalRequested = $requestedFunds + $amipassAmount;

        $statusLabels = [
            'draft' => 'Borrador',
            'pending_jefatura' => 'Pendiente Jefatura',
            'pending_controlling' => 'Pendiente Controlling',
            'pending_finances' => 'Pendiente Finanzas',
            'approved' => 'Aprobada',
            'rejected' => 'Rechazada',
        ];

        $statusLabel = $statusLabels[$planning->status] ?? strtoupper($planning->status);
    @endphp

    <table class="company-header">
        <tr>
            <td style="width: 28%; text-align: center; vertical-align: middle;">
                @if(file_exists($logoPath))
                    <img src="{{ $logoPath }}" alt="Dimak" style="height: 38px;">
                @else
                    <span style="font-size: 26px; font-weight: bold; color: #dc2626;">Dimak</span>
                @endif
            </td>
            <td style="width: 72%; text-align: center; font-size: 14px; font-weight: bold;">
                DOCUMENTO
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center; font-size: 18px; font-weight: bold;">
                PLANIFICACIÓN DE RUTA / SOLICITUD DE VIÁTICOS
            </td>
        </tr>
        <tr>
            <td style="text-align: center; font-weight: bold;">Folio: REQ-{{ str_pad($planning->id, 4, '0', STR_PAD_LEFT) }}</td>
            <td style="text-align: center; font-weight: bold;">
                VIÁTICOS &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; DIMAK-DOC-PAP-PLAN
            </td>
        </tr>
    </table>

    <div class="section-title">Datos del colaborador</div>
    <table>
        <tr>
            <th style="width: 25%;">Nombre</th>
            <td colspan="3">{{ $fullName ?: 'No registrado' }}</td>
        </tr>
        <tr>
            <th>RUT</th>
            <td>{{ $rut }}</td>
            <th>Departamento</th>
            <td>{{ $department }}</td>
        </tr>
        <tr>
            <th>Correo</th>
            <td>{{ $email }}</td>
            <th>Teléfono / Celular</th>
            <td>{{ $phone }} / {{ $cellphone }}</td>
        </tr>
    </table>

    <div class="section-title">Datos del viaje</div>
    <table>
        <tr>
            <th style="width: 25%;">Tipo de actividad</th>
            <td>{{ $planning->trip_type === 'terreno' ? 'Trabajo en terreno' : 'Reunión de negocios' }}</td>
            <th>Estado actual</th>
            <td class="{{ $planning->status === 'approved' ? 'badge-approved' : ($planning->status === 'rejected' ? 'badge-rejected' : '') }}">
                {{ $statusLabel }}
            </td>
        </tr>
        <tr>
            <th>Región</th>
            <td>{{ $planning->region ?? 'No registrado' }}</td>
            <th>Destino</th>
            <td>{{ $planning->destination ?? 'No registrado' }}</td>
        </tr>
        <tr>
            <th>Fecha inicio</th>
            <td>{{ $startDate }}</td>
            <th>Fecha término</th>
            <td>{{ $endDate }}</td>
        </tr>
        <tr>
            <th>Motivo</th>
            <td colspan="3">{{ $planning->motive ?? 'No registrado' }}</td>
        </tr>
        <tr>
            <th>Acompañantes</th>
            <td colspan="3">{{ $planning->companions ?? 'No registra acompañantes' }}</td>
        </tr>
    </table>

    <div class="section-title">Solicitud financiera</div>
    <table>
        <tr>
            <th style="width: 35%;">Fondos por rendir solicitados</th>
            <td style="text-align: right;">${{ number_format($requestedFunds, 0, ',', '.') }}</td>
        </tr>

        @if($planning->requires_amipass)
            <tr>
                <th>Amipass asignado</th>
                <td style="text-align: right;">
                    ${{ number_format($amipassAmount, 0, ',', '.') }}
                    @if($planning->amipass_business_days || $planning->amipass_days)
                        / {{ $planning->amipass_business_days ?? $planning->amipass_days }} día(s)
                    @endif
                </td>
            </tr>
            <tr>
                <th>Horario considerado para Amipass</th>
                <td>
                    Salida: {{ $planning->amipass_start_time ?? 'No registrado' }}
                    &nbsp;&nbsp; | &nbsp;&nbsp;
                    Regreso: {{ $planning->amipass_end_time ?? 'No registrado' }}
                </td>
            </tr>
        @else
            <tr>
                <th>Amipass</th>
                <td>No solicitado</td>
            </tr>
        @endif

        <tr class="total-row">
            <th>Total solicitado / asignado</th>
            <td style="text-align: right;">${{ number_format($totalRequested, 0, ',', '.') }}</td>
        </tr>
    </table>

    @if($planning->status === 'rejected')
        <table>
            <tr>
                <td class="badge-rejected" style="text-align: center; font-weight: bold;">
                    Solicitud rechazada. Este documento se genera solo como respaldo histórico y no constituye autorización de fondos, viáticos o Amipass.
                </td>
            </tr>
        </table>
    @elseif($planning->status === 'approved')
        <table>
            <tr>
                <td class="badge-approved" style="text-align: center; font-weight: bold;">
                    Planificación aprobada por el flujo correspondiente. Este documento respalda la autorización de la solicitud, pero no acredita por sí solo el cierre de la rendición de gastos.
                </td>
            </tr>
        </table>
    @endif

    <div class="section-title">Trazabilidad de aprobación</div>
    <table>
        <tr>
            <th style="width: 25%;">Fecha de creación</th>
            <td>{{ $planning->created_at ? $planning->created_at->format('d/m/Y H:i') : 'No registrado' }}</td>
            <th>Última actualización</th>
            <td>{{ $planning->updated_at ? $planning->updated_at->format('d/m/Y H:i') : 'No registrado' }}</td>
        </tr>
        <tr>
            <th>Firma digital</th>
            <td colspan="3" style="font-family: monospace; font-size: 7px;">
                {{ $planning->digital_signature ?? 'Aún no registra firma final de Finanzas' }}
            </td>
        </tr>
    </table>

    <table style="margin-top: 24px; border: none;">
        <tr style="border: none;">
            <td style="border: none; text-align: center; width: 33%;">
                ___________________________<br><br>
                Solicitante<br>
                {{ $fullName ?: 'Trabajador' }}
            </td>
            <td style="border: none; text-align: center; width: 33%;">
                ___________________________<br><br>
                Jefatura<br>
                Revisión / aprobación
            </td>
            <td style="border: none; text-align: center; width: 33%;">
                ___________________________<br><br>
                Finanzas<br>
                Liberación de fondos
            </td>
        </tr>
    </table>

    <div class="hash">
        Documento generado automáticamente por Sistema Seges.<br>
        Folio: REQ-{{ str_pad($planning->id, 4, '0', STR_PAD_LEFT) }}<br>
        Fecha de impresión: {{ now()->format('d/m/Y H:i:s') }}
    </div>

</body>
</html>