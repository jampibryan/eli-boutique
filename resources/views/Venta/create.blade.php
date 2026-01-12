@extends('adminlte::page')

@section('title', 'Resumen de Venta')

@section('content_header')
    <h1>Resumen de Venta</h1>
@stop

@section('content')
    <a href="{{ route('carrito.ver') }}" class="btn btn-secondary mb-3">Volver al Carrito</a>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
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

    <form action="{{ route('ventas.store') }}" method="POST" id="formVenta">
        @csrf

        <!-- Mostrar productos del carrito -->
        <h4>Productos en el Carrito</h4>
        <div class="card">
            <div class="card-body">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Producto</th>
                            <th>Talla</th>
                            <th>Cantidad</th>
                            <th>Stock Disponible</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            $subtotal = 0;
                            $hayProblemasStock = false;
                        @endphp
                        @foreach($carrito as $item)
                            @php
                                $producto = $productos[$item['producto_id']];
                                $talla = $tallas[$item['talla_id']];
                                $itemSubtotal = $item['cantidad'] * $producto->precioP;
                                $subtotal += $itemSubtotal;
                                
                                // Obtener el stock de la talla específica
                                $keyStock = $item['producto_id'] . '_' . $item['talla_id'];
                                $stockTalla = $stocksPorTalla[$keyStock] ?? 0;
                                
                                // Calcular stock DISPONIBLE después de esta venta
                                $stockDisponible = $stockTalla - $item['cantidad'];
                                
                                // Verificar stock
                                $stockSuficiente = $stockTalla >= $item['cantidad'];
                                if (!$stockSuficiente) {
                                    $hayProblemasStock = true;
                                }
                            @endphp
                            <tr class="{{ !$stockSuficiente ? 'table-danger' : '' }}">
                                <td>
                                    {{ $producto->descripcionP }}
                                    @if(!$stockSuficiente)
                                        <br><small class="text-danger fw-bold">
                                            <i class="fas fa-exclamation-triangle"></i> Stock insuficiente
                                        </small>
                                    @endif
                                </td>
                                <td>{{ $talla->descripcion }}</td>
                                <td>
                                    <span class="badge {{ $stockSuficiente ? 'bg-success' : 'bg-danger' }}">
                                        {{ $item['cantidad'] }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <span class="badge bg-secondary" title="Stock actual de esta talla en BD">
                                            Total: {{ $stockTalla }}
                                        </span>
                                        <br>
                                        <span class="badge {{ $stockDisponible >= 0 ? 'bg-info' : 'bg-danger' }}" title="Stock que quedará después de esta venta">
                                            Quedarán: {{ $stockDisponible }}
                                        </span>
                                    </div>
                                </td>
                                <td>S/. {{ number_format($producto->precioP, 2) }}</td>
                                <td>S/. {{ number_format($itemSubtotal, 2) }}</td>
                            </tr>
                            <!-- Campos hidden para enviar al store -->
                            <input type="hidden" name="productos[{{ $loop->index }}][id]" value="{{ $item['producto_id'] }}">
                            <input type="hidden" name="productos[{{ $loop->index }}][talla_id]" value="{{ $item['talla_id'] }}">
                            <input type="hidden" name="productos[{{ $loop->index }}][cantidad]" value="{{ $item['cantidad'] }}">
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($hayProblemasStock)
            <div class="alert alert-danger mt-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Error:</strong> No se puede procesar la venta. Algunos productos no tienen stock suficiente. Por favor, regresa al carrito y ajusta las cantidades.
            </div>
        @endif

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
                    <option value="{{ $cliente->id }}" {{ (request('cliente_id') ?: $clienteSeleccionado) == $cliente->id ? 'selected' : '' }}>{{ $cliente->nombreCliente }} {{ $cliente->apellidoCliente }}</option>
                @endforeach
            </select>
        </div>
        <a href="{{ route('clientes.create', ['redirect' => 'ventas.create']) }}" class="btn btn-info mb-3">Registrar Cliente Nuevo</a>

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

        <!-- Botón para proceder al pago -->
        <button type="submit" class="btn btn-success btn-lg {{ $hayProblemasStock ?? false ? 'disabled' : '' }}" {{ $hayProblemasStock ?? false ? 'disabled' : '' }}>
            <i class="fas fa-money-bill-wave me-2"></i> Proceder al Pago
        </button>
        <a href="{{ route('carrito.ver') }}" class="btn btn-secondary btn-lg">
            <i class="fas fa-arrow-left me-2"></i> Volver al Carrito
        </a>
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
        