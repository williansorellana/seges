<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rendición de Gastos</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; color: #4f46e5; }
        .subtitle { font-size: 14px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; font-weight: bold; }
        .total-row { font-weight: bold; background-color: #e0e7ff; }
        .signature { margin-top: 40px; text-align: center; }
        .hash { font-family: monospace; font-size: 10px; color: #888; text-align: center; margin-top: 50px; border-top: 1px dashed #ccc; padding-top: 10px; }
        .badge-green { color: green; font-weight: bold; }
        .badge-red { color: red; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <div class="title">INFORME DE RENDICIÓN DE GASTOS</div>
        <div class="subtitle">Folio: RND-{{ str_pad($rendition->id, 4, '0', STR_PAD_LEFT) }}</div>
    </div>

    <table>
        <tr>
            <th>Trabajador</th>
            <td>{{ $rendition->user->name }}</td>
            <th>Fecha Solicitud</th>
            <td>{{ $rendition->routePlanning->created_at->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <th>Destino / Motivo</th>
            <td colspan="3">{{ $rendition->routePlanning->destination }} - {{ $rendition->routePlanning->motive }}</td>
        </tr>
        <tr>
            <th>Estado Actual</th>
            <td colspan="3">{{ strtoupper($rendition->status) }}</td>
        </tr>
    </table>

    <div class="title" style="margin-bottom: 10px; font-size: 14px;">Detalle de Gastos Declarados</div>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Proveedor</th>
                <th>Tipo Doc.</th>
                <th>Nº Doc.</th>
                <th style="text-align: right;">Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rendition->expenses as $expense)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($expense->date)->format('d/m/Y') }}</td>
                    <td>{{ $expense->provider }}</td>
                    <td style="text-transform: uppercase;">{{ $expense->document_type }}</td>
                    <td>{{ $expense->document_number ?? 'S/N' }}</td>
                    <td style="text-align: right;">${{ number_format($expense->amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" style="text-align: right;">Total Rendido:</td>
                <td style="text-align: right;">${{ number_format($rendition->total_declared, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="title" style="margin-bottom: 10px; font-size: 14px;">Resumen Financiero</div>
    <table>
        <tr>
            <th>Fondos Entregados (Anticipo)</th>
            <td style="text-align: right;">${{ number_format($rendition->funds_received, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Total Gastos Rendidos</th>
            <td style="text-align: right;">${{ number_format($rendition->total_declared, 0, ',', '.') }}</td>
        </tr>
        @php $diff = $rendition->funds_received - $rendition->total_declared; @endphp
        <tr class="total-row">
            <th>Saldo a favor {{ $diff > 0 ? 'Empresa' : 'Trabajador' }}</th>
            <td style="text-align: right;" class="{{ $diff < 0 ? 'badge-red' : 'badge-green' }}">
                ${{ number_format(abs($diff), 0, ',', '.') }}
            </td>
        </tr>
    </table>

    <table style="margin-top: 50px; border: none;">
        <tr style="border: none;">
            <td style="border: none; text-align: center; width: 50%;">
                ___________________________<br><br>
                Firma Trabajador<br>
                {{ $rendition->user->name }}
            </td>
            <td style="border: none; text-align: center; width: 50%;">
                ___________________________<br><br>
                Aprobación Finanzas
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
