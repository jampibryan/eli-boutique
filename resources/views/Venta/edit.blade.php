@extends('adminlte::page')

@section('title', 'Editar Venta')

@section('content_header')
    <h1>Editar Venta</h1>
@stop

@section('content')
    <a href="{{ route('ventas.index') }}" class="btn btn-secondary mb-3">Cancelar</a>
    <a href="{{ route('pagos.create', ['id' => $venta->id, 'type' => 'venta']) }}" class="btn btn-info mb-3">Volver a Pago</a>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('ventas.update', $venta->id) }}" method="POST" id="formVenta">
        @csrf
        @method('PUT')

        <!-- Mostrar productos del carrito -->
        <h4>Productos en el Carrito</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Talla</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php $subtotal = 0; @endphp
                @foreach($carrito as $item)
                    @php
                        $producto = $productos[$item['producto_id']] ?? Producto::find($item['producto_id']);
                        $talla = $tallas[$item['talla_id']] ?? ProductoTalla::find($item['talla_id']);
                        $itemSubtotal = $item['cantidad'] * $producto->precioP;
                        $subtotal += $itemSubtotal;
                    @endphp
                    <tr>
                        <td>{{ $producto->descripcionP }}</td>
                        <td>{{ $talla->descripcion }}</td>
                        <td>{{ $item['cantidad'] }}</td>
                        <td>S/. {{ number_format($producto->precioP, 2) }}</td>
                        <td>S/. {{ number_format($itemSubtotal, 2) }}</td>
                    </tr>
                    <!-- Campos hidden para enviar al update -->
                    <input type="hidden" name="productos[{{ $loop->index }}][id]" value="{{ $item['producto_id'] }}">
                    <input type="hidden" name="productos[{{ $loop->index }}][talla_id]" value="{{ $item['talla_id'] }}">
                    <input type="hidden" name="productos[{{ $loop->index }}][cantidad]" value="{{ $item['cantidad'] }}">
                @endforeach
            </tbody>
        </table>

        @php
            $igv = $subtotal * 0.18;
            $montoTotal = $subtotal + $igv;
        @endphp

        <!-- Selección del Cliente -->
        <h4>Comprador</h4>
        <div class="form-group">
            <label for="cliente_id">Seleccionar Cliente</label>
            <select class="form-control select2" name="cliente_id" required>
                <option value="">Seleccionar Cliente</option>
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->id }}" {{ $clienteSeleccionado == $cliente->id ? 'selected' : '' }}>{{ $cliente->nombreCliente }} {{ $cliente->apellidoCliente }}</option>
                @endforeach
            </select>
        </div>
        <a href="{{ route('clientes.create', ['redirect' => 'ventas.edit', 'venta_id' => $venta->id]) }}" class="btn btn-info mb-3">Registrar Cliente Nuevo</a>

        <!-- Totales -->
        <div class="form-group">
            <label for="subTotal">Subtotal (S/.)</label>
            <input type="number" class="form-control" id="subTotal" name="subTotal" value="{{ number_format($subtotal, 2, '.', '') }}" readonly>
        </div>
        <div class="form-group">
            <label for="IGV">IGV (18%)</label>
            <input type="number" class="form-control" id="IGV" name="IGV" value="{{ number_format($igv, 2, '.', '') }}" readonly>
        </div>
        <div class="form-group">
            <label for="montoTotal">Monto Total (S/.)</label>
            <input type="number" class="form-control" id="montoTotal" name="montoTotal" value="{{ number_format($montoTotal, 2, '.', '') }}" readonly>
        </div>

        <!-- Botón para actualizar venta -->
        <button type="submit" class="btn btn-success">Actualizar Venta</button>
    </form>
@stop

@section('js')
<script>
    $('.select2').select2({
        placeholder: 'Buscar cliente...',
        allowClear: true
    });
</script>
@stop