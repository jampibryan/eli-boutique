@extends('adminlte::page')

@section('title', 'Registrar Pago')

@section('content_header')
    <!-- Utilizando un operador de fusión de null | variable ?? valorPorDefecto -->
    <h1>Registrar pago para la {{ $type }} {{ $transaction->{'codigo' . ucfirst($type)} ?? 'Código no disponible' }}</h1>
@stop

@section('content')
    <a href="{{ route($type. 's.index') }}" class="btn btn-primary mb-3">Cancelar</a>

    <form action="{{ route('pagos.store', [$transaction->id, $type]) }}" method="POST">
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

        @if($type === 'venta')
        <!-- Monto Total de la Venta -->
        <div class="form-group">
            <label for="montoTotal">Monto Total S/</label>
            <input type="number" class="form-control" id="montoTotal" value="{{ $transaction->montoTotal }}" readonly>
        </div>

        <!-- Importe Recibido -->
        <div class="form-group">
            <label for="importe">Importe Recibido S/</label>
            <input type="number" class="form-control" name="importe" id="importe" step="0.01" min="0" required>
        </div>

        <!-- Vuelto -->
        <div class="form-group">
            <label for="vuelto">Vuelto S/</label>
            <input type="number" class="form-control" id="vuelto" name="vuelto" readonly>
        </div>
        @else
        <!-- Importe Recibido para Compra -->
        <div class="form-group">
            <label for="importe">Importe Recibido S/</label>
            <input type="number" class="form-control" name="importe" id="importe" step="0.01" min="0" required>
        </div>
        @endif

        <!-- Botón para registrar el pago -->
        <button type="submit" class="btn btn-success">Registrar Pago</button>
    </form>

@stop

@section('js')
    @if($type === 'venta')
        <script>
            document.getElementById('importe').addEventListener('input', function() {
                const importe = parseFloat(this.value);
                const montoTotal = parseFloat(document.getElementById('montoTotal').value);
                const vuelto = importe - montoTotal;

                document.getElementById('vuelto').value = vuelto.toFixed(2);
            });
        </script>
    @endif
@stop
