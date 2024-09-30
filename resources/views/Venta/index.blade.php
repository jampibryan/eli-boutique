@extends('adminlte::page')

@section('title', 'Ventas')

@section('content_header')
    <h1>Lista de Ventas</h1>
@stop

@section('content')

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('ventas.create') }}" class="btn btn-primary mb-3">Registrar Nueva Venta</a>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Código Venta</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Comprobante</th>
                    <th>Estado</th>
                    <th>Monto Total</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ventas as $venta)
                    @php
                        // Definir variables temporales para el comprobante y el estado
                        $comprobanteDescripcion = $venta->pago->comprobante->descripcionCOM ?? 'Sin comprobante';
                        $estadoDescripcion = $venta->estadoVenta->descripcionEV;
                    @endphp
                    <tr>
                        <td>{{ $venta->codigoVenta }}</td>
                        <td>{{ $venta->cliente->nombreCliente}} {{ $venta->cliente->apellidoCliente}}</td>
                        <td>{{ \Carbon\Carbon::parse($venta->created_at)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($venta->created_at)->format('h:i A') }}</td>
                        <td>{{ $comprobanteDescripcion}}</td>
                        <td>{{ $estadoDescripcion }}</td>
                        <td>{{ number_format($venta->montoTotal, 2) }}</td>
                        <td>
                            
                            @if($estadoDescripcion == 'Pendiente')
                                <a href="{{ route('ventas.show', $venta) }}" class="btn btn-info btn-sm">Ver</a>
                                <a href="{{ route('ventas.edit', $venta) }}" class="btn btn-warning btn-sm">Editar</a>
                                <a href="{{ route('ventas.pagos.create', $venta) }}" class="btn btn-success btn-sm">Pagar</a>
                            @elseif($estadoDescripcion == 'Pagado')
                                <a href="{{ route('ventas.edit', $venta) }}" class="btn btn-warning btn-sm">
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">
    </script>
@stop
