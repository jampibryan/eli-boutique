@extends('adminlte::page')

@section('title', 'Productos')

@section('content_header')
    <h1>Crear un Producto</h1>
@stop

@section('content')
    <a href="{{route('productos.create')}}" class="btn btn-danger d-flex justify-content-center" >CREAR PRODUCTO</a>
   
    <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data"> <!-- Habilitar la subida de archivos -->
        @csrf

        <!-- Categoría -->
        <div class="mb-3">
            <label for="categoria_producto_id" class="form-label">Categoría</label>
            <select id="categoria_producto_id" name="categoria_producto_id" class="form-control">
                <option value="">Seleccionar categoría</option>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}" {{ old('categoria_producto_id') == $categoria->id ? 'selected' : '' }}>
                        {{ $categoria->nombreCP }}
                    </option>
                @endforeach
            </select>
            @error('categoria_producto_id')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        
        <!-- Imagen -->
        <div class="mb-3">
            <label for="imagenP" class="form-label">Imagen</label>
            <input id="imagenP" name="imagenP" type="file" class="form-control"> <!-- Cambiado a file input -->
            @error('imagenP')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        
        <!-- Descripción -->
        <div class="mb-3">
            <label for="descripcionP" class="form-label">Descripción</label>
            <textarea id="descripcionP" name="descripcionP" class="form-control" rows="3">{{ old('descripcion') }}</textarea>
            @error('descripcionP')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- Precio -->
        <div class="mb-3">
            <label for="precioP" class="form-label">Precio</label>
            <input id="precioP" name="precioP" type="number" step="any" class="form-control" value="{{ old('precio') }}">
            @error('precioP')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- Stock -->
        <div class="mb-3">
            <label for="stockP" class="form-label">Stock</label>
            <input id="stockP" name="stockP" type="number" class="form-control" value="{{ old('stockP') }}">
            @error('stockP')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <button type="submit" class="btn btn-dark">Crear</button>
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
