@extends('adminlte::page')

@section('title', 'Compras')

@section('content_header')
    <h1>Lista de compras</h1>
@stop

@section('content')

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between">
        <div>
            <a href="{{ route('compras.create') }}" class="btn btn-danger">REGISTRAR ORDEN DE COMPRA</a>
        </div>
        <div>
            {{-- <a href="{{ route('compras.index') }}" class="btn btn-secondary">GENERAR ORDEN DE COMPRA</a> --}}
            <a href="{{ route('compras.pdf') }}" target="_blank" class="btn btn-primary">GENERAR REPORTE</a>
        </div>
    </div>

    <div class="container mt-4">
        <table id="example" class="table table-dark table-striped text-center">
            <thead class="table-dark">
                <tr>
                    <th scope="col" class="align-middle">Código Compra</th>
                    <th scope="col" class="align-middle">Proveedor</th>
                    <th scope="col" class="align-middle">Fecha</th>
                    {{-- <th scope="col" class="align-middle">Hora</th> --}}
                    <th scope="col" class="align-middle">Estado</th>
                    <th scope="col" class="align-middle">Comprobante</th>
                    <th scope="col" class="align-middle">Monto Total</th>
                    <th scope="col" class="align-middle">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($compras as $compra)
                    @php
                        // Definir variables temporales para el comprobante y el estado
                        $comprobanteDescripcion = $compra->pago->comprobante->descripcionCOM ?? 'Sin comprobante';
                        $estadoDescripcion = $compra->estadoTransaccion->descripcionET;
                        $pagoCompra = $compra->pago->importe ?? 0;
                    @endphp
                    <tr>
                        <td class="align-middle">{{ $compra->codigoCompra }}</td>
                        <td class="align-middle">{{ $compra->proveedor->nombreProveedor }} {{ $compra->proveedor->apellidoProveedor }}</td>
                        <td class="align-middle">{{ \Carbon\Carbon::parse($compra->created_at)->format('d/m/Y') }}</td>
                        {{-- <td class="align-middle">{{ \Carbon\Carbon::parse($compra->created_at)->format('h:i A') }}</td> --}}
                        <td class="align-middle">{{ $estadoDescripcion }}</td>
                            
                        <td class="align-middle">{{ $comprobanteDescripcion }}</td>
                        <td class="align-middle">S/ {{ number_format($pagoCompra, 2) }}</td>

                        <td class="align-middle">
                            @if($estadoDescripcion == 'Pendiente')
                                <a href="{{ route('ordenCompras.pdf', $compra) }}" target="_blank" class="btn btn-secondary btn-sm">Generar Orden de Compra</a>
                                <a href="{{ route('compras.edit', $compra) }}" class="btn btn-info btn-sm mt-1">Editar</a>
                                <a href="{{ route('pagos.create', [$compra->id, 'compra']) }}" class="btn btn-success btn-sm mt-1">Pagar</a>
                                
                                <form action="{{ route('compras.anular', $compra->id) }}" method="POST" style="display:inline;" class="anular-form">
                                    @csrf
                                    <button type="button" class="btn btn-danger btn-sm mt-1" onclick="confirmAnular(this)">Anular</button>
                                </form>
                            @elseif($estadoDescripcion == 'Pagado')
                                <form action="{{ route('compras.recibir', $compra) }}" method="POST" style="display:inline;" class="recibir-form">
                                    @csrf
                                    <button type="button" class="btn btn-primary btn-sm" onclick="confirmRecibir(this)">Pedido Recibido</button>
                                </form>
                            @elseif($estadoDescripcion == 'Recibido')
                                <a href="{{ route('ordenCompras.pdf', $compra) }}" target="_blank" class="btn btn-secondary btn-sm">Generar Orden de Compra</a>
                                {{-- <a href="{{ route('compras.index', $compra) }}" class="btn btn-warning btn-sm mt-1">
                                    Subir {{ $comprobanteDescripcion }}
                                </a> --}}
                            @endif
                
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
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">


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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

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


<script>
    function confirmAnular(button) {
        const form = button.closest('.anular-form');

        Swal.fire({
            title: '¿Estás seguro?',
            text: "No podrás revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, anular compra!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit(); // Si el usuario confirma, se envía el formulario
            }
        });
    }
</script>



<script>
    function confirmRecibir(button) {
        const form = button.closest('.recibir-form');

        Swal.fire({
            title: '¿El pedido llegó a la tienda?',
            text: "No podrás revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, llegó a la tienda!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit(); // Si el usuario confirma, se envía el formulario
            }
        });
    }
</script>

@stop