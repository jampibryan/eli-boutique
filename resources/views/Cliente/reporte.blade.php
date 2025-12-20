<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte de Clientes - Eli Boutique</title>
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
        <h2>Reporte de Clientes</h2>

        <div class="table-responsive">
            <table class="table table-striped table-bordered mx-auto">
                <thead class="table-dark">
                    <tr>
                        <th>CÓDIGO</th>
                        <th>NOMBRE</th>
                        <th>APELLIDOS</th>
                        {{-- <th>GÉNERO</th> --}}
                        <th>DNI</th>
                        <th>EMAIL</th>
                        <th>TELÉFONO</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clientes as $cliente)
                        <tr>
                            <td>{{$cliente->id}}</td>
                            <td>{{$cliente->nombreCliente}}</td>
                            <td>{{$cliente->apellidoCliente}}</td>
                            {{-- <td>{{$cliente->tipoGenero->descripcionTG}}</td> --}}
                            <td>{{$cliente->dniCliente}}</td>
                            <td>{{$cliente->correoCliente}}</td>
                            <td>{{$cliente->telefonoCliente}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
