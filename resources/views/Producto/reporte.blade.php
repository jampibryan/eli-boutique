<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte de Productos - Eli Boutique</title>
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
        <h2>Reporte de Productos</h2>

        <div class="table-responsive">
            <table class="table table-striped table-bordered mx-auto">
                <thead class="table-dark">
                    <tr>
                        <th>CÓDIGO PRODUCTO</th>
                        <th>CATEGORIA</th>
                        <th>DESCRIPCIÓN</th>
                        <th>PRECIO</th>
                        <th>STOCK</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($productos as $producto)
                        <tr>
                            <td>{{$producto->codigoP}}</td>
                            <td>{{$producto->categoriaProducto->nombreCP}}</td>
                            <td>{{$producto->descripcionP}}</td>
                            {{-- <td>{{$producto->precioP}}</td> --}}
                            <td>S/. {{ number_format($producto->precioP, 2) }}</td>
                            <td>{{$producto->stockP}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

