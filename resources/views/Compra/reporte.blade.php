<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Reporte de Compras - Eli Boutique</title>
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

        /* ===== ESTADOS ===== */
        .badge {
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
            color: white;
        }

        .badge-pagada {
            background-color: #28a745;
        }

        .badge-pendiente {
            background-color: #ffc107;
            color: #333;
        }

        .badge-anulada {
            background-color: #dc3545;
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
        $totalCompras = $compras->count();
        $montoTotal = $compras->sum('total');
        $sumaSubtotal = $compras->sum('subtotal');
        $sumaIgv = $compras->sum('igv');
        $comprasPagadas = $compras->where('estadoTransaccion.descripcionET', 'Pagada')->count();
        $comprasPendientes = $compras->where('estadoTransaccion.descripcionET', 'Pendiente')->count();

        $colores = ['#667eea', '#764ba2', '#43e97b', '#4facfe', '#fa709a'];

        $totalUnidades = array_sum(array_column($totalesPorCategoria, 'cantidad'));
        $totalMontoCategoria = array_sum(array_column($totalesPorCategoria, 'monto'));
        $maxCantidad = $totalUnidades > 0 ? max(array_column($totalesPorCategoria, 'cantidad')) : 0;
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
                <p><strong>Total registros:</strong> {{ $totalCompras }}</p>
            </div>
        </div>
    </div>

    <!-- TÍTULO -->
    <div class="report-title">
        REPORTE GENERAL DE COMPRAS
    </div>

    <!-- RESUMEN FINANCIERO -->
    <table class="finance-table">
        <tr>
            <td style="background-color: #f0f2ff;">
                <div class="finance-label">Total Compras</div>
                <div class="finance-value" style="color: #667eea;">{{ $totalCompras }}</div>
            </td>
            <td style="background-color: #e8f5e9;">
                <div class="finance-label">Compras Pagadas</div>
                <div class="finance-value" style="color: #28a745;">{{ $comprasPagadas }}</div>
            </td>
            <td style="background-color: #fff3e0;">
                <div class="finance-label">Compras Pendientes</div>
                <div class="finance-value" style="color: #ff9800;">{{ $comprasPendientes }}</div>
            </td>
            <td style="background-color: #fce4ec;">
                <div class="finance-label">Monto Total Invertido</div>
                <div class="finance-value" style="color: #e74c3c;">S/ {{ number_format($montoTotal, 2) }}</div>
            </td>
        </tr>
    </table>

    <!-- DETALLE DE COMPRAS -->
    <div class="section-title">Detalle de Compras</div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Proveedor</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Estado</th>
                <th>Comprobante</th>
                <th>Subtotal</th>
                <th>IGV</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($compras as $compra)
                @php
                    $estado = $compra->estadoTransaccion->descripcionET ?? 'N/A';
                    $badgeClass = match ($estado) {
                        'Pagada' => 'badge-pagada',
                        'Pendiente' => 'badge-pendiente',
                        'Anulada' => 'badge-anulada',
                        default => '',
                    };
                @endphp
                <tr>
                    <td style="font-weight: bold; color: #667eea;">{{ $compra->codigoCompra }}</td>
                    <td style="text-align: left; padding-left: 8px;">{{ $compra->proveedor->nombreProveedor }}
                        {{ $compra->proveedor->apellidoProveedor }}</td>
                    <td>{{ $compra->created_at->format('d/m/Y') }}</td>
                    <td>{{ $compra->created_at->format('h:i A') }}</td>
                    <td><span class="badge {{ $badgeClass }}">{{ $estado }}</span></td>
                    <td>{{ $compra->comprobante->descripcionCOM ?? 'Sin comprobante' }}</td>
                    <td>S/ {{ number_format($compra->subtotal, 2) }}</td>
                    <td>S/ {{ number_format($compra->igv, 2) }}</td>
                    <td style="font-weight: bold;">S/ {{ number_format($compra->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="6" style="text-align: right; padding-right: 10px !important;">TOTALES</td>
                <td>S/ {{ number_format($sumaSubtotal, 2) }}</td>
                <td>S/ {{ number_format($sumaIgv, 2) }}</td>
                <td>S/ {{ number_format($montoTotal, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- GRÁFICO: PRODUCTOS POR CATEGORÍA -->
    <div class="chart-container">
        <div class="section-title">Productos Comprados por Categoría</div>

        <table class="data-table" style="margin-bottom: 16px;">
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th>Unidades Compradas</th>
                    <th>Monto Invertido</th>
                    <th>% del Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($totalesPorCategoria as $categoria => $data)
                    @php $porcentaje = $totalUnidades > 0 ? round(($data['cantidad'] / $totalUnidades) * 100, 1) : 0; @endphp
                    <tr>
                        <td style="text-align: left; padding-left: 8px;">
                            <span
                                style="display: inline-block; width: 10px; height: 10px; background-color: {{ $colores[$loop->index % count($colores)] }}; border-radius: 2px; margin-right: 5px; vertical-align: middle;"></span>
                            {{ $categoria }}
                        </td>
                        <td style="font-weight: bold;">{{ $data['cantidad'] }}</td>
                        <td>S/ {{ number_format($data['monto'], 2) }}</td>
                        <td>{{ $porcentaje }}%</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td style="text-align: right; padding-right: 10px !important;">TOTAL</td>
                    <td>{{ $totalUnidades }}</td>
                    <td>S/ {{ number_format($totalMontoCategoria, 2) }}</td>
                    <td>100%</td>
                </tr>
            </tfoot>
        </table>

        <!-- BARRAS VISUALES -->
        @foreach ($totalesPorCategoria as $categoria => $data)
            @php
                $porcentajeBarra = $maxCantidad > 0 ? ($data['cantidad'] / $maxCantidad) * 100 : 0;
                $color = $colores[$loop->index % count($colores)];
            @endphp
            <div class="chart-bar-row">
                <div class="chart-label">{{ $categoria }}</div>
                <div class="chart-bar-cell">
                    <div class="chart-bar">
                        <div class="chart-bar-inner"
                            style="width: {{ $porcentajeBarra }}%; background-color: {{ $color }};"></div>
                    </div>
                </div>
                <div class="chart-value" style="color: {{ $color }};">{{ $data['cantidad'] }} uds.</div>
            </div>
        @endforeach
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <strong>Eli Boutique</strong> — Reporte de Compras generado el {{ \Carbon\Carbon::now()->format('d/m/Y') }}
        | <span class="page-number"></span>
    </div>
</body>

</html>
