@extends('adminlte::page')

@section('title', 'Colaboradores')

@section('content_header')
    <h1>Registrar un Colaborador</h1>
@stop

@section('content')
<form action="{{ route('colaboradores.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- Cargo -->
    <div class="mb-3">
        <label for="cargo_id" class="form-label">Cargo</label>
        <select id="cargo_id" name="cargo_id" class="form-control">
            <option value="">Seleccionar cargo</option>
            @foreach($cargos as $cargo)
                <option value="{{ $cargo->id }}" {{ old('cargo_id') == $cargo->id ? 'selected' : '' }}>
                    {{ $cargo->descripcionCargo }}
                </option>
            @endforeach
        </select>
        @error('cargo_id')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <!-- Nombre -->
    <div class="mb-3">
        <label for="nombreColab" class="form-label">Nombre</label>
        <input id="nombreColab" name="nombreColab" type="text" class="form-control" value="{{ old('nombreColab') }}">
        @error('nombreColab')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <!-- Apellidos -->
    <div class="mb-3">
        <label for="apellidosColab" class="form-label">Apellidos</label>
        <input id="apellidosColab" name="apellidosColab" type="text" class="form-control" value="{{ old('apellidosColab') }}">
        @error('apellidosColab')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    
    <!-- Tipo Genero -->
    <div class="mb-3">
        <label for="tipo_genero_id" class="form-label">Género</label>
        <select id="tipo_genero_id" name="tipo_genero_id" class="form-control">
            <option value="">Seleccionar género</option>
            @foreach($generos as $genero)
                <option value="{{ $genero->id }}" {{ old('tipo_genero_id') == $genero->id ? 'selected' : '' }}>
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
        <label for="dniColab" class="form-label">DNI</label>
        <input id="dniColab" name="dniColab" type="text" class="form-control" value="{{ old('dniColab') }}">
        @error('dniColab')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <!-- Edad -->
    <div class="mb-3">
        <label for="edadColab" class="form-label">Edad</label>
        <input id="edadColab" name="edadColab" type="number" class="form-control" value="{{ old('edadColab') }}">
        @error('edadColab')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <!-- Correo -->
    <div class="mb-3">
        <label for="correoColab" class="form-label">Correo</label>
        <input id="correoColab" name="correoColab" type="email" class="form-control" value="{{ old('correoColab') }}">
        @error('correoColab')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <!-- Teléfono -->
    <div class="mb-3">
        <label for="telefonoColab" class="form-label">Teléfono</label>
        <input id="telefonoColab" name="telefonoColab" type="text" class="form-control" value="{{ old('telefonoColab') }}">
        @error('telefonoColab')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <a href="{{ route('colaboradores.index') }}" class="btn btn-danger">Cancelar</a>
    <button type="submit" class="btn btn-dark">Crear Colaborador</button>
    
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
