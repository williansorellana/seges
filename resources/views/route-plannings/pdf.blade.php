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
            <th>Destino Principal</th>
            <td colspan="3">
                {{ $planning->destination ?? 'No registrado' }} @if($planning->region) (Región: {{ $planning->region }}) @endif
            </td>
        </tr>
        @if(!empty($planning->destinations))
            <tr>
                <th>Destinos Adicionales</th>
                <td colspan="3">
                    @php
                        $additionalList = [];
                        foreach($planning->destinations as $dest) {
                            if (!empty($dest['destination'])) {
                                $additionalList[] = $dest['destination'] . (!empty($dest['region']) ? ' (' . $dest['region'] . ')' : '');
                            }
                        }
                    @endphp
                    {{ implode(', ', $additionalList) }}
                </td>
            </tr>
        @endif
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
        @if($planning->requires_funds)
            <tr>
                <th style="width: 50%;">Concepto de Gasto</th>
                <th style="text-align: right; width: 50%;">Monto Solicitado</th>
            </tr>
            <tr>
                <td>Bencina / Combustible</td>
                <td style="text-align: right;">${{ number_format($planning->funds_bencina ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Peajes</td>
                <td style="text-align: right;">${{ number_format($planning->funds_peaje ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Alojamiento</td>
                <td style="text-align: right;">${{ number_format($planning->funds_alojamiento ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Alimentación</td>
                <td style="text-align: right;">${{ number_format($planning->funds_alimentacion ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Otros Gastos</td>
                <td style="text-align: right;">${{ number_format($planning->funds_otros ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <th>Subtotal Fondos por Rendir</th>
                <td style="text-align: right;">${{ number_format($requestedFunds, 0, ',', '.') }}</td>
            </tr>
            @if($planning->funds_description)
                <tr>
                    <th>Justificación Fondos</th>
                    <td>{{ $planning->funds_description }}</td>
                </tr>
            @endif
        @else
            <tr>
                <th style="width: 35%;">Fondos por rendir solicitados</th>
                <td style="text-align: right;">No solicitado</td>
            </tr>
        @endif

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
            @if($planning->observations->count() > 0)
                <tr>
                    <td style="padding: 6px; border: 1px solid #fca5a5; background-color: #fef2f2; color: #b91c1c; font-size: 8px;">
                        <strong>Motivo de rechazo:</strong>
                        <ul style="margin: 3px 0 0 12px; padding: 0;">
                            @foreach($planning->observations as $obs)
                                <li>{{ $obs->observation }} (por {{ $obs->user->name ?? 'Revisor' }} el {{ $obs->created_at->format('d/m/Y H:i') }})</li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
            @endif
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

    @php
        $workerSignature = $planning->digitalSignatures->where('signature_type', 'planning_worker_signature')->first();
        $jefaturaSignature = $planning->digitalSignatures->where('signature_type', 'jefatura_approval')->first();
        $financesSignature = $planning->digitalSignatures->where('signature_type', 'planning_finances_approval')->first();
        $hasJefatura = $planning->user && $planning->user->jefatura_id;
    @endphp

    <table style="margin-top: 25px; border: none; width: 100%; table-layout: fixed;">
        <tr style="border: none;">
            <!-- 1. Firma Solicitante -->
            <td style="border: none; width: 33%; padding: 4px; vertical-align: top;">
                @if($workerSignature)
                    <div style="border: 1px solid #10b981; padding: 6px; border-radius: 4px; background-color: #f0fdf4; text-align: left;">
                        <div style="color: #15803d; font-weight: bold; font-size: 8px; margin-bottom: 3px; text-transform: uppercase;">
                            ✔ SOLICITUD FIRMADA
                        </div>
                        <div style="font-size: 7px; color: #374151; line-height: 1.2;">
                            <strong>Colaborador:</strong> {{ $workerSignature->user ? $workerSignature->user->name . ' ' . $workerSignature->user->last_name : $planning->user->name }}<br>
                            <strong>Fecha:</strong> {{ $workerSignature->signed_at ? $workerSignature->signed_at->format('d/m/Y H:i:s') : 'N/A' }}<br>
                            <strong>IP:</strong> {{ $workerSignature->ip_address ?? 'N/A' }}<br>
                            <strong>Token:</strong> <span style="font-family: monospace; font-size: 6.5px; color: #1e3a8a;">{{ $workerSignature->verification_token }}</span><br>
                            <span style="font-size: 6px; color: #6b7280; font-family: monospace; display: block; margin-top: 2px; word-break: break-all;">
                                HASH: {{ substr($workerSignature->hash, 0, 24) }}...
                            </span>
                        </div>
                    </div>
                @else
                    <div style="border: 1px dashed #d1d5db; padding: 15px 6px; border-radius: 4px; color: #9ca3af; text-align: center; font-size: 8px; font-weight: bold; background-color: #f9fafb;">
                        FIRMA SOLICITANTE PENDIENTE
                    </div>
                @endif
            </td>

            <!-- 2. Firma Jefatura -->
            <td style="border: none; width: 33%; padding: 4px; vertical-align: top;">
                @if($jefaturaSignature)
                    <div style="border: 1px solid #10b981; padding: 6px; border-radius: 4px; background-color: #f0fdf4; text-align: left;">
                        <div style="color: #15803d; font-weight: bold; font-size: 8px; margin-bottom: 3px; text-transform: uppercase;">
                            ✔ APROBADO JEFATURA
                        </div>
                        <div style="font-size: 7px; color: #374151; line-height: 1.2;">
                            <strong>Jefatura:</strong> {{ $jefaturaSignature->user ? $jefaturaSignature->user->name . ' ' . $jefaturaSignature->user->last_name : 'Usuario' }}<br>
                            <strong>Fecha:</strong> {{ $jefaturaSignature->signed_at ? $jefaturaSignature->signed_at->format('d/m/Y H:i:s') : 'N/A' }}<br>
                            <strong>IP:</strong> {{ $jefaturaSignature->ip_address ?? 'N/A' }}<br>
                            <strong>Token:</strong> <span style="font-family: monospace; font-size: 6.5px; color: #1e3a8a;">{{ $jefaturaSignature->verification_token }}</span><br>
                            <span style="font-size: 6px; color: #6b7280; font-family: monospace; display: block; margin-top: 2px; word-break: break-all;">
                                HASH: {{ substr($jefaturaSignature->hash, 0, 24) }}...
                            </span>
                        </div>
                    </div>
                @elseif($hasJefatura)
                    <div style="border: 1px dashed #d1d5db; padding: 15px 6px; border-radius: 4px; color: #9ca3af; text-align: center; font-size: 8px; font-weight: bold; background-color: #f9fafb;">
                        APROBACIÓN JEFATURA PENDIENTE
                    </div>
                @else
                    <div style="border: 1px solid #e5e7eb; padding: 15px 6px; border-radius: 4px; color: #9ca3af; text-align: center; font-size: 8px; font-weight: bold; background-color: #f9fafb;">
                        APROBACIÓN JEFATURA (NO APLICA)
                    </div>
                @endif
            </td>

            <!-- 3. Firma Finanzas -->
            <td style="border: none; width: 33%; padding: 4px; vertical-align: top;">
                @if($financesSignature)
                    <div style="border: 1px solid #3b82f6; padding: 6px; border-radius: 4px; background-color: #eff6ff; text-align: left;">
                        <div style="color: #1d4ed8; font-weight: bold; font-size: 8px; margin-bottom: 3px; text-transform: uppercase;">
                            ✔ FONDOS LIBERADOS
                        </div>
                        <div style="font-size: 7px; color: #374151; line-height: 1.2;">
                            <strong>Finanzas:</strong> {{ $financesSignature->user ? $financesSignature->user->name . ' ' . $financesSignature->user->last_name : 'Finanzas' }}<br>
                            <strong>Fecha:</strong> {{ $financesSignature->signed_at ? $financesSignature->signed_at->format('d/m/Y H:i:s') : 'N/A' }}<br>
                            <strong>IP:</strong> {{ $financesSignature->ip_address ?? 'N/A' }}<br>
                            <strong>Token:</strong> <span style="font-family: monospace; font-size: 6.5px; color: #1e3a8a;">{{ $financesSignature->verification_token }}</span><br>
                            <span style="font-size: 6px; color: #6b7280; font-family: monospace; display: block; margin-top: 2px; word-break: break-all;">
                                HASH: {{ substr($financesSignature->hash, 0, 24) }}...
                            </span>
                        </div>
                    </div>
                @elseif($planning->status === 'rejected')
                    <div style="border: 1px solid #ef4444; padding: 15px 6px; border-radius: 4px; color: #b91c1c; text-align: center; font-size: 8px; font-weight: bold; background-color: #fef2f2;">
                        SOLICITUD RECHAZADA
                    </div>
                @else
                    <div style="border: 1px dashed #d1d5db; padding: 15px 6px; border-radius: 4px; color: #9ca3af; text-align: center; font-size: 8px; font-weight: bold; background-color: #f9fafb;">
                        APROBACIÓN FINANZAS PENDIENTE
                    </div>
                @endif
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