<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas</title>
    <style>
        body {
            text-align: center;
            font-family: Arial, sans-serif;
        }
        h1, h2 {
            margin: 20px 0;
        }
        table {
            margin: 0 auto;
            border-collapse: collapse;
            width: 80%;
        }
        th, td {
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
        }
        img {
            display: block;
            margin: 20px auto;
        }
    </style>
</head>
<body>
    <h1>Reporte de Ventas por Día</h1>

    <!-- Tabla resumen -->
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Cliente</th>
                <th>Fecha</th>
                {{-- <th>Hora</th> --}}
                <th>Estado</th>
                <th>Total (S/)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ventas as $venta)
                <tr>
                    <td>{{ $venta->id }}</td>
                    <td>{{ $venta->cliente->nombreCliente }} {{ $venta->cliente->apellidoCliente }}</td>
                    <td>{{ $venta->created_at->format('Y-m-d') }}</td>
                    {{-- <td>{{ $venta->created_at->format('H:i:s') }}</td> --}}
                    <td>{{ $venta->estadoTransaccion->descripcionET }}</td>
                    <td>{{ $venta->montoTotal }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Gráfico -->
    <h2>Gráfico de Ventas</h2>
   
    <img src="{{ $chartImage }}" alt="Gráfico de Ventas" style="width: 80%;">

</body>
</html>
