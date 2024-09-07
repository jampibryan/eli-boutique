@extends('adminlte::page')

@section('title', 'Productos')

@section('content_header')
    <h1>Lista de Productos</h1>
@stop

@section('content')
    <p>Welcome to this beautiful admin panel.</p>
    <a href="{{route('productos.create')}}" class="btn btn-danger d-flex justify-content-center" >CREAR PRODUCTO</a>

    <table class="table table-dark table-striped mt-4">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">IMAGEN</th>
                <th scope="col">CATEGORIA</th>
                <th scope="col">DESCRIPCION</th>
                <th scope="col">PRECIO</th>
                <th scope="col">STOCK</th>
                <th scope="col">ACCIONES</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($productos as $producto)
                <tr>
                    <td>{{ $producto->id }}</td>
                    <td>{{ $producto->categoriaProducto->nombreCP }}</td>
                    <td>
                        <img src="{{ $producto->imagenP }}" alt="Imagen del producto" width="100">

                    </td>
                    <td>{{ $producto->descripcionP }}</td>
                    <td>{{ $producto->precioP }}</td>
                    <td>{{ $producto->stockP }}</td>
                    <td>
                        <a href="{{route('productos.edit', $producto)}}" class="btn btn-info">Editar</a>
                        <form action="{{route('productos.destroy', $producto)}}" method="post">
                            @csrf
                            @method('delete')
                            <button class="btn btn-danger mt-1">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

@stop

@section('js')
    {{-- <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script> --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">
    </script>
@stop
