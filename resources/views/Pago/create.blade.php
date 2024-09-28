@extends('adminlte::page')

@section('title', 'Registrar Pago')

@section('content_header')
    <h1>Registrar Pago para la Venta #{{ $venta->id }}</h1>
@stop

@section('content')

    <form action="{{ route('ventas.pagos.store', $venta->id) }}" method="POST">
        @csrf

        <!-- Comprobante -->
        <div class="form-group">
            <label for="comprobante_id">Comprobante</label>
            <select name="comprobante_id" id="comprobante_id" class="form-control" required>
                <option value="">Seleccionar Comprobante</option>
                @foreach($comprobantes as $comprobante)
                    <option value="{{ $comprobante->id }}">{{ $comprobante->descripcionCOM }}</option>
                @endforeach
            </select>
        </div>

        <!-- Monto Total de la Venta -->
        <div class="form-group">
            <label for="montoTotal">Monto Total</label>
            <input type="number" class="form-control" id="montoTotal" value="{{ $venta->montoTotal }}" readonly>
        </div>

        <!-- Importe Recibido -->
        <div class="form-group">
            <label for="importeRecibido">Importe Recibido</label>
            <input type="number" class="form-control" name="importeRecibido" id="importeRecibido" step="0.01" min="0" required>
        </div>

        <!-- Vuelto -->
        <div class="form-group">
            <label for="vuelto">Vuelto</label>
            <input type="number" class="form-control" id="vuelto" name="vuelto" readonly>
        </div>

        <!-- Botón para registrar el pago -->
        <button type="submit" class="btn btn-success">Registrar Pago</button>
    </form>

@stop

@section('js')
    <script>
        document.getElementById('importeRecibido').addEventListener('input', function() {
            const importeRecibido = parseFloat(this.value);
            const montoTotal = parseFloat(document.getElementById('montoTotal').value);
            const vuelto = importeRecibido - montoTotal;

            document.getElementById('vuelto').value = vuelto.toFixed(2);
        });
    </script>
@stop
