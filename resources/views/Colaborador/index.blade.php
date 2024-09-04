@extends('adminlte::page')

@section('title', 'Clientes')

@section('content_header')
    <h1>Lista de Colaboradores</h1>
@stop

@section('content')
    <p>Welcome to this beautiful admin panel.</p>

    <table class="table table-dark table-striped mt-4">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">CARGO</th>
                <th scope="col">GÉNERO</th>
                <th scope="col">NOMBRE</th>
                <th scope="col">APELLIDOS</th>
                <th scope="col">DNI</th>
                <th scope="col">EDAD</th>
                <th scope="col">TELÉFONO</th>
                <th scope="col">ACCIONES</th>
            </tr>
        </thead>
    
        <tbody>
            @foreach ($colaboradores as $colaborador)
            <tr>
                <td>{{$colaborador->id}}</td>
                <td>{{$colaborador->cargo->descripcionCargo}}</td>
                <td>{{$colaborador->tipoGenero->descripcionTG}}</td>
                <td>{{$colaborador->nombreColab}}</td>
                <td>{{$colaborador->apellidosColab}}</td>
                <td>{{$colaborador->dniCliente}}</td>
                <td>{{$colaborador->edadColab}}</td>
                <td>{{$colaborador->telefonoColab}}</td>
                <td>Editar | Borrar</td>
    
            </tr>
            @endforeach
        </tbody>
    
    </table>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

@stop

@section('js')
    {{-- <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script> --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
@stop


