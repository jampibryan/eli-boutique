@extends('adminlte::page')

@section('title', 'Productos')

@section('content_header')
    <h1>Lista de productos</h1>
@stop

@section('content')

    <div class="d-flex justify-content-between mb-3">
        <a href="{{ route('productos.create') }}" class="btn btn-danger">REGISTRAR PRODUCTO</a>
        <a href="{{ route('productos.pdf') }}" target="_blank" class="btn btn-primary">GENERAR REPORTE</a>
    </div>

    <!-- Formulario de búsqueda por categoría -->
    <form method="GET" class="mb-3">
        <div class="form-group col-md-3"">
            <label for="categoria">Filtrar por Categoría:</label>
            <select name="categoria" id="categoria" class="form-control" onchange="this.form.submit()">
                <option value="">Todas las Categorías</option>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id }}" {{ request('categoria') == $categoria->id ? 'selected' : '' }}>
                        {{ $categoria->nombreCP }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    <div class="row">
        @foreach ($productos as $producto)
            <div class="col-md-6 mb-4">
                <div class="card" style="height: 100%; padding: 10px;">
                    <h5 class="card-title text-center mt-2 mb-2"><strong>{{ $producto->categoriaProducto->nombreCP }}</strong></h5>
                    <img src="{{ $producto->imagenP }}" class="card-img-top" alt="Imagen del producto" style="height: 200px; width: 100%; object-fit: contain;">
                    <div class="card-body p-2">
                        <p class="card-text text-center mb-1"><strong>Código:</strong> {{ $producto->codigoP }}</p>
                        <p class="card-text text-center mb-1"><strong>Descripción:</strong> {{ $producto->descripcionP }}</p>
                        <p class="card-text text-center mb-1"><strong>Precio:</strong> S/. {{ number_format($producto->precioP, 2) }}</p>
                        <p class="card-text text-center mb-2"><strong>Stock:</strong> {{ $producto->stockP }}</p>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('productos.edit', $producto) }}" class="btn btn-info btn-sm">Editar</a>
                            <form action="{{ route('productos.destroy', $producto) }}" method="post">
                                @csrf
                                @method('delete')
                                <button class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">
    </script>
@stop
