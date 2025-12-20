<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte de Compras - Eli Boutique</title>
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
        <h2>Reporte de Compras</h2>

        <div class="table-responsive">
            <table class="table table-striped table-bordered mx-auto">
                <thead class="table-dark">
                    <tr>
                        <th>CÓDIGO COMPRA</th>
                        <th>PROVEEDOR</th>
                        <th>FECHA</th>
                        <th>HORA</th>
                        <th>ESTADO</th>
                        <th>COMPROBANTE</th>
                        <th>MONTO TOTAL</th>
                    </tr>
                </thead>
                
                <tbody>
                    @foreach ($compras as $compra)
                        @php
                            // Definir variables temporales para el comprobante y el estado
                            $comprobanteDescripcion = $compra->pago->comprobante->descripcionCOM ?? 'Sin comprobante';
                            $estadoDescripcion = $compra->estadoTransaccion->descripcionET;
                            $pagoCompra = $compra->pago->importe ?? 0;
                        @endphp

                        <tr>
                            <td>{{ $compra->codigoCompra }}</td>
                            <td>{{ $compra->proveedor->nombreProveedor }} {{ $compra->proveedor->apellidoProveedor }}</td>
                            <td>{{ \Carbon\Carbon::parse($compra->created_at)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($compra->created_at)->format('h:i A') }}</td>
                            <td>{{ $estadoDescripcion }}</td>
                            <td>{{ $comprobanteDescripcion }}</td>
                            <td>S/ {{ number_format($pagoCompra, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
