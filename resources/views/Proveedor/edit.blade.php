@extends('adminlte::page')

@section('title', 'Proveedores')

@section('content_header')
    <h1>Editar un Proveedor</h1>
@stop

@section('content')
<form action="{{ route('proveedores.update', $proveedor->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT') <!-- Necesario para indicar que es una actualización -->

    <!-- Tipo Proveedor -->
    {{-- <div class="mb-3">
        <label for="tipo_proveedor_id" class="form-label">Categoría</label>
        <select id="tipo_proveedor_id" name="tipo_proveedor_id" class="form-control">
            @foreach($tiposProv as $tipoProv)
                <option value="{{ $tipoProv->id }}" {{ $proveedor->tipo_proveedor_id == $tipoProv->id ? 'selected' : '' }}>
                    {{ $tipoProv->descripcionTE }}
                </option>
            @endforeach
        </select>
        @error('tipo_proveedor_id')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div> --}}

    <!-- Empresa -->
    <div class="mb-3">
        <label for="nombreEmpresa" class="form-label">Empresa</label>
        <input id="nombreEmpresa" name="nombreEmpresa" type="text" class="form-control" value="{{ $proveedor->nombreEmpresa }}">
        @error('nombreEmpresa')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <!-- Nombre -->
    <div class="mb-3">
        <label for="nombreProveedor" class="form-label">Nombre</label>
        <input id="nombreProveedor" name="nombreProveedor" type="text" class="form-control" value="{{ $proveedor->nombreProveedor }}">
        @error('nombreProveedor')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>    

    <!-- Apellidos -->
    <div class="mb-3">
        <label for="apellidoProveedor" class="form-label">Apellidos</label>
        <input id="apellidoProveedor" name="apellidoProveedor" type="text" class="form-control" value="{{ $proveedor->apellidoProveedor }}">
        @error('apellidoProveedor')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>    
    
    <!-- RUC -->
    <div class="mb-3">
        <label for="RUC" class="form-label">DNI</label>
        <input id="RUC" name="RUC" type="text" class="form-control" value="{{ $proveedor->RUC }}">
        @error('RUC')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    
    <!-- DIRECCIÓN -->
    <div class="mb-3">
        <label for="direccionProveedor" class="form-label">Edad</label>
        <input id="direccionProveedor" name="direccionProveedor" type="text" class="form-control" value="{{ $proveedor->direccionProveedor }}">
        @error('direccionProveedor')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <!-- Correo -->
    <div class="mb-3">
        <label for="correoProveedor" class="form-label">Correo</label>
        <input id="correoProveedor" name="correoProveedor" type="email" class="form-control" value="{{ $proveedor->correoProveedor }}">
        @error('correoProveedor')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    
    <!-- Teléfono -->
    <div class="mb-3">
        <label for="telefonoProveedor" class="form-label">Teléfono</label>
        <input id="telefonoProveedor" name="telefonoProveedor" type="text" class="form-control" value="{{ $proveedor->telefonoProveedor }}">
        @error('telefonoProveedor')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <a href="{{route('proveedores.index')}}" class="btn btn-danger">Cancelar</a>
    <button type="submit" class="btn btn-dark">Actualizar Proveedor</button>
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
