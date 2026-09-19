@extends('adminlte::page')

@section('title', 'Venta')

@section('content_header')
    <!-- <h1><i class="fas fa-receipt"></i> Resumen de Venta</h1> -->
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

        .product-item.insufficient-stock {
            border-left-color: #f44336;
            background: #ffebee;
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

        .stock-info-compact {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .price-info {
            text-align: right;
        }

        .price-label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
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
        }

        .total-box-label {
            font-size: 14px;
            margin-bottom: 5px;
            opacity: 0.9;
        }

        .total-box-value {
            font-size: 40px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .total-box-note {
            font-size: 12px;
            opacity: 0.8;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn-proceed {
            flex: 1;
            min-width: 200px;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
            color: white;
            padding: 15px 30px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .btn-proceed:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(56, 239, 125, 0.4);
            color: white;
        }

        .btn-proceed:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .btn-back-cart {
            background: white;
            border: 2px solid #667eea;
            color: #667eea;
            padding: 15px 30px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .btn-back-cart:hover {
            background: #667eea;
            color: white;
        }

        .alert-modern {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 20px;
        }
    </style>
@stop

@section('content')
    <a href="{{ route('carrito.ver') }}" class="btn btn-back-cart mb-3">
        <i class="fas fa-arrow-left"></i> Volver al Carrito
    </a>

    @if (session('error'))
        <div class="alert alert-danger alert-modern">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
        </div>
    @endif
    @if (session('success'))
        <div class="alert alert-success alert-modern">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
        </div>
    @endif
    @if (session('info'))
        <div class="alert alert-info alert-modern">
            <i class="fas fa-info-circle me-2"></i>
            {{ session('info') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-modern">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('ventas.store') }}" method="POST" id="formVenta">
        @csrf

        <div class="checkout-container">
            <!-- Productos -->
            <div class="section-header">
                <i class="fas fa-shopping-bag"></i> Productos a Vender
            </div>

            <div class="product-list">
                @php
                    $subtotal = 0;
                    $hayProblemasStock = false;
                @endphp
                @foreach ($carrito as $item)
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

                    <div class="product-item {{ !$stockSuficiente ? 'insufficient-stock' : '' }}">
                        <div class="product-info">
                            <div class="product-info-name">
                                {{ $producto->descripcionP }}
                                @if (!$stockSuficiente)
                                    <span class="badge bg-danger ms-2">
                                        <i class="fas fa-exclamation-triangle"></i> Sin Stock
                                    </span>
                                @endif
                            </div>
                            <div class="product-info-details">
                                <span class="info-badge bg-primary text-white">
                                    <i class="fas fa-tag"></i> {{ $talla->descripcion }}
                                </span>
                                <span class="info-badge bg-success text-white">
                                    <i class="fas fa-cube"></i> Cant: {{ $item['cantidad'] }}
                                </span>
                                <div class="stock-info-compact">
                                    <span class="info-badge bg-secondary text-white">
                                        Stock: {{ $stockTalla }}
                                    </span>
                                    <span
                                        class="info-badge {{ $stockDisponible >= 0 ? 'bg-info' : 'bg-danger' }} text-white">
                                        Quedarán: {{ $stockDisponible }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="price-info">
                            <div class="price-label">Subtotal</div>
                            <div class="price-value">S/ {{ number_format($itemSubtotal, 2) }}</div>
                            <small class="text-muted">S/ {{ number_format($producto->precioP, 2) }} c/u</small>
                        </div>
                    </div>

                    <!-- Campos hidden para enviar al store -->
                    <input type="hidden" name="productos[{{ $loop->index }}][id]" value="{{ $item['producto_id'] }}">
                    <input type="hidden" name="productos[{{ $loop->index }}][talla_id]" value="{{ $item['talla_id'] }}">
                    <input type="hidden" name="productos[{{ $loop->index }}][cantidad]" value="{{ $item['cantidad'] }}">
                @endforeach
            </div>

            @if ($hayProblemasStock)
                <div class="alert alert-danger alert-modern">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Error:</strong> No se puede procesar la venta. Algunos productos no tienen stock suficiente.
                    Por favor, regresa al carrito y ajusta las cantidades.
                </div>
            @endif

            @php
                // El precio ya incluye IGV
                $montoTotal = $subtotal;
            @endphp

            <!-- Cliente -->
            <div class="section-header">
                <i class="fas fa-user"></i> Datos del Cliente
            </div>

            <div class="client-selector">
                <label for="cliente_id">
                    <i class="fas fa-address-card"></i> Seleccionar Cliente
                </label>
                <div class="d-flex gap-2 align-items-center">
                    <select class="form-control select2" id="cliente_id_select" name="cliente_id" required style="flex: 1;">
                        <option value="">-- Buscar cliente por nombre o DNI --</option>
                        @foreach ($clientes as $cliente)
                            <option value="{{ $cliente->id }}"
                                {{ (request('cliente_id') ?: $clienteSeleccionado) == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->nombreCliente }} {{ $cliente->apellidoCliente }} - DNI:
                                {{ $cliente->dniCliente }}
                            </option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-add-client mt-0 text-nowrap" data-bs-toggle="modal" data-bs-target="#modalQuickCliente">
                        <i class="fas fa-user-plus"></i> + Alta Rápida
                    </button>
                </div>
            </div>

            <!-- Total -->
            <div class="total-box">
                <div class="total-box-label">TOTAL A PAGAR</div>
                <div class="total-box-value">S/ {{ number_format($montoTotal, 2) }}</div>
                <div class="total-box-note">
                    <i class="fas fa-info-circle"></i> Precio incluye IGV (18%)
                </div>
            </div>
            <input type="hidden" id="montoTotal" name="montoTotal" value="{{ number_format($montoTotal, 2, '.', '') }}">

            <!-- Botones -->
            <div class="action-buttons">
                <button type="submit" class="btn btn-proceed {{ $hayProblemasStock ?? false ? 'disabled' : '' }}"
                    {{ $hayProblemasStock ?? false ? 'disabled' : '' }}>
                    <i class="fas fa-cash-register me-2"></i> Proceder al Pago
                </button>
                <a href="{{ route('carrito.ver') }}" class="btn btn-back-cart">
                    <i class="fas fa-shopping-cart me-2"></i> Editar Carrito
                </a>
            </div>
        </div>
    </form>

    <!-- Modal Alta Rápida de Cliente -->
    <div class="modal fade" id="modalQuickCliente" tabindex="-1" aria-labelledby="modalQuickClienteLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-dark text-white rounded-top-4">
                    <h5 class="modal-title fw-bold" id="modalQuickClienteLabel">
                        <i class="fas fa-user-plus text-warning me-2"></i> Alta Rápida de Cliente
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formQuickCliente">
                    @csrf
                    <div class="modal-body p-4">
                        <div id="quickClienteAlert" class="alert alert-danger d-none mb-3"></div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Nombre *</label>
                                <input type="text" name="nombreCliente" class="form-control" placeholder="Ej: María" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Apellido *</label>
                                <input type="text" name="apellidoCliente" class="form-control" placeholder="Ej: Perez" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">DNI *</label>
                                <input type="text" name="dniCliente" class="form-control" placeholder="8 dígitos" maxlength="8" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Teléfono</label>
                                <input type="text" name="telefonoCliente" class="form-control" placeholder="999888777">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-muted">Género *</label>
                                <select name="tipo_genero_id" class="form-select" required>
                                    <option value="">-- Seleccionar --</option>
                                    @if(isset($generos))
                                        @foreach($generos as $gen)
                                            <option value="{{ $gen->id }}">{{ $gen->descripcionTG }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 rounded-bottom-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-boutique-gold fw-bold" id="btnSaveQuickCliente">
                            <i class="fas fa-save me-1"></i> Guardar y Seleccionar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#cliente_id_select').select2({
                placeholder: 'Buscar cliente por nombre o DNI...',
                allowClear: true,
                width: '100%'
            });

            // Form Submit AJAX para Quick Cliente
            $('#formQuickCliente').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#btnSaveQuickCliente');
                const $alert = $('#quickClienteAlert');
                $alert.addClass('d-none').text('');
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Guardando...');

                $.ajax({
                    url: "{{ route('clientes.store') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    headers: {
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Guardar y Seleccionar');
                        if (response.success && response.cliente) {
                            const c = response.cliente;
                            const optionText = `${c.nombreCliente} ${c.apellidoCliente} - DNI: ${c.dniCliente || ''}`;
                            const newOption = new Option(optionText, c.id, true, true);
                            
                            $('#cliente_id_select').append(newOption).trigger('change');

                            // Cerrar Modal y resetear form
                            const modalEl = document.getElementById('modalQuickCliente');
                            const modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();
                            $('#formQuickCliente')[0].reset();

                            Swal.fire({
                                icon: 'success',
                                title: 'Cliente Creado',
                                text: 'El cliente se registró y seleccionó automáticamente.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Guardar y Seleccionar');
                        let msg = 'Error al registrar cliente.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        $alert.html(msg).removeClass('d-none');
                    }
                });
            });
        });
    </script>
@stop
