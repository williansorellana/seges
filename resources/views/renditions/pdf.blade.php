<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rendición de Gastos</title>
    <style>
        @page {
        margin: 18px 22px;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 8px;
            color: #222;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }

        .title {
            font-size: 14px;
            font-weight: bold;
            color: #4f46e5;
        }

        .subtitle {
            font-size: 9px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            table-layout: fixed;
        }

        th, td {
            border: 1px solid #000;
            padding: 3px;
            text-align: left;
            vertical-align: middle;
            word-wrap: break-word;
        }
            th { background-color: #f3f4f6; font-weight: bold; }
            .total-row { font-weight: bold; background-color: #e0e7ff; }
            .signature {
        margin-top: 20px;
        text-align: center;
        }

        .hash {
            font-family: monospace;
            font-size: 7px;
            color: #888;
            text-align: center;
            margin-top: 25px;
            border-top: 1px dashed #ccc;
            padding-top: 6px;
        }
        .badge-green { color: green; font-weight: bold; }
        .badge-red { color: red; font-weight: bold; }
        
        .company-header {
            margin-bottom: 8px;
        }

        .company-header td {
            border: 1px solid #000;
            padding: 4px;
        }

        .info-table {
            margin-bottom: 10px;
        }

        .info-table th,
        .info-table td {
            border: 1px solid #000;
            padding: 3px;
        }
    </style>
</head>
<body>

    @php
        $logoPath = public_path('images/dimak-logo.png');
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
                RENDICIÓN DE GASTOS
            </td>
        </tr>
        <tr>
            <td style="text-align: center; font-weight: bold;">Revisión N°: 04</td>
            <td style="text-align: center; font-weight: bold;">
                VIÁTICOS &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; DIMAK-DOC-PAP-01
            </td>
        </tr>
    </table>

    @php
        $user = $rendition->user;
        $planning = $rendition->routePlanning;

        $fullName = trim(($user->name ?? '') . ' ' . ($user->last_name ?? ''));
        $department = $user->departamento ?? 'No registrado';
        $rut = $user->rut ?? 'No registrado';
        $email = $user->email ?? 'No registrado';
        $phone = $user->phone ?? $user->telefono ?? 'No registrado';
        $cellphone = $user->cellphone ?? $user->celular ?? 'No registrado';

        $startDate = $planning->start_date ? \Carbon\Carbon::parse($planning->start_date)->format('d/m/Y') : 'No registrado';
        $endDate = $planning->end_date ? \Carbon\Carbon::parse($planning->end_date)->format('d/m/Y') : 'No registrado';
        $renditionDate = $rendition->created_at ? $rendition->created_at->format('d/m/Y') : 'No registrado';
        $deliveryDate = $rendition->updated_at ? $rendition->updated_at->format('d/m/Y') : 'No registrado';

        $destination = $planning->destination ?? 'No registrado';
        $motive = $planning->motive ?? 'No registrado';

        $statusLabels = [
            'draft' => 'Borrador',
            'pending_jefatura' => 'Pendiente Jefatura',
            'pending_controlling' => 'Pendiente Controlling',
            'pending_finances' => 'Pendiente Finanzas',
            'approved' => 'Aprobada',
            'rejected' => 'Rechazada',
        ];

        $statusLabel = $statusLabels[$rendition->status] ?? strtoupper($rendition->status);

    @endphp

    <table class="info-table">
        <tr>
            <th style="width: 25%;">Área/Departamento</th>
            <td colspan="5">{{ $department }}</td>
        </tr>
        <tr>
            <th>Nombre</th>
            <td colspan="5">{{ $fullName ?: 'No registrado' }}</td>
        </tr>
        <tr>
            <th>Rut</th>
            <td colspan="5">{{ $rut }}</td>
        </tr>
        <tr>
            <th>Correo</th>
            <td>{{ $email }}</td>
            <th>Teléfono</th>
            <td>{{ $phone }}</td>
            <th>Celular</th>
            <td>{{ $cellphone }}</td>
        </tr>
        <tr>
            <th>Fecha de Inicio</th>
            <td>{{ $startDate }}</td>
            <th>Fecha de Término</th>
            <td>{{ $endDate }}</td>
            <th>Dimak</th>
            <td>DIMAK</td>
        </tr>
        <tr>
            <th>Fecha de rendición</th>
            <td>{{ $renditionDate }}</td>
            <th>Fecha de entrega</th>
            <td>{{ $deliveryDate }}</td>
            <th>Estado</th>
            <td class="{{ $rendition->status === 'approved' ? 'badge-green' : ($rendition->status === 'rejected' ? 'badge-red' : '') }}">
                {{ $statusLabel }}
            </td>
        </tr>
        <tr>
            <th>Dirección/Región/Ciudad Colaborador</th>
            <td colspan="3">{{ $destination }}</td>
            <th>Detalle</th>
            <td>{{ $motive }}</td>
        </tr>
        <tr>
            <th>Forma de cobro</th>
            <td colspan="2">Movilización</td>
            <th>Tipo de cobro</th>
            <td colspan="2">DIMAK</td>
        </tr>
    </table>

    @if($rendition->status === 'rejected')
        <table>
            <tr>
                <td class="badge-red" style="text-align: center; font-weight: bold;">
                    Rendición rechazada. Este documento se genera solo como respaldo histórico y no constituye aprobación ni cierre financiero válido.
                </td>
            </tr>
        </table>
    @endif

    <div class="title" style="margin-bottom: 10px; font-size: 14px;">Detalle de Gastos Declarados</div>

    <table>
        <thead>
            <tr>
                <th style="width: 8%;">Día</th>
                <th style="width: 15%;">Detalle</th>
                <th style="width: 8%;">Doc.</th>
                <th style="width: 8%;">Nº</th>
                <th style="width: 8%; text-align: right;">Bencina</th>
                <th style="width: 7%; text-align: right;">Peaje</th>
                <th style="width: 10%; text-align: right;">Estac./Transb.</th>
                <th style="width: 9%; text-align: right;">Aloj.</th>
                <th style="width: 8%; text-align: right;">Comida</th>
                <th style="width: 7%; text-align: right;">Otros</th>
                <th style="width: 8%; text-align: right;">Total</th>
            </tr>
        </thead>

        <tbody>
            @foreach($rendition->expenses as $expense)
                @php
                    $category = $expense->expense_category ?? 'otros';

                    $bencina = $category === 'bencina' ? $expense->amount : 0;
                    $peaje = $category === 'peaje' ? $expense->amount : 0;
                    $estacionamiento = $category === 'estacionamiento_transbordador' ? $expense->amount : 0;
                    $alojamiento = $category === 'alojamiento' ? $expense->amount : 0;
                    $comida = $category === 'comida' ? $expense->amount : 0;
                    $otros = $category === 'otros' ? $expense->amount : 0;
                @endphp

                <tr>
                    <td>{{ \Carbon\Carbon::parse($expense->date)->format('d/m/Y') }}</td>
                    <td>{{ $expense->provider }}</td>
                    <td style="text-transform: uppercase;">{{ $expense->document_type }}</td>
                    <td>{{ $expense->document_number ?? 'S/N' }}</td>

                    <td style="text-align: right;">{{ $bencina > 0 ? '$'.number_format($bencina, 0, ',', '.') : '-' }}</td>
                    <td style="text-align: right;">{{ $peaje > 0 ? '$'.number_format($peaje, 0, ',', '.') : '-' }}</td>
                    <td style="text-align: right;">{{ $estacionamiento > 0 ? '$'.number_format($estacionamiento, 0, ',', '.') : '-' }}</td>
                    <td style="text-align: right;">{{ $alojamiento > 0 ? '$'.number_format($alojamiento, 0, ',', '.') : '-' }}</td>
                    <td style="text-align: right;">{{ $comida > 0 ? '$'.number_format($comida, 0, ',', '.') : '-' }}</td>
                    <td style="text-align: right;">{{ $otros > 0 ? '$'.number_format($otros, 0, ',', '.') : '-' }}</td>

                    <td style="text-align: right; font-weight: bold;">
                        ${{ number_format($expense->amount, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach

            <tr class="total-row">
                <td colspan="4" style="text-align: right;">Subtotal:</td>

                <td style="text-align: right;">
                    ${{ number_format($rendition->expenses->where('expense_category', 'bencina')->sum('amount'), 0, ',', '.') }}
                </td>

                <td style="text-align: right;">
                    ${{ number_format($rendition->expenses->where('expense_category', 'peaje')->sum('amount'), 0, ',', '.') }}
                </td>

                <td style="text-align: right;">
                    ${{ number_format($rendition->expenses->where('expense_category', 'estacionamiento_transbordador')->sum('amount'), 0, ',', '.') }}
                </td>

                <td style="text-align: right;">
                    ${{ number_format($rendition->expenses->where('expense_category', 'alojamiento')->sum('amount'), 0, ',', '.') }}
                </td>

                <td style="text-align: right;">
                    ${{ number_format($rendition->expenses->where('expense_category', 'comida')->sum('amount'), 0, ',', '.') }}
                </td>

                <td style="text-align: right;">
                    ${{ number_format($rendition->expenses->where('expense_category', 'otros')->sum('amount'), 0, ',', '.') }}
                </td>

                <td style="text-align: right;">
                    ${{ number_format($rendition->total_declared, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    <div class="title" style="margin-bottom: 10px; font-size: 14px;">Resumen Financiero</div>
    <table>
        @php
            $planning = $rendition->routePlanning;
            $normalFunds = $planning->requested_funds ?? 0;
            $amipassAmount = $planning->amipass_amount ?? 0;
        @endphp

        <tr>
            <th>Fondos por rendir</th>
            <td style="text-align: right;">${{ number_format($normalFunds, 0, ',', '.') }}</td>
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
        @endif

        <tr>
            <th>Total entregado / asignado</th>
            <td style="text-align: right;">${{ number_format($rendition->funds_received, 0, ',', '.') }}</td>
        </tr>

        <tr>
            <th>Total Gastos Rendidos</th>
            <td style="text-align: right;">${{ number_format($rendition->total_declared, 0, ',', '.') }}</td>
        </tr>
        @php
            $diff = $rendition->status === 'approved'
                ? $rendition->difference
                : ($rendition->funds_received - $rendition->total_declared);
        @endphp

        <tr class="total-row">
            @if($diff > 0)
                <th>Saldo a devolver a Empresa</th>
                <td style="text-align: right;" class="badge-green">
                    ${{ number_format(abs($diff), 0, ',', '.') }}
                </td>
            @elseif($diff < 0)
                <th>Reembolso a favor del Trabajador</th>
                <td style="text-align: right;" class="badge-red">
                    ${{ number_format(abs($diff), 0, ',', '.') }}
                </td>
            @else
                <th>Resultado Final</th>
                <td style="text-align: right;" class="badge-green">
                    Sin saldo pendiente
                </td>
            @endif
        </tr>

        @if($rendition->status === 'approved' && $rendition->refund_resolved_at)
            <tr>
                <th>Fecha de cierre financiero</th>
                <td style="text-align: right;">
                    {{ \Carbon\Carbon::parse($rendition->refund_resolved_at)->format('d/m/Y H:i') }}
                </td>
            </tr>
        @endif
    </table>

    <table style="margin-top: 20px; border: none;">
        <tr style="border: none;">
            <td style="border: none; text-align: center; width: 50%;">
                ___________________________<br><br>
                Firma Trabajador<br>
                {{ $rendition->user->name }}
            </td>
            <td style="border: none; text-align: center; width: 50%;">
                ___________________________<br><br>
                Aprobación Finanzas<br>
                @if($rendition->status === 'approved' && $rendition->refund_resolved_at)
                    Cierre financiero: {{ \Carbon\Carbon::parse($rendition->refund_resolved_at)->format('d/m/Y H:i') }}
                @elseif($rendition->status === 'rejected')
                    Rendición rechazada
                @else
                    Pendiente de cierre
                @endif
            </td>
        </tr>
    </table>

    <div class="hash">
        Documento generado automáticamente por Sistema Seges.<br>
        Firma Electrónica (Hash Planificación original): {{ $rendition->routePlanning->digital_signature ?? 'N/A' }}<br>
        Fecha de impresión: {{ now()->format('d/m/Y H:i:s') }}
    </div>

</body>
</html>
