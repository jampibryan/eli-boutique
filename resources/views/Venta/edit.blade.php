@extends('adminlte::page')

@section('title', 'Editar Venta')

@section('content_header')
    <!-- <h1><i class="fas fa-edit"></i> Editar Venta</h1> -->
@stop

@section('css')
    <style>
        .checkout-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 20px;
        }

        .section-header {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #667eea;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .product-list {
            margin-bottom: 30px;
        }

        .product-item {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px 20px;
            margin-bottom: 12px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .product-info {
            flex: 1;
            min-width: 200px;
        }

        .product-info-name {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .product-info-details {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .info-badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .price-info {
            text-align: right;
        }

        .price-value {
            font-size: 18px;
            font-weight: bold;
            color: #667eea;
        }

        .client-selector {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .client-selector label {
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .select2-container--default .select2-selection--single {
            height: 45px;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 41px;
            padding-left: 15px;
        }

        .btn-add-client {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 10px;
            transition: all 0.3s;
        }

        .btn-add-client:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(56, 239, 125, 0.4);
            color: white;
        }

        .total-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            text-align: center;
        }

        .total-box-label {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 5px;
        }

        .total-box-value {
            font-size: 48px;
            font-weight: bold;
        }

        .total-box-note {
            font-size: 12px;
            opacity: 0.8;
            margin-top: 5px;
        }

        .btn-back {
            background: white;
            border: 2px solid #667eea;
            color: #667eea;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            margin-right: 10px;
        }

        .btn-back:hover {
            background: #667eea;
            color: white;
        }

        .btn-payment {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-payment:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(56, 239, 125, 0.4);
            color: white;
        }

        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 15px 40px;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
    </style>
@stop

@section('content')
    <div class="mb-3">
        <a href="{{ route('ventas.index') }}" class="btn btn-back">
            <i class="fas fa-times"></i> Cancelar
        </a>
        <a href="{{ route('pagos.create', ['id' => $venta->id, 'type' => 'venta']) }}" class="btn btn-payment">
            <i class="fas fa-cash-register"></i> Volver a Pago
        </a>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <form action="{{ route('ventas.update', $venta->id) }}" method="POST" id="formVenta">
        @csrf
        @method('PUT')

        <div class="checkout-container">
            <div class="section-header">
                <i class="fas fa-shopping-bag"></i> Productos de la Venta
            </div>

            <div class="product-list">
                @php $subtotal = 0; @endphp
                @foreach ($carrito as $item)
                    @php
                        $producto = $productos[$item['producto_id']] ?? App\Models\Producto::find($item['producto_id']);
                        $talla = $tallas[$item['talla_id']] ?? App\Models\ProductoTalla::find($item['talla_id']);
                        $itemSubtotal = $item['cantidad'] * $producto->precioP;
                        $subtotal += $itemSubtotal;
                    @endphp

                    <div class="product-item">
                        <div class="product-info">
                            <div class="product-info-name">{{ $producto->descripcionP }}</div>
                            <div class="product-info-details">
                                <span class="info-badge bg-primary">
                                    <i class="fas fa-ruler-combined"></i> Talla: {{ $talla->descripcion }}
                                </span>
                                <span class="info-badge bg-success">
                                    <i class="fas fa-shopping-cart"></i> Cantidad: {{ $item['cantidad'] }}
                                </span>
                                <span class="info-badge bg-info">
                                    <i class="fas fa-tag"></i> S/ {{ number_format($producto->precioP, 2) }}
                                </span>
                            </div>
                        </div>
                        <div class="price-info">
                            <div class="price-value">S/ {{ number_format($itemSubtotal, 2) }}</div>
                        </div>
                    </div>

                    <!-- Campos hidden para enviar al update -->
                    <input type="hidden" name="productos[{{ $loop->index }}][id]" value="{{ $item['producto_id'] }}">
                    <input type="hidden" name="productos[{{ $loop->index }}][talla_id]" value="{{ $item['talla_id'] }}">
                    <input type="hidden" name="productos[{{ $loop->index }}][cantidad]" value="{{ $item['cantidad'] }}">
                @endforeach
            </div>
        </div>

        @php
            // El precio ya incluye IGV
            $montoTotal = $subtotal;
        @endphp

        <div class="client-selector">
            <div class="section-header">
                <i class="fas fa-user"></i> Información del Cliente
            </div>

            <div class="form-group">
                <label for="cliente_id">
                    <i class="fas fa-user-circle"></i> Seleccionar Cliente
                </label>
                <select class="form-control select2" name="cliente_id" id="cliente_id" required>
                    <option value="">Seleccionar Cliente</option>
                    @foreach ($clientes as $cliente)
                        <option value="{{ $cliente->id }}" {{ $clienteSeleccionado == $cliente->id ? 'selected' : '' }}>
                            {{ $cliente->nombreCliente }} {{ $cliente->apellidoCliente }}
                        </option>
                    @endforeach
                </select>
            </div>

            <a href="{{ route('clientes.create', ['redirect' => 'ventas.edit', 'venta_id' => $venta->id]) }}"
                class="btn btn-add-client">
                <i class="fas fa-user-plus"></i> Registrar Cliente Nuevo
            </a>
        </div>

        <div class="total-box">
            <div class="total-box-label">TOTAL A PAGAR</div>
            <div class="total-box-value">S/ {{ number_format($montoTotal, 2) }}</div>
            <div class="total-box-note">* Precio incluye IGV (18%)</div>
        </div>

        <input type="hidden" name="montoTotal" value="{{ number_format($montoTotal, 2, '.', '') }}">

        <button type="submit" class="btn btn-submit">
            <i class="fas fa-save"></i> Actualizar Venta
        </button>
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
