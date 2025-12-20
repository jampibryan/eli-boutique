@extends('adminlte::page')

@section('title', 'Proveedores')

@section('content_header')
    <h1>Lista de proveedores</h1>
@stop

@section('content')

    <div class="d-flex justify-content-between">
        <a href="{{ route('proveedores.create') }}" class="btn btn-danger">REGISTRAR PROVEEDOR</a>
        <a href="{{ route('proveedores.pdf') }}" target="_blank" class="btn btn-primary">GENERAR REPORTE</a>
    </div>

    <div class="container mt-4">
        <table id="example" class="table table-dark table-striped text-center">
            <thead>
                <tr>
                    <th scope="col">CÓDIGO</th>
                    {{-- <th scope="col">TIPO PROVEEDOR</th> --}}
                    <th scope="col">EMPRESA</th>
                    <th scope="col">NOMBRE</th>
                    <th scope="col">APELLIDOS</th>
                    <th scope="col">RUC</th>
                    <th scope="col">DIRECCIÓN</th>
                    <th scope="col">EMAIL</th>
                    <th scope="col">TELÉFONO</th>
                    <th scope="col">ACCIONES</th>
                </tr>
            </thead>
        
            <tbody>
                @foreach ($proveedores as $proveedor)
                <tr>
                    <td class="align-middle">{{$proveedor->id}}</td>
                    {{-- <td class="align-middle">{{$proveedor->tipoProveedor->descripcionTE}}</td> --}}
                    <td class="align-middle">{{$proveedor->nombreEmpresa}}</td>
                    <td class="align-middle">{{$proveedor->nombreProveedor}}</td>
                    <td class="align-middle">{{$proveedor->apellidoProveedor}}</td>
                    <td class="align-middle">{{$proveedor->RUC}}</td>
                    <td class="align-middle">{{$proveedor->direccionProveedor}}</td>
                    <td class="align-middle">{{$proveedor->correoProveedor}}</td>
                    <td class="align-middle">{{$proveedor->telefonoProveedor}}</td>
                    <td class="align-middle">
                        <a href="{{route('proveedores.edit', $proveedor)}}" class="btn btn-info">Editar</a>
                        <form action="{{route('proveedores.destroy', $proveedor)}}" method="post">
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


