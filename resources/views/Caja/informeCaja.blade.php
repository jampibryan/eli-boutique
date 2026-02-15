<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Informe Diario de Caja - Eli Boutique</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

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
        .header-content { display: table; width: 100%; }
        .logo-section {
            display: table-cell;
            width: 15%;
            vertical-align: middle;
        }
        .logo-section img {
            width: 65px; height: 65px;
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
        .company-info p { font-size: 9px; color: #777; margin: 1px 0; }
        .report-info {
            display: table-cell;
            width: 30%;
            text-align: right;
            vertical-align: middle;
            font-size: 9px;
            color: #555;
        }
        .report-info p { margin: 2px 0; }
        .report-info strong { color: #333; }

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
            margin-bottom: 18px;
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
            margin: 16px 0 8px 0;
            border-left: 4px solid #667eea;
            background-color: #f0f2ff;
        }
        .section-title-red {
            font-size: 12px;
            font-weight: bold;
            color: #e74c3c;
            padding: 6px 10px;
            margin: 16px 0 8px 0;
            border-left: 4px solid #e74c3c;
            background-color: #fdf0ef;
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
        table.data-table thead.red th {
            background-color: #e74c3c;
            border-color: #c0392b;
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
        .total-row-red td {
            background-color: #e74c3c !important;
            color: white !important;
            font-weight: bold;
            padding: 7px 5px !important;
            border-color: #c0392b !important;
            font-size: 10px !important;
        }
        .no-data {
            text-align: center;
            color: #aaa;
            font-style: italic;
            padding: 12px;
            font-size: 10px;
        }

        /* ===== GRÁFICO ===== */
        .chart-container {
            width: 70%;
            margin: 5px auto 10px auto;
        }
        .chart-bar-cell {
            vertical-align: bottom;
            text-align: center;
            border: none !important;
            padding: 0 15px !important;
            height: 130px;
        }
        .chart-label-cell {
            text-align: center;
            border: none !important;
            padding: 5px 15px 0 15px !important;
            font-size: 9px;
            font-weight: bold;
        }

        /* ===== LAYOUT 2 COLUMNAS ===== */
        .two-cols { display: table; width: 100%; margin-bottom: 10px; }
        .col-left { display: table-cell; width: 48%; vertical-align: top; padding-right: 10px; }
        .col-right { display: table-cell; width: 48%; vertical-align: top; padding-left: 10px; }

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
        .footer strong { color: #667eea; }
        .page-number:before { content: "Página " counter(page); }
    </style>
</head>

<body>
    @php
        $hayGastos = $caja->egresoDiario > 0;
        $fechaFormateada = \Carbon\Carbon::parse($caja->fecha);
        $diaSemana = $fechaFormateada->locale('es')->isoFormat('dddd');
        $fechaLarga = $fechaFormateada->format('d/m/Y');
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
                <p><strong>Caja:</strong> {{ $caja->codigoCaja }}</p>
                <p><strong>Fecha:</strong> {{ ucfirst($diaSemana) }}, {{ $fechaLarga }}</p>
                <p><strong>Generado:</strong> {{ date('d/m/Y h:i A') }}</p>
                <p><strong>Usuario:</strong> {{ Auth::user()->name ?? 'Sistema' }}</p>
            </div>
        </div>
    </div>

    <!-- ======== TÍTULO ======== -->
    <div class="report-title">
        INFORME DIARIO DE CAJA — {{ $caja->codigoCaja }}
    </div>

    <!-- ======== 1. RESUMEN FINANCIERO ======== -->
    <table class="finance-table">
        <tr>
            <td style="width:{{ $hayGastos ? '20%' : '33%' }};">
                <div class="finance-label">Clientes Atendidos</div>
                <div class="finance-value" style="color:#667eea;">{{ $caja->clientesHoy }}</div>
            </td>
            <td style="width:{{ $hayGastos ? '20%' : '34%' }};">
                <div class="finance-label">Productos Vendidos</div>
                <div class="finance-value" style="color:#667eea;">{{ $caja->productosVendidos }}</div>
            </td>
            <td style="width:{{ $hayGastos ? '20%' : '33%' }};">
                <div class="finance-label">Ingresos del Día</div>
                <div class="finance-value" style="color:#28a745;">S/ {{ number_format($caja->ingresoDiario, 2) }}</div>
            </td>
            @if ($hayGastos)
            <td style="width:20%;">
                <div class="finance-label">Gastos del Día</div>
                <div class="finance-value" style="color:#e74c3c;">S/ {{ number_format($caja->egresoDiario, 2) }}</div>
            </td>
            <td style="width:20%;">
                <div class="finance-label">Balance Neto</div>
                <div class="finance-value" style="color:{{ $caja->balanceDiario >= 0 ? '#28a745' : '#e74c3c' }};">
                    S/ {{ number_format($caja->balanceDiario, 2) }}
                </div>
            </td>
            @endif
        </tr>
    </table>

    <!-- ======== 2. GRÁFICO COMPARATIVO (solo si hay gastos) ======== -->
    @if ($hayGastos)
    @php
        $ingreso = $caja->ingresoDiario;
        $egreso = $caja->egresoDiario;
        $maxVal = max($ingreso, $egreso, 1);
        // Altura máxima de barra: 110px
        $hIngreso = max(round(($ingreso / $maxVal) * 110), 8);
        $hEgreso = max(round(($egreso / $maxVal) * 110), 8);
        // Padding superior para alinear abajo: 110 - altura
        $padIngreso = 110 - $hIngreso;
        $padEgreso = 110 - $hEgreso;
    @endphp

    <div class="section-title">Comparativo Ingresos vs Gastos</div>

    <div style="width:50%; margin:5px auto 12px auto;">
        <table style="width:100%; border-collapse:collapse;">
            {{-- Barras con valor encima, juntas al centro --}}
            <tr>
                <td style="text-align:right; border:none; padding:0 1px 0 0; vertical-align:top;">
                    <div style="width:80px; margin-left:auto; padding-top:{{ $padIngreso }}px;">
                        <div style="text-align:center; font-size:10px; font-weight:bold; color:#28a745; padding-bottom:3px;">
                            S/ {{ number_format($ingreso, 2) }}
                        </div>
                        <div style="background-color:#28a745; width:80px; height:{{ $hIngreso }}px;"></div>
                    </div>
                </td>
                <td style="text-align:left; border:none; padding:0 0 0 1px; vertical-align:top;">
                    <div style="width:80px; padding-top:{{ $padEgreso }}px;">
                        <div style="text-align:center; font-size:10px; font-weight:bold; color:#e74c3c; padding-bottom:3px;">
                            S/ {{ number_format($egreso, 2) }}
                        </div>
                        <div style="background-color:#e74c3c; width:80px; height:{{ $hEgreso }}px;"></div>
                    </div>
                </td>
            </tr>
            {{-- Línea base --}}
            <tr>
                <td colspan="2" style="border:none; padding:0;">
                    <div style="background-color:#ccc; height:2px; width:164px; margin:0 auto;"></div>
                </td>
            </tr>
            {{-- Leyendas pegadas --}}
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

    <!-- ======== 3. DETALLE DE VENTAS (INGRESOS) ======== -->
    <div class="section-title">Ventas Realizadas — Ingresos</div>
    @if ($ventas->count() > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:7%;">N°</th>
                <th style="width:15%;">Código</th>
                <th style="width:30%;">Cliente</th>
                <th style="width:12%;">Hora</th>
                <th style="width:10%;">Cant.</th>
                <th style="width:16%;">Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ventas as $index => $venta)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td style="font-weight:bold; color:#667eea;">{{ $venta->codigoVenta }}</td>
                <td style="text-align:left; padding-left:8px;">{{ $venta->cliente->nombreCliente ?? '' }} {{ $venta->cliente->apellidoCliente ?? '' }}</td>
                <td>{{ \Carbon\Carbon::parse($venta->created_at)->format('h:i A') }}</td>
                <td>{{ $venta->detalles->sum('cantidad') }}</td>
                <td style="font-weight:bold;">S/ {{ number_format($venta->montoTotal, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" style="text-align:right; padding-right:10px;">TOTAL INGRESOS</td>
                <td>{{ $ventas->sum(fn($v) => $v->detalles->sum('cantidad')) }}</td>
                <td>S/ {{ number_format($caja->ingresoDiario, 2) }}</td>
            </tr>
        </tbody>
    </table>
    @else
        <p class="no-data">No se registraron ventas este día.</p>
    @endif

    <!-- ======== 4. DETALLE DE COMPRAS (GASTOS) — solo si hay ======== -->
    @if ($hayGastos)
    <div style="page-break-inside: avoid;">
    <div class="section-title-red">Compras Pagadas — Gastos</div>
    @if ($compras->count() > 0)
    <table class="data-table">
        <thead class="red">
            <tr>
                <th style="width:7%;">N°</th>
                <th style="width:15%;">Código</th>
                <th style="width:30%;">Proveedor</th>
                <th style="width:12%;">Hora</th>
                <th style="width:10%;">Cant.</th>
                <th style="width:16%;">Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($compras as $index => $compra)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td style="font-weight:bold; color:#e74c3c;">{{ $compra->codigoCompra }}</td>
                <td style="text-align:left; padding-left:8px;">{{ $compra->proveedor->nombreEmpresa ?? 'N/A' }}</td>
                <td>{{ $compra->pago ? \Carbon\Carbon::parse($compra->pago->created_at)->format('h:i A') : '-' }}</td>
                <td>{{ $compra->detalles->sum('cantidad') }}</td>
                <td style="font-weight:bold;">S/ {{ number_format($compra->pago ? $compra->pago->importe : 0, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row-red">
                <td colspan="4" style="text-align:right; padding-right:10px;">TOTAL GASTOS</td>
                <td>{{ $compras->sum(fn($c) => $c->detalles->sum('cantidad')) }}</td>
                <td>S/ {{ number_format($caja->egresoDiario, 2) }}</td>
            </tr>
        </tbody>
    </table>
    @else
        <p class="no-data">No se registraron compras pagadas este día.</p>
    @endif
    </div>
    @endif

    <!-- ======== 5. CLIENTES Y PRODUCTOS (2 columnas) ======== -->
    <div class="two-cols">
        <div class="col-left">
            <div class="section-title" style="display:block;">Clientes Atendidos ({{ $clientes->count() }})</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:12%;">N°</th>
                        <th style="width:55%;">Nombre</th>
                        <th style="width:33%;">DNI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clientes as $index => $cliente)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="text-align:left; padding-left:6px;">{{ $cliente->nombreCliente }} {{ $cliente->apellidoCliente }}</td>
                        <td>{{ $cliente->dniCliente }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="col-right">
            <div class="section-title" style="display:block;">Productos Vendidos ({{ $productosVendidos->count() }})</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:12%;">N°</th>
                        <th style="width:58%;">Producto</th>
                        <th style="width:30%;">Cant.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productosVendidos->sortByDesc('cantidad') as $index => $producto)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td style="text-align:left; padding-left:6px;">{{ $producto->descripcion }}</td>
                        <td style="font-weight:bold;">{{ $producto->cantidad }}</td>
                    </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="2" style="text-align:right; padding-right:8px;">TOTAL</td>
                        <td>{{ $productosVendidos->sum('cantidad') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ======== FOOTER ======== -->
    <div class="footer">
        <p><strong>Eli Boutique</strong> — Sistema de Gestión Empresarial | Documento generado automáticamente</p>
        <p class="page-number"></p>
    </div>
</body>

</html>
