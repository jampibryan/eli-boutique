@php
    use Illuminate\Support\Facades\Auth;
    $isFactura = $tipoComprobante === 'Factura';
    $usuario = Auth::user();
@endphp

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isFactura ? 'Factura' : 'Boleta' }} de Venta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .container {
            background-color: #fff;
            padding: 32px 28px 24px 28px;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(102, 126, 234, 0.10);
            max-width: 650px;
            margin: 30px auto;
        }

        h1,
        h2,
        h3 {
            margin: 10px 0;
        }

        .header {
            display: flex;
            align-items: center;
            border-bottom: 3px solid #667eea;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        .header img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 3px solid #667eea;
            margin-right: 24px;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.15);
        }

        .header .info {
            flex: 1;
            text-align: center;
        }

        .header .info h1 {
            color: #667eea;
            margin: 0;
            font-size: 26px;
            letter-spacing: 1px;
            font-weight: bold;
        }

        .header .info p {
            font-size: 12px;
            color: #666;
            margin: 2px 0;
        }

        .header .info .ruc {
            font-size: 11px;
        }

        .header .fecha {
            text-align: right;
            font-size: 11px;
            color: #666;
            min-width: 120px;
        }

        .datos {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 18px;
        }

        .datos .left {
            flex: 1;
        }

        .datos .left .tipo {
            font-size: 15px;
            color: #764ba2;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .datos .left .num {
            font-size: 13px;
            margin-bottom: 4px;
        }

        .datos .left .cliente,
        .datos .left .dni {
            font-size: 12px;
            color: #333;
        }

        .datos .right {
            text-align: right;
            font-size: 12px;
            color: #333;
        }

        .datos .right .user {
            font-weight: bold;
        }

        .datos .right .contacto {
            font-size: 11px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-bottom: 18px;
        }

        thead tr {
            background: #333;
            color: #fff;
        }

        th,
        td {
            padding: 8px 4px;
            text-align: center;
            border: 1px solid #e0e0e0;
        }

        th:first-child {
            border-radius: 6px 0 0 0;
        }

        th:last-child {
            border-radius: 0 6px 0 0;
        }

        tbody tr {
            background: #f8f9fa;
        }

        .total,
        .totales {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            margin-top: 10px;
        }

        .total .monto,
        .totales .monto {
            background: #667eea;
            color: #fff;
            padding: 12px 28px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.10);
            width: 320px;
            text-align: right;
        }

        .totales .base,
        .totales .igv {
            background: #f8f9fa;
            color: #333;
            padding: 8px 24px;
            font-size: 13px;
            border-bottom: 1px solid #e0e0e0;
            width: 320px;
            text-align: right;
        }

        .totales .base {
            border-radius: 8px 8px 0 0;
        }

        .totales .monto {
            border-radius: 0 0 8px 8px;
        }

        .leyenda {
            margin-top: 18px;
            text-align: center;
            font-size: 11px;
            color: #666;
        }
    </style>
</head>

<body style="background: #f4f6fb;">
    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="{{ public_path('img/logo_eli_boutique.png') }}" alt="Logo">
            <div class="info">
                <h1>ELI BOUTIQUE</h1>
                <p>Sistema de Gestión Empresarial</p>
                <p class="ruc">RUC: 20123456789 | Teléfono: 987 654 321</p>
            </div>
            <div class="fecha">
                <p><strong>Fecha:</strong> {{ $venta->created_at->format('d/m/Y') }}</p>
                <p><strong>Hora:</strong> {{ $venta->created_at->format('h:i A') }}</p>
            </div>
        </div>

        <!-- Datos comprobante -->
        <div class="datos">
            <div class="left">
                <div class="tipo">{{ $isFactura ? 'Factura de Venta' : 'Boleta de Venta' }}</div>
                <div class="num">N°: <strong>{{ $venta->codigoVenta }}</strong></div>
                <div class="cliente">{{ $isFactura ? 'Señor(es):' : 'Cliente:' }}
                    <strong>{{ $venta->cliente->nombreCliente }} {{ $venta->cliente->apellidoCliente }}</strong>
                </div>
                <div class="dni">DNI: <strong>{{ $venta->cliente->dniCliente }}</strong></div>
            </div>
            <div class="right">
                <div class="user">Atendido por: {{ $usuario->name }}</div>
                <div class="contacto">Correo: {{ $usuario->email }}</div>
            </div>
        </div>

        <!-- Tabla de productos -->
        <table>
            <thead>
                <tr>
                    <th>Cantidad</th>
                    <th>Descripción del Producto</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($venta->detalles as $detalle)
                    <tr>
                        <td>{{ $detalle->cantidad }}</td>
                        <td>
                            @if ($detalle->producto)
                                {{ $detalle->producto->descripcionP }}
                            @else
                                <em style="color: #999;">Producto eliminado</em>
                            @endif
                        </td>
                        <td>S/. {{ number_format($detalle->precio_unitario, 2) }}</td>
                        <td>S/. {{ number_format($detalle->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totales -->
        @if ($isFactura)
            <div class="totales">
                @php
                    $totalBaseImponible = $venta->detalles->sum(function ($d) {
                        return $d->cantidad * $d->base_imponible;
                    });
                    $totalIGV = $venta->detalles->sum(function ($d) {
                        return $d->cantidad * $d->igv;
                    });
                @endphp
                <div class="base">BASE IMPONIBLE: S/. {{ number_format($totalBaseImponible, 2) }}</div>
                <div class="igv">IGV (18%): S/. {{ number_format($totalIGV, 2) }}</div>
                <div class="monto">TOTAL: S/. {{ number_format($venta->montoTotal, 2) }}</div>
            </div>
            <div class="leyenda">* Los precios mostrados incluyen IGV. Gracias por su preferencia.</div>
        @else
            <div class="total">
                <div class="monto">TOTAL: S/. {{ number_format($venta->montoTotal, 2) }}</div>
            </div>
            <div class="leyenda">* Los precios mostrados incluyen impuestos. Gracias por su compra.</div>
        @endif
    </div>
</body>

</html>
