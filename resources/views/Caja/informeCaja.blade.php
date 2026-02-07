<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Informe Diario de Caja - Eli Boutique</title>
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

        .section-title {
            font-size: 13px;
            color: #667eea;
            font-weight: bold;
            margin: 18px 0 8px 0;
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
                <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($caja->fecha)->format('d/m/Y') }}</p>
                <p><strong>Código Caja:</strong> {{ $caja->codigoCaja }}</p>
            </div>
        </div>
    </div>

    <!-- Report Title -->
    <div class="report-title">
        📊 INFORME DETALLADO DE CAJA
    </div>

    <!-- Resumen de Caja -->
    <div class="section-title">Resumen del Día</div>
    <table>
        <thead>
            <tr>
                <th>Clientes Atendidos</th>
                <th>Productos Vendidos</th>
                <th>Ingreso Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $caja->clientesHoy }}</td>
                <td>{{ $caja->productosVendidos }}</td>
                <td>S/ {{ number_format($caja->ingresoDiario, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Ventas del Día -->
    <div class="section-title">Ventas Realizadas</div>
    <table>
        <thead>
            <tr>
                <th>Código Venta</th>
                <th>Cliente</th>
                <th>Hora</th>
                <th>Monto Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ventas as $venta)
                <tr>
                    <td>{{ $venta->codigoVenta }}</td>
                    <td>{{ $venta->cliente->nombreCliente }} {{ $venta->cliente->apellidoCliente }}</td>
                    <td>{{ \Carbon\Carbon::parse($venta->created_at)->format('h:i A') }}</td>
                    <td>S/ {{ number_format($venta->montoTotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Clientes Atendidos -->
    <div class="section-title">Clientes Atendidos</div>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>DNI</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($clientes as $cliente)
                <tr>
                    <td>{{ $cliente->nombreCliente }} {{ $cliente->apellidoCliente }}</td>
                    <td>{{ $cliente->dniCliente }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Productos Vendidos -->
    <div class="section-title">Productos Vendidos</div>
    <table>
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Cantidad Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($productosVendidos as $producto)
                <tr>
                    <td>{{ $producto->descripcion }}</td>
                    <td>{{ $producto->cantidad }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p><strong>Eli Boutique</strong> - Sistema de Gestión | Generado automáticamente el {{ date('d/m/Y h:i A') }}
        </p>
        <p class="page-number"></p>
    </div>
</body>

</html>
