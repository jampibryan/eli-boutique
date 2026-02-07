<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
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
            font-size: 10px;
            color: #333;
            line-height: 1.4;
            padding: 15px 20px;
        }

        /* ===== HEADER ===== */
        .header {
            border-bottom: 3px solid #e67e22;
            padding-bottom: 12px;
            margin-bottom: 5px;
        }

        .header-content {
            display: table;
            width: 100%;
        }

        .logo-section {
            display: table-cell;
            width: 15%;
            vertical-align: middle;
            padding-left: 5px;
        }

        .logo-section img {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e67e22;
        }

        .company-info {
            display: table-cell;
            width: 55%;
            text-align: center;
            vertical-align: middle;
            padding: 0 10px;
        }

        .company-info h1 {
            font-size: 22px;
            color: #e67e22;
            margin-bottom: 3px;
            font-weight: bold;
        }

        .company-info p {
            font-size: 9px;
            color: #666;
            margin: 1px 0;
        }

        .report-info {
            display: table-cell;
            width: 30%;
            text-align: right;
            vertical-align: middle;
            font-size: 9px;
            color: #555;
            padding-right: 5px;
        }

        .report-info p {
            margin: 2px 0;
        }

        .report-info strong {
            color: #333;
        }

        /* ===== TÍTULO ===== */
        .report-title {
            background: linear-gradient(135deg, #e67e22 0%, #e74c3c 100%);
            color: white;
            padding: 9px;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
            border-radius: 5px;
            letter-spacing: 0.5px;
        }

        .date-range {
            text-align: center;
            font-size: 10px;
            color: #555;
            margin-bottom: 12px;
            padding: 5px 0;
            border-bottom: 1px dashed #ccc;
        }

        .date-range strong {
            color: #e67e22;
        }

        /* ===== RESUMEN ===== */
        .summary-box {
            display: table;
            width: 100%;
            margin-bottom: 12px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            overflow: hidden;
        }

        .summary-item {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 8px 5px;
            border-right: 1px solid #e0e0e0;
        }

        .summary-item:last-child {
            border-right: none;
        }

        .summary-item .label {
            font-size: 8px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 3px;
        }

        .summary-item .value {
            font-size: 14px;
            font-weight: bold;
            color: #2c2c2c;
        }

        .summary-item .value.orange {
            color: #e67e22;
        }

        .summary-item .value.green {
            color: #28a745;
        }

        .summary-item .value.red {
            color: #e74c3c;
        }

        /* ===== SECCIÓN ===== */
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #e67e22;
            margin: 10px 0 6px 0;
            padding-bottom: 4px;
            border-bottom: 2px solid #e67e22;
            display: inline-block;
        }

        /* ===== TABLA ===== */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 9px;
        }

        thead th {
            background: #e67e22;
            color: white;
            padding: 7px 4px;
            font-size: 8px;
            font-weight: bold;
            text-align: center;
            border: 1px solid #d35400;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        tbody tr:nth-child(even) {
            background-color: #fef9f4;
        }

        tbody td {
            padding: 5px 4px;
            text-align: center;
            border: 1px solid #dee2e6;
            font-size: 8.5px;
        }

        tbody td.text-left {
            text-align: left;
            padding-left: 6px;
        }

        tbody td.text-right {
            text-align: right;
            padding-right: 6px;
        }

        .badge-productos {
            background: #fef0e0;
            color: #e67e22;
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-estado {
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 7.5px;
            font-weight: bold;
        }

        .badge-pagado {
            background: #d4edda;
            color: #155724;
        }

        .badge-recibido {
            background: #cce5ff;
            color: #004085;
        }

        /* ===== TOTALES ===== */
        .totals-row {
            background: #e67e22 !important;
            color: white;
            font-weight: bold;
        }

        .totals-row td {
            border-color: #d35400;
            color: white;
            padding: 7px 4px;
            font-size: 9px;
        }

        /* ===== GRÁFICO ===== */
        .chart-section {
            margin-top: 15px;
            page-break-inside: avoid;
        }

        .chart-img {
            display: block;
            margin: 8px auto 0 auto;
            border: 2px solid #e67e22;
            border-radius: 8px;
            max-width: 92%;
        }

        /* ===== FOOTER ===== */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #888;
            padding: 8px 20px 5px 20px;
            border-top: 1px solid #dee2e6;
        }

        .footer strong {
            color: #e67e22;
        }

        .page-number:before {
            content: "Página " counter(page);
        }
    </style>
</head>

<body>
    <!-- ===== HEADER ===== -->
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
                <p><strong>Fecha emisión:</strong> {{ date('d/m/Y') }}</p>
                <p><strong>Hora:</strong> {{ date('h:i A') }}</p>
                <p><strong>Usuario:</strong> {{ Auth::user()->name ?? 'Sistema' }}</p>
                <p><strong>Total registros:</strong> {{ $totalRegistros }}</p>
            </div>
        </div>
    </div>

    <!-- ===== TÍTULO ===== -->
    <div class="report-title">
        REPORTE DE COMPRAS {{ $tipo === 'mes' ? 'POR MES' : 'POR DÍA' }}
    </div>

    <div class="date-range">
        Periodo consultado: <strong>{{ $fechaDesde }}</strong> al <strong>{{ $fechaHasta }}</strong>
    </div>

    <!-- ===== RESUMEN ===== -->
    <div class="summary-box">
        <div class="summary-item">
            <div class="label">Total Compras</div>
            <div class="value orange">S/ {{ number_format($totalCompras, 2) }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Subtotal (sin IGV)</div>
            <div class="value">S/ {{ number_format($totalSubtotal, 2) }}</div>
        </div>
        <div class="summary-item">
            <div class="label">IGV Total</div>
            <div class="value red">S/ {{ number_format($totalIGV, 2) }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Productos comprados</div>
            <div class="value green">{{ $totalProductos }}</div>
        </div>
    </div>

    <!-- ===== SECCIÓN 1: DETALLE DE COMPRAS ===== -->
    <div class="section-title">📋 {{ $tipo === 'mes' ? 'Resumen Mensual de Compras' : 'Resumen Diario de Compras' }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:8%;">N°</th>
                <th style="width:18%;">{{ $tipo === 'mes' ? 'Mes' : 'Fecha' }}</th>
                <th style="width:14%;">Cant. Compras</th>
                <th style="width:12%;">Productos</th>
                <th style="width:16%;">Subtotal</th>
                <th style="width:14%;">IGV</th>
                <th style="width:18%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($datosAgrupados as $index => $grupo)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-weight:bold;color:#e67e22;">{{ $grupo['periodo'] }}</td>
                    <td>{{ $grupo['cantidadCompras'] }}</td>
                    <td><span class="badge-productos">{{ $grupo['productos'] }}</span></td>
                    <td class="text-right">S/ {{ number_format($grupo['subtotal'], 2) }}</td>
                    <td class="text-right">S/ {{ number_format($grupo['igv'], 2) }}</td>
                    <td class="text-right" style="font-weight:bold;">S/ {{ number_format($grupo['total'], 2) }}</td>
                </tr>
            @endforeach
            {{-- Fila de totales --}}
            <tr class="totals-row">
                <td colspan="2" style="text-align:right;padding-right:8px;">TOTALES</td>
                <td>{{ $totalRegistros }}</td>
                <td>{{ $totalProductos }}</td>
                <td style="text-align:right;padding-right:6px;">S/ {{ number_format($totalSubtotal, 2) }}</td>
                <td style="text-align:right;padding-right:6px;">S/ {{ number_format($totalIGV, 2) }}</td>
                <td style="text-align:right;padding-right:6px;">S/ {{ number_format($totalCompras, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- ===== SECCIÓN 2: GRÁFICO ===== -->
    <div class="chart-section">
        <div class="section-title">📊 Representación Gráfica</div>
        <img src="{{ $chartImage }}" alt="Gráfico de Compras" class="chart-img">
    </div>

    <!-- ===== FOOTER ===== -->
    <div class="footer">
        <p><strong>Eli Boutique</strong> - Sistema de Gestión | Documento generado el {{ date('d/m/Y') }} a las
            {{ date('h:i A') }}</p>
        <p class="page-number"></p>
    </div>
</body>

</html>
