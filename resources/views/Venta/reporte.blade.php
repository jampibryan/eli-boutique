<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte de Ventas - Eli Boutique</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <style>
        .table td, .table th, h1 {
            padding: 8px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container mt-5 text-center">
        <h1>Eli Boutique</h1>
        <h2>Reporte de Ventas</h2>

        <div class="table-responsive">
            <table class="table table-striped table-bordered mx-auto">
                <thead class="table-dark">
                    <tr>
                        <th>CÓDIGO VENTA</th>
                        <th>CLIENTE</th>
                        <th>FECHA</th>
                        <th>HORA</th>
                        <th>COMPROBANTE</th>
                        <th>ESTADO</th>
                        <th>MONTO TOTAL</th>
                    </tr>
                </thead>
                
                <tbody>
                    @foreach ($ventas as $venta)
                        @php
                        // Definir variables temporales para el comprobante y el estado
                        $comprobanteDescripcion = $venta->pago->comprobante->descripcionCOM ?? 'Sin comprobante';
                        $estadoDescripcion = $venta->estadoTransaccion->descripcionET;
                    @endphp

                        <tr>
                            <td>{{ $venta->codigoVenta }}</td>
                            <td>{{ $venta->cliente->nombreCliente}} {{ $venta->cliente->apellidoCliente}}</td>
                            <td>{{ \Carbon\Carbon::parse($venta->created_at)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($venta->created_at)->format('h:i A') }}</td>
                            <td>{{ $comprobanteDescripcion}}</td>
                            <td>{{ $estadoDescripcion }}</td>
                            <td>S/ {{ number_format($venta->montoTotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
