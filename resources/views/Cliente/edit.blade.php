@extends('adminlte::page')

@section('title', 'Clientes')

@section('content_header')
    <h1>Editar un Cliente</h1>
@stop

@section('content')
<form action="{{ route('clientes.update', $cliente->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT') <!-- Necesario para indicar que es una actualización -->

    <!-- Nombre -->
    <div class="mb-3">
        <label for="nombreCliente" class="form-label">Nombre</label>
        <input id="nombreCliente" name="nombreCliente" type="text" class="form-control" value="{{ $cliente->nombreCliente }}">
        @error('nombreCliente')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <!-- Apellidos -->
    <div class="mb-3">
        <label for="apellidoCliente" class="form-label">Apellidos</label>
        <input id="apellidoCliente" name="apellidoCliente" type="text" class="form-control" value="{{ $cliente->apellidoCliente }}">
        @error('apellidoCliente')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    
    <!-- Tipo Genero -->
    <div class="mb-3">
        <label for="tipo_genero_id" class="form-label">Género</label>
        <select id="tipo_genero_id" name="tipo_genero_id" class="form-control">
            <option value="">Seleccionar género</option>
            @foreach($generos as $genero)
                <option value="{{ $genero->id }}" {{ $cliente->tipo_genero_id == $genero->id ? 'selected' : '' }}>
                    {{ $genero->descripcionTG }}
                </option>
            @endforeach
        </select>
        @error('tipo_genero_id')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <!-- DNI -->
    <div class="mb-3">
        <label for="dniCliente" class="form-label">DNI</label>
        <input id="dniCliente" name="dniCliente" type="text" class="form-control" value="{{ $cliente->dniCliente }}">
        @error('dniCliente')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <!-- Correo -->
    <div class="mb-3">
        <label for="correoCliente" class="form-label">Correo</label>
        <input id="correoCliente" name="correoCliente" type="email" class="form-control" value="{{ $cliente->correoCliente }}">
        @error('correoCliente')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <!-- Teléfono -->
    <div class="mb-3">
        <label for="telefonoCliente" class="form-label">Teléfono</label>
        <input id="telefonoCliente" name="telefonoCliente" type="text" class="form-control" value="{{ $cliente->telefonoCliente }}">
        @error('telefonoCliente')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <a href="{{ route('clientes.index') }}" class="btn btn-danger">Cancelar</a>
    <button type="submit" class="btn btn-dark">Actualizar Cliente</button>
</form>

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
