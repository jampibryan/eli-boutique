@extends('adminlte::page')

@section('title', 'Productos')

@section('content_header')
    <h1>Lista de Ventas</h1>
@stop

@section('content')

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('ventas.create') }}" class="btn btn-primary">Registrar Nueva Venta</a>

    <table class="table">
        <thead>
            <tr>
                <th>Código Venta</th>
                <th>Cliente</th>
                <th>Comprobante</th>
                <th>Estado</th>
                <th>Monto Total</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventas as $venta)
                <tr>
                    <td>{{ $venta->codigoVenta }}</td>
                    <td>{{ $venta->cliente->nombre }}</td>
                    <td>{{ $venta->comprobante->descripcion }}</td>
                    <td>{{ $venta->estadoVenta->descripcionEV }}</td>
                    <td>{{ $venta->montoTotal }}</td>
                    <td>
                        <a href="{{ route('ventas.show', $venta) }}" class="btn btn-info">Ver</a>
                        <a href="{{ route('ventas.edit', $venta) }}" class="btn btn-warning">Editar</a>
                        <form action="{{ route('ventas.destroy', $venta) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
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
