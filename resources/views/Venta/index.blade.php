
@extends('adminlte::page')

@section('title', 'Ventas')

@section('content_header')
    <h1>Lista de ventas</h1>
@stop

@section('content')

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="alert alert-secondary mt-3" role="alert" style="font-size: 0.9rem; color: #555; background-color: #f8f9fa; border: 1px solid #ddd;">
        Presiona <strong>F1</strong> para acceder a la <a href="#" class="text-primary" style="text-decoration: underline;" onclick="window.open('/guiaventas/index.htm', '_blank'); return false;">Guía de Ventas</a>
    </div>    

    <div class="d-flex justify-content-between">
        <a href="{{ route('ventas.create') }}" class="btn btn-danger">REGISTRAR VENTA</a>

        {{-- <a href="{{ route('exportarCSV') }}" class="btn btn-info">DESCARGAR CSV</a> --}}
                
        {{-- <a href="{{ route('ventas.index') }}" class="btn btn-primary">GENERAR REPORTE</a> --}}
        <a href="{{ route('ventas.pdf') }}" target="_blank" class="btn btn-primary">GENERAR REPORTE</a>
    </div>

    <div class="container mt-4">
        <table id="example" class="table table-dark table-striped text-center">
            <thead class="table-dark">
                <tr>
                    <th scope="col" class="align-middle">Código Venta</th>
                    <th scope="col" class="align-middle">Cliente</th>
                    <th scope="col" class="align-middle">Fecha</th>
                    <th scope="col" class="align-middle">Hora</th>
                    <th scope="col" class="align-middle">Comprobante</th>
                    <th scope="col" class="align-middle">Estado</th>
                    <th scope="col" class="align-middle">Monto Total</th>
                    <th scope="col" class="align-middle">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ventas as $venta)
                    @php
                        // Definir variables temporales para el comprobante y el estado
                        $comprobanteDescripcion = $venta->pago->comprobante->descripcionCOM ?? 'Sin comprobante';
                        $estadoDescripcion = $venta->estadoTransaccion->descripcionET;
                    @endphp
                    <tr>
                        <td class="align-middle">{{ $venta->codigoVenta }}</td>
                        <td class="align-middle">{{ $venta->cliente->nombreCliente}} {{ $venta->cliente->apellidoCliente}}</td>
                        <td class="align-middle">{{ \Carbon\Carbon::parse($venta->created_at)->format('d/m/Y') }}</td>
                        <td class="align-middle">{{ \Carbon\Carbon::parse($venta->created_at)->format('h:i A') }}</td>
                        <td class="align-middle">{{ $comprobanteDescripcion}}</td>
                        <td class="align-middle">{{ $estadoDescripcion }}</td>
                        <td class="align-middle">S/ {{ number_format($venta->montoTotal, 2) }}</td>
                        <td class="align-middle">
                            @if($estadoDescripcion == 'Pendiente')
                                {{-- <a href="{{ route('ventas.show', $venta) }}" class="btn btn-info btn-sm">Ver</a> --}}
                                <a href="{{ route('ventas.edit', $venta) }}" class="btn btn-info btn-sm">Editar</a>
                                <a href="{{ route('pagos.create', [$venta->id, 'venta']) }}" class="btn btn-success btn-sm">Pagar</a>


                                <form action="{{ route('ventas.anular', $venta->id) }}" method="POST" style="display:inline;" class="anular-form">
                                    @csrf
                                    <button type="button" class="btn btn-danger btn-sm mt-1" onclick="confirmAnular(this)">Anular</button>
                                </form>
                            @elseif($estadoDescripcion == 'Pagado')
                                <a href="{{ route('ventas.comprobante', $venta) }}" target="_blank" class="btn btn-warning btn-sm">
                                    Generar {{ $comprobanteDescripcion }}
                                </a>
                            @endif
                            {{-- <form action="{{ route('ventas.destroy', $venta) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Estás seguro de eliminar esta venta?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                            </form> --}}
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
            confirmButtonText: 'Sí, anular venta!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit(); // Si el usuario confirma, se envía el formulario
            }
        });
    }
</script>

{{-- <script>
    function capturarTecla(event) {
        // Verificar si se presionó la tecla F1
        if (event.key === "F1") {
            event.preventDefault(); // Prevenir la acción predeterminada del navegador

            // Llamar al backend para abrir el archivo de ayuda
            fetch('{{ route("abrirAyuda") }}')
                .then(response => {
                    if (response.ok) {
                        console.log("Ayuda abierta correctamente.");
                    } else {
                        alert("No se pudo abrir el archivo de ayuda.");
                    }
                })
                .catch(error => {
                    console.error("Error al intentar abrir la ayuda:", error);
                });
        }
    }

    // Escuchar eventos de teclado en toda la página
    document.addEventListener("keydown", capturarTecla);
</script> --}}


<script>
    function capturarTecla(event) {
        if (event.key === "F1") {
            event.preventDefault(); // Prevenir la acción predeterminada del navegador
            // Abrir la ayuda en una nueva pestaña
            window.open('/guiaventas/index.htm', '_blank');
        }
    }

    document.addEventListener("keydown", capturarTecla);
</script>



@stop

