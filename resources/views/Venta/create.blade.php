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

        .payment-mode-card {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 20px;
            cursor: pointer;
            transition: all 0.25s ease;
            background: #ffffff;
            display: block;
            margin-bottom: 0;
            height: 100%;
        }

        .payment-mode-card:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        }

        .payment-mode-card.active {
            border-color: #11998e;
            background: #f0fdf4;
            box-shadow: 0 4px 16px rgba(17, 153, 142, 0.2);
        }

        .payment-mode-card.active-pendiente {
            border-color: #f59e0b;
            background: #fffbeb;
            box-shadow: 0 4px 16px rgba(245, 158, 11, 0.2);
        }

        .mode-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .bg-success-light {
            background: #dcfce7;
            color: #15803d;
        }

        .bg-warning-light {
            background: #fef3c7;
            color: #b45309;
        }

        .btn-shortcut {
            border-radius: 8px;
            font-weight: 600;
            padding: 5px 12px;
            font-size: 13px;
            transition: all 0.2s;
        }

        .btn-shortcut:hover {
            transform: scale(1.04);
        }

        .vuelto-box {
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            color: white;
            transition: all 0.3s;
        }

        .vuelto-box.positivo {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .vuelto-box.negativo {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
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
                <select class="form-control select2" name="cliente_id" required>
                    <option value="">-- Buscar cliente por nombre o DNI --</option>
                    @foreach ($clientes as $cliente)
                        <option value="{{ $cliente->id }}"
                            {{ (request('cliente_id') ?: $clienteSeleccionado) == $cliente->id ? 'selected' : '' }}>
                            {{ $cliente->nombreCliente }} {{ $cliente->apellidoCliente }} - DNI:
                            {{ $cliente->dniCliente }}
                        </option>
                    @endforeach
                </select>
                <a href="{{ route('clientes.create', ['redirect' => 'ventas.create']) }}" class="btn btn-add-client">
                    <i class="fas fa-user-plus"></i> Registrar Cliente Nuevo
                </a>
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

            <!-- Modalidad de Cobro -->
            <div class="section-header">
                <i class="fas fa-credit-card"></i> Modalidad de Cobro en Mostrador
            </div>

            <!-- Selector de Flujo: Cobro Rápido vs Apartado/Pendiente -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="payment-mode-card active" id="cardCobrar">
                        <input type="radio" name="accion" value="cobrar" id="radioCobrar" checked class="d-none">
                        <div class="d-flex align-items-center">
                            <div class="mode-icon bg-success-light me-3">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <div>
                                <div class="fw-bold fs-6 text-dark">Cobro Rápido en Caja (Recomendado)</div>
                                <small class="text-muted">Cobra ahora, registra en caja y emite comprobante en 1 clic</small>
                            </div>
                        </div>
                    </label>
                </div>
                <div class="col-md-6">
                    <label class="payment-mode-card" id="cardPendiente">
                        <input type="radio" name="accion" value="pendiente" id="radioPendiente" class="d-none">
                        <div class="d-flex align-items-center">
                            <div class="mode-icon bg-warning-light me-3">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <div class="fw-bold fs-6 text-dark">Guardar como Pendiente (Apartado)</div>
                                <small class="text-muted">Reserva el stock por tallas para cobrar en otro momento</small>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Panel de Cobro Inmediato (POS Express) -->
            <div id="panelCobroInmediato" class="p-3 bg-light rounded-3 border mb-4 shadow-sm">
                <div class="row g-3">
                    <!-- Tipo de Comprobante -->
                    <div class="col-md-6">
                        <label for="comprobante_id" class="form-label fw-bold">
                            <i class="fas fa-file-invoice text-primary me-1"></i> Tipo de Comprobante
                        </label>
                        <select name="comprobante_id" id="comprobante_id" class="form-control" style="height: 48px; border-radius: 8px; font-weight: 600;">
                            @foreach ($comprobantes as $comp)
                                <option value="{{ $comp->id }}" data-desc="{{ strtolower($comp->descripcionCOM) }}"
                                    {{ strcasecmp($comp->descripcionCOM, 'Boleta') === 0 ? 'selected' : '' }}>
                                    {{ $comp->descripcionCOM }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Importe Recibido -->
                    <div class="col-md-6">
                        <label for="importeRecibido" class="form-label fw-bold">
                            <i class="fas fa-money-bill-wave text-success me-1"></i> Importe Recibido en Soles (S/)
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white fw-bold">S/</span>
                            <input type="number" step="0.01" min="0" name="importe" id="importeRecibido" class="form-control"
                                style="height: 48px; font-size: 1.15rem; font-weight: bold;"
                                value="{{ number_format($montoTotal, 2, '.', '') }}">
                        </div>

                        <!-- Atajos Rápidos de Efectivo -->
                        <div class="d-flex gap-2 mt-2 flex-wrap">
                            <button type="button" class="btn btn-sm btn-outline-primary btn-shortcut" data-amount="{{ number_format($montoTotal, 2, '.', '') }}">
                                Exacto (S/ {{ number_format($montoTotal, 2) }})
                            </button>
                            @php
                                $billetes = [50, 100, 200];
                            @endphp
                            @foreach ($billetes as $b)
                                @if ($b >= $montoTotal)
                                    <button type="button" class="btn btn-sm btn-outline-secondary btn-shortcut" data-amount="{{ number_format($b, 2, '.', '') }}">
                                        S/ {{ $b }}.00
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Campos de Factura (Condicional si es Factura) -->
                <div id="seccionFactura" class="row g-3 mt-2" style="display: none;">
                    <div class="col-md-6">
                        <label for="ruc_factura" class="form-label fw-bold">
                            <i class="fas fa-building text-info me-1"></i> RUC de la Empresa (11 dígitos)
                        </label>
                        <input type="text" name="ruc_factura" id="ruc_factura" class="form-control" maxlength="11"
                            placeholder="Ej: 20601234567" style="height: 45px;">
                    </div>
                    <div class="col-md-6">
                        <label for="razon_social_factura" class="form-label fw-bold">
                            <i class="fas fa-file-signature text-info me-1"></i> Razón Social
                        </label>
                        <input type="text" name="razon_social_factura" id="razon_social_factura" class="form-control"
                            placeholder="Ej: TEXTILES Y MODA SAC" style="height: 45px;">
                    </div>
                </div>

                <!-- Display de Vuelto en Vivo -->
                <div class="mt-3 vuelto-box positivo" id="boxVuelto">
                    <div class="small text-uppercase opacity-75 fw-bold" id="lblVueltoTitulo">Vuelto a Entregar al Cliente</div>
                    <div class="fs-2 fw-bold" id="lblVueltoMonto">S/ 0.00</div>
                </div>
            </div>

            <!-- Botones -->
            <div class="action-buttons">
                <button type="submit" id="btnSubmitVenta" class="btn btn-proceed {{ $hayProblemasStock ?? false ? 'disabled' : '' }}"
                    {{ $hayProblemasStock ?? false ? 'disabled' : '' }}>
                    <i class="fas fa-bolt me-2"></i> Cobrar y Emitir Comprobante (1 Clic)
                </button>
                <a href="{{ route('carrito.ver') }}" class="btn btn-back-cart">
                    <i class="fas fa-shopping-cart me-2"></i> Editar Carrito
                </a>
            </div>
        </div>
    </form>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: 'Buscar cliente por nombre o DNI...',
                allowClear: true,
                width: '100%'
            });

            const montoTotal = parseFloat("{{ $montoTotal }}");
            const radioCobrar = document.getElementById('radioCobrar');
            const radioPendiente = document.getElementById('radioPendiente');
            const cardCobrar = document.getElementById('cardCobrar');
            const cardPendiente = document.getElementById('cardPendiente');
            const panelCobroInmediato = document.getElementById('panelCobroInmediato');
            const btnSubmitVenta = document.getElementById('btnSubmitVenta');
            const comprobanteSelect = document.getElementById('comprobante_id');
            const seccionFactura = document.getElementById('seccionFactura');
            const rucFactura = document.getElementById('ruc_factura');
            const razonSocialFactura = document.getElementById('razon_social_factura');
            const inputImporte = document.getElementById('importeRecibido');
            const boxVuelto = document.getElementById('boxVuelto');
            const lblVueltoTitulo = document.getElementById('lblVueltoTitulo');
            const lblVueltoMonto = document.getElementById('lblVueltoMonto');

            function actualizarModo() {
                if (radioCobrar.checked) {
                    cardCobrar.classList.add('active');
                    cardCobrar.classList.remove('active-pendiente');
                    cardPendiente.classList.remove('active', 'active-pendiente');
                    panelCobroInmediato.style.display = 'block';
                    btnSubmitVenta.innerHTML = '<i class="fas fa-bolt me-2"></i> Cobrar y Emitir Comprobante (1 Clic)';
                    btnSubmitVenta.style.background = 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)';
                } else {
                    cardPendiente.classList.add('active-pendiente');
                    cardCobrar.classList.remove('active', 'active-pendiente');
                    panelCobroInmediato.style.display = 'none';
                    btnSubmitVenta.innerHTML = '<i class="fas fa-clock me-2"></i> Guardar como Pendiente (Apartar Prenda)';
                    btnSubmitVenta.style.background = 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)';
                }
            }

            cardCobrar.addEventListener('click', function() {
                radioCobrar.checked = true;
                actualizarModo();
            });

            cardPendiente.addEventListener('click', function() {
                radioPendiente.checked = true;
                actualizarModo();
            });

            function checkFactura() {
                if (!comprobanteSelect) return;
                const selectedOpt = comprobanteSelect.options[comprobanteSelect.selectedIndex];
                const desc = (selectedOpt.text || '').toLowerCase();
                if (desc.includes('factura')) {
                    seccionFactura.style.display = 'flex';
                    rucFactura.required = radioCobrar.checked;
                    razonSocialFactura.required = radioCobrar.checked;
                } else {
                    seccionFactura.style.display = 'none';
                    rucFactura.required = false;
                    razonSocialFactura.required = false;
                }
            }

            if (comprobanteSelect) {
                comprobanteSelect.addEventListener('change', checkFactura);
                checkFactura();
            }

            function recalcularVuelto() {
                const imp = parseFloat(inputImporte.value) || 0;
                const diff = imp - montoTotal;

                if (diff >= -0.001) {
                    boxVuelto.classList.remove('negativo');
                    boxVuelto.classList.add('positivo');
                    lblVueltoTitulo.textContent = 'Vuelto a Entregar al Cliente';
                    lblVueltoMonto.textContent = 'S/ ' + Math.max(0, diff).toFixed(2);
                } else {
                    boxVuelto.classList.remove('positivo');
                    boxVuelto.classList.add('negativo');
                    lblVueltoTitulo.textContent = 'Monto Insuficiente (Falta cobrar)';
                    lblVueltoMonto.textContent = '- S/ ' + Math.abs(diff).toFixed(2);
                }
            }

            if (inputImporte) {
                inputImporte.addEventListener('input', recalcularVuelto);
                recalcularVuelto();
            }

            document.querySelectorAll('.btn-shortcut').forEach(btn => {
                btn.addEventListener('click', function() {
                    const val = this.getAttribute('data-amount');
                    if (inputImporte) {
                        inputImporte.value = val;
                        recalcularVuelto();
                    }
                });
            });

            document.getElementById('formVenta').addEventListener('submit', function(e) {
                if (radioCobrar.checked) {
                    const imp = parseFloat(inputImporte.value) || 0;
                    if (imp < (montoTotal - 0.001)) {
                        e.preventDefault();
                        alert('El importe recibido (S/ ' + imp.toFixed(2) + ') no puede ser menor al total a pagar (S/ ' + montoTotal.toFixed(2) + ').');
                        inputImporte.focus();
                        return false;
                    }

                    const selectedOpt = comprobanteSelect.options[comprobanteSelect.selectedIndex];
                    const desc = (selectedOpt.text || '').toLowerCase();
                    if (desc.includes('factura')) {
                        const ruc = rucFactura.value.trim();
                        if (!/^\d{11}$/.test(ruc)) {
                            e.preventDefault();
                            alert('El RUC para Factura debe tener exactamente 11 dígitos numéricos.');
                            rucFactura.focus();
                            return false;
                        }
                        if (!razonSocialFactura.value.trim()) {
                            e.preventDefault();
                            alert('Por favor ingrese la Razón Social para la Factura.');
                            razonSocialFactura.focus();
                            return false;
                        }
                    }
                }
            });
        });
    </script>
@stop
