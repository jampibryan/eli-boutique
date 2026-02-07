<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Reporte de Productos - Eli Boutique</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
            padding: 15px;
        }

        .header {
            margin-top: 15px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
        }

        .header-content {
            display: table;
            width: 100%;
        }

        .logo-section {
            display: table-cell;
            width: 20%;
            vertical-align: middle;
            padding-left: 10px;
        }

        .logo-section img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #667eea;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }

        .company-info {
            display: table-cell;
            width: 60%;
            text-align: center;
            vertical-align: middle;
            padding: 0 15px;
        }

        .company-info h1 {
            font-size: 24px;
            color: #667eea;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .company-info p {
            font-size: 10px;
            color: #666;
            margin: 2px 0;
        }

        .report-info {
            display: table-cell;
            width: 20%;
            text-align: right;
            vertical-align: middle;
            font-size: 9px;
            color: #666;
            padding-right: 10px;
        }

        .report-info strong {
            color: #333;
        }

        .report-title {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 12px;
            border-radius: 5px;
        }

        .category-section {
            margin-top: 25px;
            margin-bottom: 10px;
        }

        .category-title {
            background: #f8f9fa;
            color: #667eea;
            font-size: 14px;
            font-weight: bold;
            padding: 8px 12px;
            border-left: 4px solid #667eea;
            border-radius: 3px;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead {
            background: #667eea;
            color: white;
        }

        thead th {
            padding: 10px 8px;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            border: 1px solid #5568d3;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tbody tr:hover {
            background-color: #e9ecef;
        }

        tbody td {
            padding: 8px 6px;
            text-align: center;
            border: 1px solid #dee2e6;
            font-size: 10px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #666;
            padding-top: 10px;
            border-top: 1px solid #dee2e6;
        }

        .footer strong {
            color: #667eea;
        }

        .page-number:before {
            content: "Página " counter(page);
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <div class="logo-section">
                <img src="{{ public_path('img/logo_eli_boutique.png') }}" alt="Logo">
            </div>
            <div class="company-info">
                <h1>ELI BOUTIQUE</h1>
                <p>Sistema de Gestión Empresarial</p>
                <p>RUC: 20123456789 | Teléfono: 987 654 321</p>
            </div>
            <div class="report-info">
                <p><strong>Fecha:</strong> {{ date('d/m/Y') }}</p>
                <p><strong>Hora:</strong> {{ date('h:i A') }}</p>
                <p><strong>Usuario:</strong> {{ Auth::user()->name ?? 'Sistema' }}</p>
            </div>
        </div>
    </div>

    <!-- Report Title -->
    <div class="report-title">
        🛒 REPORTE DE PRODUCTOS
    </div>

    <!-- Sección por Categoría -->

    @php
        $productosPorCategoria = $productos->groupBy(function ($item) {
            return $item->categoriaProducto->nombreCP ?? 'Sin Categoría';
        });
    @endphp

    @foreach ($productosPorCategoria as $categoria => $productosCat)
        <div class="category-section" style="page-break-inside: avoid;">
            <div class="category-title">{{ $categoria }}</div>
            <table>
                <thead>
                    <tr>
                        <th width="15%">CÓDIGO</th>
                        <th width="35%">DESCRIPCIÓN</th>
                        <th width="15%">PRECIO</th>
                        <th width="15%">STOCK TOTAL</th>
                        <th width="20%">TALLAS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productosCat as $producto)
                        <tr>
                            <td>{{ $producto->codigoP }}</td>
                            <td>{{ $producto->descripcionP }}</td>
                            <td>S/. {{ number_format($producto->precioP, 2) }}</td>
                            <td>
                                {{ $producto->tallaStocks->sum('stock') }}
                            </td>
                            <td>
                                @if ($producto->tallaStocks->count())
                                    @foreach ($producto->tallaStocks as $tallaStock)
                                        <span style="display:inline-block; margin-right:4px;">
                                            {{ $tallaStock->talla->descripcion ?? '-' }} ({{ $tallaStock->stock }})
                                        </span>
                                    @endforeach
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    <!-- Footer -->
    <div class="footer">
        <p><strong>Eli Boutique</strong> - Sistema de Gestión | Generado automáticamente el {{ date('d/m/Y h:i A') }}
        </p>
        <p class="page-number"></p>
    </div>
</body>

</html>
