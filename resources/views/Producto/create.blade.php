@extends('adminlte::page')

@section('title', 'Productos')

@section('content_header')
    <h1>Registrar un Producto</h1>
@stop

@section('content')
    <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
        <!-- Habilitar la subida de archivos -->
        @csrf

        <!-- Código Producto -->
        <div class="mb-3">
            <label for="codigoP" class="form-label">Código Producto</label>
            <input id="codigoP" name="codigoP" type="text" class="form-control" value="{{ old('codigoP') }}">
            @error('codigoP')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- Categoría -->
        <div class="mb-3">
            <label for="categoria_producto_id" class="form-label">Categoría</label>
            <select id="categoria_producto_id" name="categoria_producto_id" class="form-control">
                <option value="">Seleccionar categoría</option>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id }}"
                        {{ old('categoria_producto_id') == $categoria->id ? 'selected' : '' }}>
                        {{ $categoria->nombreCP }}
                    </option>
                @endforeach
            </select>
            @error('categoria_producto_id')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- Género -->
        <div class="mb-3">
            <label for="producto_genero_id" class="form-label">Género</label>
            <select id="producto_genero_id" name="producto_genero_id" class="form-control">
                <option value="">Seleccionar género</option>
                @foreach ($generos as $genero)
                    <option value="{{ $genero->id }}"
                        {{ old('producto_genero_id') == $genero->id ? 'selected' : '' }}>
                        {{ $genero->descripcion }}
                    </option>
                @endforeach
            </select>
            @error('producto_genero_id')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- Talla -->
        <div class="mb-3">
            <label for="producto_talla_id" class="form-label">Talla</label>
            <select id="producto_talla_id" name="producto_talla_id" class="form-control">
                <option value="">Seleccionar talla</option>
                @foreach ($tallas as $talla)
                    <option value="{{ $talla->id }}"
                        {{ old('producto_talla_id') == $talla->id ? 'selected' : '' }}>
                        {{ $talla->descripcion }}
                    </option>
                @endforeach
            </select>
            @error('producto_talla_id')
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
            <input id="precioP" name="precioP" type="number" step="any" class="form-control"
                value="{{ old('precio') }}">
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
        
        <a href="{{ route('productos.index') }}" class="btn btn-danger">Cancelar</a>
        <button type="submit" class="btn btn-dark">Crear Producto</button>

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

