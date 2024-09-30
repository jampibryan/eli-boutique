@extends('adminlte::page')

@section('title', 'Clientes')

@section('content_header')
    <h1>Lista de Clientes</h1>
@stop

@section('content')
    <a href="{{route('clientes.create')}}" class="btn btn-danger justify-content-center" >CREAR CLIENTE</a>

    {{-- @if(auth()->user()->can('gestionar clientes'))
        <a href="{{ route('clientes.create') }}" class="btn btn-danger justify-content-center">CREAR CLIENTE</a>
    @endif
     --}}
     {{-- Esta línea de código se utiliza para mostrar un botón "Agregar Cliente" solo si el usuario autenticado tiene el permiso gestionar clientes. Esto es parte de la lógica de autorización de tu aplicación y asegura que solo los usuarios con el permiso adecuado puedan ver y utilizar esa funcionalidad. --}}


    <div class="container mt-4">
        <table id="example" class="table table-dark table-striped text-center">
            <thead>
                <tr>
                    <th scope="col" class="align-middle">ID</th>
                    <th scope="col" class="align-middle">NOMBRE</th>
                    <th scope="col" class="align-middle">APELLIDOS</th>
                    <th scope="col" class="align-middle">GÉNERO</th>
                    <th scope="col" class="align-middle">DNI</th>
                    <th scope="col" class="align-middle">EMAIL</th>
                    <th scope="col" class="align-middle">TELÉFONO</th>
                    <th scope="col" class="align-middle">ACCIONES</th>
                </tr>
            </thead>
        
            <tbody>
                @foreach ($clientes as $cliente)
                <tr>
                    <td class="align-middle">{{$cliente->id}}</td>
                    <td class="align-middle">{{$cliente->nombreCliente}}</td>
                    <td class="align-middle">{{$cliente->apellidoCliente}}</td>
                    <td class="align-middle">{{$cliente->tipoGenero->descripcionTG}}</td>
                    <td class="align-middle">{{$cliente->dniCliente}}</td>
                    <td class="align-middle">{{$cliente->correoCliente}}</td>
                    <td class="align-middle">{{$cliente->telefonoCliente}}</td>
                    <td class="align-middle">
                        <a href="{{route('clientes.edit', $cliente)}}" class="btn btn-info">Editar</a>
                        <form action="{{route('clientes.destroy', $cliente)}}" method="post">
                            @csrf
                            @method('delete')
                            <button class="btn btn-danger mt-1">Eliminar</button>
                        </form>
                    </td>    
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">


    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">

    <!-- jQuery (necesario para DataTables) -->
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
@stop

@section('js')
    {{-- <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script> --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#example').DataTable({
                "language": {
                    "decimal": "",
                    "emptyTable": "No hay datos disponibles en la tabla",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                    "infoFiltered": "(filtrado de _MAX_ registros totales)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                    "search": "Buscar:",
                    "zeroRecords": "No se encontraron resultados",
                    "paginate": {
                        "first": "Primero",
                        "last": "Último",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    },
                    "aria": {
                        "sortAscending": ": activar para ordenar la columna de manera ascendente",
                        "sortDescending": ": activar para ordenar la columna de manera descendente"
                    }
                }
            });
        });
    </script>
@stop