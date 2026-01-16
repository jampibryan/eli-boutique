<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Centrar verticalmente */
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 90%; /* Ancho responsivo */
            max-width: 600px; /* Ancho máximo */
            text-align: center; /* Centrar texto */
        }

        h1, h2, h3 {
            margin: 10px 0;
        }

        .client-info, .product-table, .footer {
            margin-top: 20px;
            border: 1px solid #000; /* Borde para sección */
            padding: 10px;
            border-radius: 8px;
            background-color: #f9f9f9; /* Color de fondo ligero */
        }

        .date-table {
            margin: auto; /* Centrar el contenedor de la tabla */
            width: 100%; /* Ancho completo */
        }

        .date-table table {
            margin: auto; /* Centrar la tabla */
            width: auto; /* Ancho automático para centrar */
            border-collapse: collapse; /* Colapsar bordes */
            text-align: center; /* Centrar contenido de celdas */
        }

        th, td {
            border: 1px solid #000; /* Borde de celdas */
            text-align: center; /* Centrar contenido de celdas */
            padding: 8px;
        }

        .table-title {
            font-size: 18px;
            margin-bottom: 10px;
            font-weight: bold; /* Negrita */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Eli Boutique</h1>
        <p>De: {{ $colaborador->nombreColab }} {{ $colaborador->apellidosColab }}</p>
        <p>Telf: {{ $colaborador->telefonoColab }}</p>
        <p>Correo: {{ $colaborador->correoColab }}</p>

        <div class="client-info">
            <h2>Factura de Venta</h2>
            <h3>Número de Factura: {{ $venta->codigoVenta }}</h3>
            <p>Señor(es): {{ $venta->cliente->nombreCliente }} {{ $venta->cliente->apellidoCliente }}</p>
            <p>DNI: {{ $venta->cliente->dniCliente }}</p>

            <div class="date-table">
                <table>
                    <tr>
                        <th>Día</th>
                        <th>Mes</th>
                        <th>Año</th>
                    </tr>
                    <tr>
                        <td>{{ $venta->created_at->format('d') }}</td>
                        <td>{{ $venta->created_at->format('m') }}</td>
                        <td>{{ $venta->created_at->format('Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="product-table">
            <div class="table-title">Productos Vendidos</div>
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
                        <td>{{ $detalle->producto->descripcionP }}</td>
                        <td>S/. {{ number_format($detalle->precio_unitario, 2) }}</td>
                        <td>S/. {{ number_format($detalle->subtotal, 2) }} </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="footer">
            @php
                $totalBaseImponible = $venta->detalles->sum(function($d) { return $d->cantidad * $d->base_imponible; });
                $totalIGV = $venta->detalles->sum(function($d) { return $d->cantidad * $d->igv; });
            @endphp
            <p>BASE IMPONIBLE: S/. {{ number_format($totalBaseImponible, 2) }}</p>
            <p>IGV (18%): S/. {{ number_format($totalIGV, 2) }}</p>
            <p><strong>TOTAL A PAGAR: S/. {{ number_format($venta->montoTotal, 2) }}</strong></p>
            <p style="font-size: 11px; margin-top: 10px; color: #666;">
                * Los precios mostrados incluyen IGV
            </p>
        </div>
    </div>
</body>
</html>
