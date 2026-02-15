<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Reporte de Ventas - Eli Boutique</title>
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

        /* ===== RESUMEN ===== */
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

        /* ===== TABLA ===== */
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

        /* ===== PAGINACIÓN ===== */
        tr { page-break-inside: avoid; }
        thead { display: table-header-group; }
        .finance-table { page-break-inside: avoid; }

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
        $totalVentas = $ventas->count();
        $montoTotal = $ventas->sum('montoTotal');
        $sumaSubtotal = $ventas->sum('subTotal');
        $sumaIgv = $ventas->sum('IGV');
        $ventasPagadas = $ventas->where('estadoTransaccion.descripcionET', 'Pagada')->count();
        $ventasPendientes = $ventas->where('estadoTransaccion.descripcionET', 'Pendiente')->count();
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
                <p><strong>Total registros:</strong> {{ $totalVentas }}</p>
            </div>
        </div>
    </div>

    <!-- TÍTULO -->
    <div class="report-title">
        REPORTE GENERAL DE VENTAS
    </div>

    <!-- RESUMEN FINANCIERO -->
    <table class="finance-table">
        <tr>
            <td style="background-color: #f0f2ff;">
                <div class="finance-label">Total Ventas</div>
                <div class="finance-value" style="color: #667eea;">{{ $totalVentas }}</div>
            </td>
            <td style="background-color: #e8f5e9;">
                <div class="finance-label">Ventas Pagadas</div>
                <div class="finance-value" style="color: #28a745;">{{ $ventasPagadas }}</div>
            </td>
            <td style="background-color: #fff3e0;">
                <div class="finance-label">Ventas Pendientes</div>
                <div class="finance-value" style="color: #ff9800;">{{ $ventasPendientes }}</div>
            </td>
            <td style="background-color: #fce4ec;">
                <div class="finance-label">Monto Total Vendido</div>
                <div class="finance-value" style="color: #e74c3c;">S/ {{ number_format($montoTotal, 2) }}</div>
            </td>
        </tr>
    </table>

    <!-- TABLA DE VENTAS -->
    <table class="data-table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Cliente</th>
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
            @foreach ($ventas as $venta)
                @php
                    $estado = $venta->estadoTransaccion->descripcionET ?? 'N/A';
                    $badgeClass = match($estado) {
                        'Pagada' => 'badge-pagada',
                        'Pendiente' => 'badge-pendiente',
                        'Anulada', 'Anulado' => 'badge-anulada',
                        default => ''
                    };
                @endphp
                <tr>
                    <td style="font-weight: bold; color: #667eea;">{{ $venta->codigoVenta }}</td>
                    <td style="text-align: left; padding-left: 8px;">{{ $venta->cliente->nombreCliente ?? '' }} {{ $venta->cliente->apellidoCliente ?? '' }}</td>
                    <td>{{ $venta->created_at->format('d/m/Y') }}</td>
                    <td>{{ $venta->created_at->format('h:i A') }}</td>
                    <td><span class="badge {{ $badgeClass }}">{{ $estado }}</span></td>
                    <td>{{ $venta->pago->comprobante->descripcionCOM ?? 'Sin comprobante' }}</td>
                    <td>S/ {{ number_format($venta->subTotal, 2) }}</td>
                    <td>S/ {{ number_format($venta->IGV, 2) }}</td>
                    <td style="font-weight: bold;">S/ {{ number_format($venta->montoTotal, 2) }}</td>
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

    <!-- FOOTER -->
    <div class="footer">
        <strong>Eli Boutique</strong> — Reporte de Ventas generado el {{ \Carbon\Carbon::now()->format('d/m/Y') }}
        | <span class="page-number"></span>
    </div>
</body>

</html>
