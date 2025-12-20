<!-- resources/views/compra/orden.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden de Compra</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
        }
        .section {
            margin-bottom: 30px;
            padding: 15px;
            border: 1px solid #dee2e6;
            background-color: #ffffff;
        }
        .section-title {
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 10px;
        }
        .left-column, .right-column {
            width: 45%;
            display: inline-block;
            vertical-align: top;
        }
        .right-column {
            float: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Orden de Compra</h1>
    </div>

    <div class="section">
        <div class="section-title">Datos Generales de la Orden de Compra</div>
        <div class="left-column">
            <strong>Compañía:</strong> ELI BOUTIQUE E.I.R.L<br>
            <strong>Contacto:</strong> {{ $colaborador->nombreColab }} {{ $colaborador->apellidosColab }}<br>
            <strong>Domicilio:</strong> Ayacucho #624<br>
            <strong>Ciudad:</strong> Pacanga<br>
            <strong>Código Postal:</strong> 13861
        </div>
        <div class="right-column">
            <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($compra->created_at)->format('d/m/Y') }}<br>
            <strong>Código de Compra:</strong> {{ $compra->codigoCompra }}
        </div>
    </div>

    <div class="section">
        <div class="section-title">Datos del Vendedor y Datos de Envío</div>
        <div class="left-column">
            <strong>Vendedor:</strong><br> <br>
            <strong>Compañía:</strong> {{ $compra->proveedor->nombreEmpresa }}<br>
            <strong>Contacto:</strong> {{ $compra->proveedor->nombreProveedor }} {{ $compra->proveedor->apellidoProveedor }}<br>
            <strong>Dirección:</strong> {{ $compra->proveedor->direccionProveedor }}<br>
            <strong>Correo:</strong> {{ $compra->proveedor->correoProveedor }}
            <strong>Teléfono:</strong> {{ $compra->proveedor->telefonoProveedor }}
        </div>
        <div class="right-column">
            <strong>Enviar a:</strong><br> <br>
            <strong>Compañía:</strong> ELI BOUTIQUE E.I.R.L<br>
            <strong>Domicilio:</strong> Ayacucho #624 - Pacanga<br>
            <strong>Correo:</strong> eliboutique@gmail.com<br>
            <strong>Teléfono:</strong> {{ $colaborador->telefonoColab }}
        </div>
    </div>

    <div class="section">
        <div class="section-title">Productos</div>
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($compra->detalles as $detalle)
                    <tr>
                        <td>{{ $detalle->producto->codigoP }}</td>
                        <td>{{ $detalle->producto->descripcionP }}</td>
                        <td>{{ $detalle->cantidad }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
