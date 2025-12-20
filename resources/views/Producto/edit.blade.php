@extends('adminlte::page')

@section('title', 'Productos')

@section('content_header')
    <h1>Editar el producto</h1>
@stop

@section('content')
<form action="{{ route('productos.update', $producto->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT') <!-- Necesario para indicar que es una actualización -->

     <!-- Código Producto -->
    <div class="mb-3">
        <label for="codigoP" class="form-label">Código Producto</label>
        <input id="codigoP" name="codigoP" type="text" class="form-control" value="{{ $producto->codigoP }}">
        @error('codigoP')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <!-- Categoría -->
    <div class="mb-3">
        <label for="categoria_producto_id" class="form-label">Categoría</label>
        <select id="categoria_producto_id" name="categoria_producto_id" class="form-control">
            @foreach($categorias as $categoria)
                <option value="{{ $categoria->id }}" {{ $producto->categoria_producto_id == $categoria->id ? 'selected' : '' }}>
                    {{ $categoria->nombreCP }}
                </option>
            @endforeach
        </select>
        @error('categoria_producto_id')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <!-- Imagen actual -->
    <div class="mb-3">
        <label for="imagenP" class="form-label">Imagen actual</label><br>

        @if($producto->imagenP)
            <img src="{{ $producto->imagenP }}" alt="Imagen actual del producto" width="200">
        @else
            <p>No hay imagen</p>
        @endif
    </div>

    <!-- Subir nueva imagen (opcional) -->
    <div class="mb-3">
        <label for="imagenP" class="form-label">Cambiar Imagen</label>
        <input id="imagenP" name="imagenP" type="file" class="form-control">
    </div>

    <!-- Descripción -->
    <div class="mb-3">
        <label for="descripcionP" class="form-label">Descripción</label>
        <textarea id="descripcionP" name="descripcionP" class="form-control" rows="3">{{ $producto->descripcionP }}</textarea>
        @error('descripcionP')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <!-- Precio -->
    <div class="mb-3">
        <label for="precioP" class="form-label">Precio</label>
        <input id="precioP" name="precioP" type="number" step="any" class="form-control" value="{{ $producto->precioP }}">
        @error('precioP')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <!-- Stock -->
    <div class="mb-3">
        <label for="stockP" class="form-label">Stock</label>
        <input id="stockP" name="stockP" type="number" class="form-control" value="{{ $producto->stockP }}">
        @error('stockP')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <a href="{{route('productos.index')}}" class="btn btn-danger">Cancelar</a>
    <button type="submit" class="btn btn-dark">Actualizar Producto</button>
</form>
    
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
