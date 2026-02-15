<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Reporte de Cajas - Eli Boutique</title>
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

        .report-subtitle {
            text-align: center;
            font-size: 10px;
            color: #666;
            margin-top: -12px;
            margin-bottom: 14px;
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
        $totalIngresos = $cajas->sum('ingresoDiario');
        $totalEgresos = $cajas->sum('egresoDiario');
        $totalClientes = $cajas->sum('clientesHoy');
        $totalProductos = $cajas->sum('productosVendidos');
        $totalBalance = $totalIngresos - $totalEgresos;
        $hayGastos = $totalEgresos > 0;
        $totalDias = $cajas->count();

        // Rango de fechas para mostrar
        if (isset($desde) && isset($hasta) && $desde && $hasta) {
            $rangoTexto =
                \Carbon\Carbon::parse($desde)->format('d/m/Y') . ' — ' . \Carbon\Carbon::parse($hasta)->format('d/m/Y');
        } elseif (isset($desde) && $desde) {
            $rangoTexto = 'Desde ' . \Carbon\Carbon::parse($desde)->format('d/m/Y');
        } elseif (isset($hasta) && $hasta) {
            $rangoTexto = 'Hasta ' . \Carbon\Carbon::parse($hasta)->format('d/m/Y');
        } else {
            $rangoTexto = 'Todas las cajas registradas';
        }
    @endphp

    <!-- ======== HEADER ======== -->
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
                <p><strong>Período:</strong> {{ $rangoTexto }}</p>
                <p><strong>Total Cajas:</strong> {{ $totalDias }} días</p>
                <p><strong>Generado:</strong> {{ date('d/m/Y h:i A') }}</p>
                <p><strong>Usuario:</strong> {{ Auth::user()->name ?? 'Sistema' }}</p>
            </div>
        </div>
    </div>

    <!-- ======== TÍTULO ======== -->
    <div class="report-title">
        REPORTE GENERAL DE CAJAS
    </div>
    <p class="report-subtitle">{{ $rangoTexto }}</p>

    <!-- ======== 1. RESUMEN FINANCIERO ======== -->
    <table class="finance-table">
        <tr>
            <td style="width:{{ $hayGastos ? '16%' : '25%' }};">
                <div class="finance-label">Días Operados</div>
                <div class="finance-value" style="color:#667eea;">{{ $totalDias }}</div>
            </td>
            <td style="width:{{ $hayGastos ? '16%' : '25%' }};">
                <div class="finance-label">Total Clientes</div>
                <div class="finance-value" style="color:#667eea;">{{ $totalClientes }}</div>
            </td>
            <td style="width:{{ $hayGastos ? '17%' : '25%' }};">
                <div class="finance-label">Total Productos</div>
                <div class="finance-value" style="color:#667eea;">{{ $totalProductos }}</div>
            </td>
            <td style="width:{{ $hayGastos ? '17%' : '25%' }};">
                <div class="finance-label">Total Ingresos</div>
                <div class="finance-value" style="color:#28a745;">S/ {{ number_format($totalIngresos, 2) }}</div>
            </td>
            @if ($hayGastos)
                <td style="width:17%;">
                    <div class="finance-label">Total Gastos</div>
                    <div class="finance-value" style="color:#e74c3c;">S/ {{ number_format($totalEgresos, 2) }}</div>
                </td>
                <td style="width:17%;">
                    <div class="finance-label">Balance Neto</div>
                    <div class="finance-value" style="color:{{ $totalBalance >= 0 ? '#28a745' : '#e74c3c' }};">
                        S/ {{ number_format($totalBalance, 2) }}
                    </div>
                </td>
            @endif
        </tr>
    </table>

    <!-- ======== 2. GRÁFICO COMPARATIVO (solo si hay gastos) ======== -->
    @if ($hayGastos)
        @php
            $maxVal = max($totalIngresos, $totalEgresos, 1);
            $hIngreso = max(round(($totalIngresos / $maxVal) * 110), 8);
            $hEgreso = max(round(($totalEgresos / $maxVal) * 110), 8);
            $padIngreso = 110 - $hIngreso;
            $padEgreso = 110 - $hEgreso;
        @endphp

        <div class="section-title">Comparativo Total Ingresos vs Gastos</div>

        <div style="width:50%; margin:5px auto 12px auto;">
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="text-align:right; border:none; padding:0 1px 0 0; vertical-align:top;">
                        <div style="width:80px; margin-left:auto; padding-top:{{ $padIngreso }}px;">
                            <div
                                style="text-align:center; font-size:10px; font-weight:bold; color:#28a745; padding-bottom:3px;">
                                S/ {{ number_format($totalIngresos, 2) }}
                            </div>
                            <div style="background-color:#28a745; width:80px; height:{{ $hIngreso }}px;"></div>
                        </div>
                    </td>
                    <td style="text-align:left; border:none; padding:0 0 0 1px; vertical-align:top;">
                        <div style="width:80px; padding-top:{{ $padEgreso }}px;">
                            <div
                                style="text-align:center; font-size:10px; font-weight:bold; color:#e74c3c; padding-bottom:3px;">
                                S/ {{ number_format($totalEgresos, 2) }}
                            </div>
                            <div style="background-color:#e74c3c; width:80px; height:{{ $hEgreso }}px;"></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="border:none; padding:0;">
                        <div style="background-color:#ccc; height:2px; width:164px; margin:0 auto;"></div>
                    </td>
                </tr>
                <tr>
                    <td style="text-align:right; border:none; padding:3px 1px 0 0;">
                        <div style="width:80px; margin-left:auto; text-align:center;">
                            <span style="font-size:9px; font-weight:bold; color:#28a745;">Ingresos</span>
                        </div>
                    </td>
                    <td style="text-align:left; border:none; padding:3px 0 0 1px;">
                        <div style="width:80px; text-align:center;">
                            <span style="font-size:9px; font-weight:bold; color:#e74c3c;">Gastos</span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    @endif

    <!-- ======== 3. DETALLE DE CAJAS ======== -->
    <div class="section-title">Detalle por Día</div>

    @if ($cajas->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:10%;">Código</th>
                    <th style="width:12%;">Fecha</th>
                    <th style="width:10%;">Clientes</th>
                    <th style="width:10%;">Productos</th>
                    <th style="width:16%;">Ingresos</th>
                    @if ($hayGastos)
                        <th style="width:16%;">Gastos</th>
                        <th style="width:16%;">Balance</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($cajas as $caja)
                    @php
                        $balance = $caja->ingresoDiario - $caja->egresoDiario;
                    @endphp
                    <tr>
                        <td style="font-weight:bold; color:#667eea;">{{ $caja->codigoCaja }}</td>
                        <td>{{ \Carbon\Carbon::parse($caja->fecha)->format('d/m/Y') }}</td>
                        <td>{{ $caja->clientesHoy }}</td>
                        <td>{{ $caja->productosVendidos }}</td>
                        <td style="color:#28a745; font-weight:bold;">S/ {{ number_format($caja->ingresoDiario, 2) }}
                        </td>
                        @if ($hayGastos)
                            <td style="color:#e74c3c; font-weight:bold;">S/ {{ number_format($caja->egresoDiario, 2) }}
                            </td>
                            <td style="color:{{ $balance >= 0 ? '#28a745' : '#e74c3c' }}; font-weight:bold;">
                                S/ {{ number_format($balance, 2) }}
                            </td>
                        @endif
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="4" style="text-align:right; padding-right:10px;">TOTALES</td>
                    <td>S/ {{ number_format($totalIngresos, 2) }}</td>
                    @if ($hayGastos)
                        <td>S/ {{ number_format($totalEgresos, 2) }}</td>
                        <td>S/ {{ number_format($totalBalance, 2) }}</td>
                    @endif
                </tr>
            </tbody>
        </table>
    @else
        <p style="text-align:center; color:#aaa; font-style:italic; padding:20px;">
            No se encontraron cajas en el período seleccionado.
        </p>
    @endif

    <!-- ======== FOOTER ======== -->
    <div class="footer">
        <p><strong>Eli Boutique</strong> — Sistema de Gestión Empresarial | Documento generado automáticamente</p>
        <p class="page-number"></p>
    </div>
</body>

</html>
