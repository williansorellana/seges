<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Etiqueta - {{ $asset->codigo_interno }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 2px;
            /* Reducido de 5px */
            text-align: center;
        }

        .container {
            /* border: 1px dashed #000; Eliminated prepicado */
            padding: 2px;
            /* Reducido de 8px */
            display: inline-block;
            width: 98%;
            height: 98%;
            box-sizing: border-box;
        }

        /* Logos según tamaño - Reducidos */
        .logo {
            margin-bottom: 2px;
        }

        .logo.small {
            max-height: 20px;
        }

        .logo.medium {
            max-height: 28px;
            /* Reducido de 35px */
        }

        .logo.large {
            max-height: 40px;
        }

        /* Títulos según tamaño */
        .title {
            font-weight: bold;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
            line-height: 1.1;
        }

        .title.small {
            font-size: 8px;
        }

        .title.medium {
            font-size: 10px;
            /* Reducido de 11px */
        }

        .title.large {
            font-size: 13px;
        }

        /* Código de barras */
        .barcode {
            margin: 2px 0;
            /* Reducido de 5px */
        }

        .barcode img {
            display: block;
            margin: 0 auto;
        }

        /* Códigos */
        .code {
            font-weight: bold;
            margin-top: 1px;
            line-height: 1;
        }

        .code.small {
            font-size: 7px;
        }

        .code.medium {
            font-size: 8px;
            /* Reducido de 9px */
        }

        .code.large {
            font-size: 10px;
        }

        .barcode-text {
            font-size: 6px;
            color: #333;
            margin-top: 1px;
            line-height: 1;
        }

        .barcode-text.medium {
            font-size: 7px;
            /* Reducido de 8px */
        }

        .barcode-text.large {
            font-size: 9px;
        }

        /* Meta información */
        .meta {
            font-size: 6px;
            color: #555;
            margin-top: 2px;
            line-height: 1.1;
        }

        .meta.medium {
            font-size: 7px;
            /* Reducido de 8px */
        }

        .meta.large {
            font-size: 9px;
            line-height: 1.2;
        }

        .divider {
            margin: 0 2px;
        }
    </style>
</head>

<body>
    <div class="container">
        {{-- Logo de Dimak --}}
        @php
            $logoPath = public_path('images/dimak-logo.png');
            $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
            $labelSize = $size ?? 'medium';
        @endphp

        @if($logoData)
            <div class="logo {{ $labelSize }}">
                <img src="data:image/png;base64,{{ $logoData }}" alt="Dimak"
                    style="max-height: 100%; max-width: 120px; object-fit: contain;">
            </div>
        @else
            <div class="logo {{ $labelSize }}" style="font-weight: bold; font-size: 10px;">
                DIMAK
            </div>
        @endif

        {{-- Nombre del activo --}}
        <div class="title {{ $labelSize }}">
            @if($labelSize === 'small')
                {{ Str::limit($asset->nombre, 18) }}
            @elseif($labelSize === 'medium')
                {{ Str::limit($asset->nombre, 25) }}
            @else
                {{ Str::limit($asset->nombre, 35) }}
            @endif
        </div>

        {{-- Código de barras --}}
        <div class="barcode">
            <img src="data:image/png;base64,{{ $barcode }}" alt="Barcode">
        </div>

        {{-- Código interno --}}
        <div class="code {{ $labelSize }}">
            {{ $asset->codigo_interno }}
        </div>

        {{-- Código de barras (texto) --}}
        <div class="barcode-text {{ $labelSize }}">
            {{ $asset->codigo_barra }}
        </div>

        {{-- Información adicional según tamaño --}}
        @if($labelSize === 'medium' || $labelSize === 'large')
            <div class="meta {{ $labelSize }}">
                @if($asset->category)
                    {{ $asset->category->nombre }}
                @endif
                @if($asset->category && $asset->ubicacion)
                    <span class="divider">|</span>
                @endif
                @if($asset->ubicacion)
                    {{ $asset->ubicacion }}
                @endif
            </div>
        @endif

        {{-- Información extendida solo para etiquetas grandes --}}
        @if($labelSize === 'large')
            @if($asset->numero_serie)
                <div class="meta {{ $labelSize }}" style="margin-top: 5px;">
                    N° Serie: {{ $asset->numero_serie }}
                </div>
            @endif
        @endif

    </div>
</body>

</html>