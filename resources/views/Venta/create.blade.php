@extends('adminlte::page')

@section('title', 'Ventas')

@section('content_header')
    <h1>Registro de Venta</h1>
@stop

@section('content')

    <form action="{{ route('ventas.store') }}" method="POST">
        @csrf

        <!-- Código de venta -->
        <div class="form-group">
            <label for="codigoVenta">Código Venta</label>
            <input type="text" class="form-control" name="codigoVenta" required>
        </div>

        <!-- Clientes -->
        <div class="form-group">
            <label for="cliente_id">Cliente</label>
            <select class="form-control" name="cliente_id" required>
                <option value="">Seleccionar Cliente</option>
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id  ? 'selected' : '' }}>
                        {{ $cliente->nombreCliente }} {{ $cliente->apellidoCliente }} 
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Tipo de Comprobante -->
        <div class="form-group">
            <label for="comprobante_id">Tipo Comprobante</label>
            <select class="form-control" name="comprobante_id" required>
                <option value="">Seleccionar Commprobante de Pago</option>
                @foreach($comprobantes as $comprobante)
                    <option value="{{ $comprobante->id }}" {{ old('comprobante_id') == $comprobante->id  ? 'selected' : '' }}>
                        {{ $comprobante->descripcionCOM }} 
                    </option>
                @endforeach
            </select>
        </div>

        <!-- subTotal -->
        <div class="form-group">
            <label for="subTotal">Subtotal</label>
            <input type="number" class="form-control" name="subTotal" step="0.01" required>
        </div>

        <!-- IGV -->
        <div class="form-group">
            <label for="IGV">IGV 18%</label>
            <input type="number" class="form-control" name="IGV" step="0.01" required>
        </div>

        <!-- Monto Total -->
        <div class="form-group">
            <label for="montoTotal">Monto Total</label>
            <input type="number" class="form-control" name="montoTotal" step="0.01" required>
        </div>
        <button type="submit" class="btn btn-success">Guardar Venta</button>
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
