<!DOCTYPE html>
<html lang="es">

<head>
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
            padding: 20px 25px;
        }

        /* ===== HEADER ===== */
        .header {
            border-bottom: 3px solid #667eea;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        .header-content {
            display: table;
            width: 100%;
        }

        .logo-section {
            display: table-cell;
            width: 15%;
            vertical-align: middle;
        }

        .logo-section img {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            border: 2px solid #667eea;
        }

        .company-info {
            display: table-cell;
            width: 55%;
            text-align: center;
            vertical-align: middle;
        }

        .company-info h1 {
            font-size: 22px;
            color: #667eea;
            margin-bottom: 3px;
        }

        .company-info p {
            font-size: 9px;
            color: #777;
            margin: 1px 0;
        }

        .report-info {
            display: table-cell;
            width: 30%;
            text-align: right;
            vertical-align: middle;
            font-size: 9px;
            color: #555;
        }

        .report-info p {
            margin: 2px 0;
        }

        .report-info strong {
            color: #333;
        }

        /* ===== TÍTULO ===== */
        .report-title {
            background-color: #667eea;
            color: white;
            padding: 8px 0;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        /* ===== RESUMEN FINANCIERO ===== */
        .finance-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .finance-table td {
            text-align: center;
            padding: 10px 5px;
            border: 1px solid #e0e0e0;
        }

        .finance-label {
            font-size: 8px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .finance-value {
            font-size: 14px;
            font-weight: bold;
            margin-top: 2px;
        }

        /* ===== SECCIÓN ===== */
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #667eea;
            padding: 6px 10px;
            margin: 14px 0 8px 0;
            border-left: 4px solid #667eea;
            background-color: #f0f2ff;
        }

        /* ===== CATEGORÍA ===== */
        .category-title {
            font-size: 11px;
            font-weight: bold;
            color: #667eea;
            padding: 5px 10px;
            margin: 12px 0 6px 0;
            border-left: 3px solid #764ba2;
            background-color: #faf5ff;
        }

        /* ===== TABLAS ===== */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        table.data-table thead th {
            background-color: #667eea;
            color: white;
            padding: 7px 5px;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
            border: 1px solid #5568d3;
            text-transform: uppercase;
        }

        table.data-table tbody td {
            padding: 5px 4px;
            text-align: center;
            border: 1px solid #dee2e6;
            font-size: 9.5px;
        }

        table.data-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .total-row td {
            background-color: #667eea !important;
            color: white !important;
            font-weight: bold;
            padding: 7px 5px !important;
            border-color: #5568d3 !important;
            font-size: 10px !important;
        }

        /* ===== PAGINACIÓN ===== */
        tr { page-break-inside: avoid; }
        thead { display: table-header-group; }
        .finance-table { page-break-inside: avoid; }
        .section-title { page-break-after: avoid; }
        .category-title { page-break-after: avoid; }

        /* ===== STOCK BADGES ===== */
        .stock-alto {
            color: #28a745;
            font-weight: bold;
        }

        .stock-medio {
            color: #ff9800;
            font-weight: bold;
        }

        .stock-bajo {
            color: #dc3545;
            font-weight: bold;
        }

        .talla-tag {
            display: inline-block;
            background-color: #f0f2ff;
            border: 1px solid #d0d5f5;
            border-radius: 3px;
            padding: 1px 5px;
            margin: 1px 2px;
            font-size: 8px;
            color: #444;
        }

        /* ===== GRÁFICO DE BARRAS ===== */
        .chart-container {
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .chart-bar-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .chart-label {
            display: table-cell;
            width: 28%;
            text-align: right;
            padding-right: 10px;
            vertical-align: middle;
            font-size: 10px;
            font-weight: bold;
            color: #444;
        }

        .chart-bar-cell {
            display: table-cell;
            width: 55%;
            vertical-align: middle;
        }

        .chart-bar {
            height: 28px;
            border-radius: 4px;
        }

        .chart-bar-inner {
            height: 100%;
            border-radius: 4px;
            display: block;
        }

        .chart-value {
            display: table-cell;
            width: 17%;
            text-align: left;
            padding-left: 10px;
            vertical-align: middle;
            font-size: 10px;
            font-weight: bold;
            color: #333;
        }

        /* ===== FOOTER ===== */
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #999;
            padding-top: 8px;
            border-top: 1px solid #e0e0e0;
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
    @php
        $totalProductos = $productos->count();
        $stockTotal = $productos->sum(fn($p) => $p->tallaStocks->sum('stock'));
        $valorInventario = $productos->sum(fn($p) => $p->tallaStocks->sum('stock') * $p->precioP);
        $totalCategorias = count($stockPorCategoria);

        $productosPorCategoria = $productos->groupBy(fn($p) => $p->categoriaProducto->nombreCP ?? 'Sin categoría');

        $colores = ['#667eea', '#764ba2', '#43e97b', '#4facfe', '#fa709a'];

        $totalStockCategoria = array_sum(array_column($stockPorCategoria, 'stock'));
        $totalValorCategoria = array_sum(array_column($stockPorCategoria, 'valorInventario'));
        $maxStock = $totalStockCategoria > 0 ? max(array_column($stockPorCategoria, 'stock')) : 0;
    @endphp

    <!-- HEADER -->
    <div class="header">
        <div class="header-content">
            <div class="logo-section">
                <img src="{{ public_path('img/logo_eli_boutique.png') }}" alt="Logo">
            </div>
            <div class="company-info">
                <h1>Eli Boutique</h1>
                <p>RUC: 20612345678 | Av. Moda 456, Lima</p>
                <p>Tel: (01) 555-8899 | ventas@eliboutique.pe</p>
            </div>
            <div class="report-info">
                <p><strong>Reporte generado:</strong></p>
                <p>{{ \Carbon\Carbon::now()->format('d/m/Y h:i A') }}</p>
                <p><strong>Total productos:</strong> {{ $totalProductos }}</p>
            </div>
        </div>
    </div>

    <!-- TÍTULO -->
    <div class="report-title">
        REPORTE GENERAL DE PRODUCTOS
    </div>

    <!-- RESUMEN -->
    <table class="finance-table">
        <tr>
            <td style="background-color: #f0f2ff;">
                <div class="finance-label">Total Productos</div>
                <div class="finance-value" style="color: #667eea;">{{ $totalProductos }}</div>
            </td>
            <td style="background-color: #e8f5e9;">
                <div class="finance-label">Categorías</div>
                <div class="finance-value" style="color: #28a745;">{{ $totalCategorias }}</div>
            </td>
            <td style="background-color: #fff3e0;">
                <div class="finance-label">Stock Total (uds)</div>
                <div class="finance-value" style="color: #ff9800;">{{ $stockTotal }}</div>
            </td>
            <td style="background-color: #fce4ec;">
                <div class="finance-label">Valor del Inventario</div>
                <div class="finance-value" style="color: #e74c3c;">S/ {{ number_format($valorInventario, 2) }}</div>
            </td>
        </tr>
    </table>

    <!-- DETALLE POR CATEGORÍA -->
    <div class="section-title">Detalle de Productos por Categoría</div>

    @foreach ($productosPorCategoria as $categoria => $productosCat)
        <div style="page-break-inside: avoid;">
        <div class="category-title">{{ $categoria }} ({{ $productosCat->count() }} productos)</div>

        <table class="data-table">
            <thead>
                <tr>
                    <th width="10%">Código</th>
                    <th width="28%">Descripción</th>
                    <th width="12%">Género</th>
                    <th width="10%">Precio</th>
                    <th width="10%">Stock</th>
                    <th width="30%">Tallas (stock)</th>
                </tr>
            </thead>
            <tbody>
                @php $subtotalStock = 0; @endphp
                @foreach ($productosCat as $producto)
                    @php
                        $stockProducto = $producto->tallaStocks->sum('stock');
                        $subtotalStock += $stockProducto;
                        $stockClass = $stockProducto >= 20 ? 'stock-alto' : ($stockProducto >= 10 ? 'stock-medio' : 'stock-bajo');
                    @endphp
                    <tr>
                        <td style="font-weight: bold; color: #667eea;">{{ $producto->codigoP }}</td>
                        <td style="text-align: left; padding-left: 8px;">{{ $producto->descripcionP }}</td>
                        <td>{{ $producto->productoGenero->descripcionPG ?? '-' }}</td>
                        <td>S/ {{ number_format($producto->precioP, 2) }}</td>
                        <td class="{{ $stockClass }}">{{ $stockProducto }}</td>
                        <td style="text-align: left; padding-left: 6px;">
                            @if ($producto->tallaStocks->count())
                                @foreach ($producto->tallaStocks as $ts)
                                    <span class="talla-tag">{{ $ts->talla->descripcion ?? '-' }}: {{ $ts->stock }}</span>
                                @endforeach
                            @else
                                <span style="color: #999;">Sin tallas</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" style="text-align: right; padding-right: 10px !important;">SUBTOTAL {{ strtoupper($categoria) }}</td>
                    <td>{{ $subtotalStock }} uds.</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        </div>
    @endforeach

    <!-- GRÁFICO: STOCK POR CATEGORÍA -->
    <div class="chart-container">
        <div class="section-title">Stock Total por Categoría</div>

        <table class="data-table" style="margin-bottom: 16px;">
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th>Productos</th>
                    <th>Stock Total</th>
                    <th>Valor Inventario</th>
                    <th>% del Stock</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stockPorCategoria as $categoria => $data)
                    @php $porcentaje = $totalStockCategoria > 0 ? round(($data['stock'] / $totalStockCategoria) * 100, 1) : 0; @endphp
                    <tr>
                        <td style="text-align: left; padding-left: 8px;">
                            <span style="display: inline-block; width: 10px; height: 10px; background-color: {{ $colores[$loop->index % count($colores)] }}; border-radius: 2px; margin-right: 5px; vertical-align: middle;"></span>
                            {{ $categoria }}
                        </td>
                        <td>{{ $data['productos'] }}</td>
                        <td style="font-weight: bold;">{{ $data['stock'] }}</td>
                        <td>S/ {{ number_format($data['valorInventario'], 2) }}</td>
                        <td>{{ $porcentaje }}%</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td style="text-align: right; padding-right: 10px !important;">TOTAL</td>
                    <td>{{ $totalProductos }}</td>
                    <td>{{ $totalStockCategoria }}</td>
                    <td>S/ {{ number_format($totalValorCategoria, 2) }}</td>
                    <td>100%</td>
                </tr>
            </tfoot>
        </table>

        <!-- BARRAS VISUALES -->
        @foreach ($stockPorCategoria as $categoria => $data)
            @php
                $porcentajeBarra = $maxStock > 0 ? ($data['stock'] / $maxStock) * 100 : 0;
                $color = $colores[$loop->index % count($colores)];
            @endphp
            <div class="chart-bar-row">
                <div class="chart-label">{{ $categoria }}</div>
                <div class="chart-bar-cell">
                    <div class="chart-bar">
                        <div class="chart-bar-inner" style="width: {{ $porcentajeBarra }}%; background-color: {{ $color }};"></div>
                    </div>
                </div>
                <div class="chart-value" style="color: {{ $color }};">{{ $data['stock'] }} uds.</div>
            </div>
        @endforeach
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <strong>Eli Boutique</strong> — Reporte de Productos generado el {{ \Carbon\Carbon::now()->format('d/m/Y') }}
        | <span class="page-number"></span>
    </div>
</body>

</html>
