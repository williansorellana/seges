<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Etiquetas de Activos</title>
    <style>
        @page {
            margin: 5mm; 
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 0;
        /* Tabla de página completa */
        .page-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; /* Fuerza ancho de columnas fijo */
            font-size: 10px; /* Restaurar tamaño base para evitar colapsos */
        }
        
        .page-table td {
            vertical-align: top;
            padding: 2mm;
            text-align: center;
        }

        /* Contenido de Etiqueta */
        .label-content {
            /* border: 0.5px solid #eee; debug */
            padding: 2px;
            overflow: hidden; /* Evita que contenido salga del contenedor */
        }

        /* Estilos Internos */
        .logo { 
            margin-bottom: 2px;
            display: block;
            /* overflow: hidden; Removed to prevent cutting */
        }
        .logo.small { max-height: 18px; } /* Increased from 14px */
        .logo.medium { max-height: 25px; }
        .logo.large { max-height: 35px; }

        .title {
            font-weight: bold;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
            margin-bottom: 2px;
        }
        .title.small { font-size: 8px; margin-bottom: 2px; }
        .title.medium { font-size: 9px; margin-bottom: 2px; }
        .title.large { font-size: 11px; margin-bottom: 4px; }

        .barcode { margin: 2px auto; display: block; }
        .barcode img { 
            display: block; 
            margin: 0 auto; 
            clear: both; 
            height: auto;
        }
        /* Constraints for barcode width to match physical label size */
        .barcode img.small { max-width: 42mm; }
        .barcode img.medium { max-width: 62mm; }
        .barcode img.large { max-width: 92mm; }

        .code { font-weight: bold; line-height: 1; display: block; margin-top: 1px; }
        .code.small { font-size: 7px; }
        .code.medium { font-size: 8px; }
        .code.large { font-size: 9px; margin-top: 2px; }

        .barcode-text { color: #333; line-height: 1; display: block; }
        .barcode-text.small { font-size: 6px; }
        .barcode-text.medium { font-size: 7px; }
        .barcode-text.large { font-size: 8px; }

        .meta { color: #555; line-height: 1.1; margin-top: 2px; display: block; }
        .meta.medium { font-size: 7px; }
        .meta.large { font-size: 8px; }

        .divider { margin: 0 2px; }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('images/dimak-logo.png');
        $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
        $labelSize = $size ?? 'medium';

        // Configuración
        switch($labelSize) {
            case 'small':
                $cols = 3;
                $rowsPerPage = 9; // 9 filas x 3 cols = 27
                break;
            case 'medium':
                $cols = 2;
                $rowsPerPage = 7; // 7 filas x 2 cols = 14
                break;
            case 'large':
                $cols = 2;
                $rowsPerPage = 5; // 5 filas x 2 cols = 10
                break;
            default:
                $cols = 2;
                $rowsPerPage = 7;
        }
        
        $itemsPerPage = $cols * $rowsPerPage;
        $pages = $assetsWithBarcodes->chunk($itemsPerPage);
    @endphp

    @foreach($pages as $pageIndex => $pageItems)
        <table class="page-table">
            <tbody>
                @foreach($pageItems->chunk($cols) as $rowItems)
                    <tr>
                        @foreach($rowItems as $item)
                        <td style="width: {{ 100 / $cols }}%;">
                            <div class="label-content">
                                {{-- Logo --}}
                                @if($logoData)
                                    <div class="logo {{ $labelSize }}">
                                        <img src="data:image/png;base64,{{ $logoData }}" alt="Dimak"
                                            style="max-height: 100%; max-width: 100px; object-fit: contain;">
                                    </div>
                                @else
                                    <div class="logo {{ $labelSize }}">DIMAK</div>
                                @endif

                                {{-- Nombre --}}
                                <div class="title {{ $labelSize }}">
                                    {{ Str::limit($item['asset']->nombre, $labelSize == 'small' ? 15 : ($labelSize == 'medium' ? 22 : 28)) }}
                                </div>

                                {{-- Barcode --}}
                                <div class="barcode">
                                    <img src="data:image/png;base64,{{ $item['barcode'] }}" alt="Barcode" 
                                        class="{{ $labelSize }}"
                                        style="width: auto; max-width: 100%;">
                                </div>

                                {{-- Código --}}
                                <div class="code {{ $labelSize }}">
                                    {{ $item['asset']->codigo_interno }}
                                </div>
                                <div class="barcode-text {{ $labelSize }}">
                                    {{ $item['asset']->codigo_barra }}
                                </div>

                                {{-- Info Extra --}}
                                @if($labelSize !== 'small')
                                    <div class="meta {{ $labelSize }}">
                                        @if($item['asset']->category) {{ $item['asset']->category->nombre }} @endif
                                        @if($item['asset']->category && $item['asset']->ubicacion) <span class="divider">|</span> @endif
                                        @if($item['asset']->ubicacion) {{ $item['asset']->ubicacion }} @endif
                                    </div>
                                @endif

                                @if($labelSize === 'large' && $item['asset']->numero_serie)
                                    <div class="meta {{ $labelSize }}">
                                        SN: {{ Str::limit($item['asset']->numero_serie, 20) }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        @endforeach
                        
                        {{-- Rellenar celdas vacías --}}
                        @for($i = count($rowItems); $i < $cols; $i++)
                            <td></td>
                        @endfor
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>