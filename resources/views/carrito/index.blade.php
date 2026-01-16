@extends('adminlte::page')

@section('title', 'Carrito de Compras')

@section('content_header')
@stop

@section('css')
<style>
    .page-title {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px 30px;
        border-radius: 15px;
        margin-bottom: 25px;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }
    
    .page-title h1 {
        margin: 0;
        font-size: 28px;
        font-weight: bold;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .page-title h1 i {
        font-size: 32px;
    }
    .cart-container {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        padding: 30px;
        margin-bottom: 20px;
    }
    
    .cart-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    
    .cart-header h3 {
        margin: 0;
        font-size: 20px;
        font-weight: bold;
    }
    
    .product-card {
        background: white;
        border: 2px solid #f0f0f0;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        transition: all 0.3s;
    }
    
    .product-card:hover {
        border-color: #667eea;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
    }
    
    .product-card.stock-warning {
        border-color: #f44336;
        background: #ffebee;
    }
    
    .product-name {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
    }
    
    .product-details {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .detail-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
        align-items: center;
        text-align: center;
    }
    
    .detail-label {
        font-size: 11px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }
    
    .detail-value {
        font-size: 16px;
        font-weight: 600;
        color: #333;
    }
    
    .talla-control {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .cantidad-control {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-control {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: 2px solid;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        padding: 0;
    }
    
    .btn-control:hover:not(:disabled) {
        transform: scale(1.1);
    }
    
    .btn-control:disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }
    
    .btn-control.minus {
        border-color: #f44336;
        color: #f44336;
    }
    
    .btn-control.minus:hover:not(:disabled) {
        background: #f44336;
        color: white;
    }
    
    .btn-control.plus {
        border-color: #4caf50;
        color: #4caf50;
    }
    
    .btn-control.plus:hover:not(:disabled) {
        background: #4caf50;
        color: white;
    }
    
    .btn-control.talla {
        border-color: #667eea;
        color: #667eea;
    }
    
    .btn-control.talla:hover:not(:disabled) {
        background: #667eea;
        color: white;
    }
    
    .badge-custom {
        padding: 8px 15px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
    }
    
    .stock-info {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    
    .stock-badge {
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }
    
    .product-actions {
        display: flex;
        gap: 8px;
        margin-top: 15px;
    }
    
    .btn-duplicate {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        border: none;
        color: white;
        padding: 8px 15px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .btn-duplicate:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(56, 239, 125, 0.4);
        color: white;
    }
    
    .btn-remove {
        background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
        border: none;
        color: white;
        padding: 8px 15px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .btn-remove:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(235, 51, 73, 0.4);
        color: white;
    }
    
    .cart-summary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
        margin-top: 20px;
    }
    
    .cart-summary .total-label {
        font-size: 14px;
        margin-bottom: 5px;
    }
    
    .cart-summary .total-value {
        font-size: 32px;
        font-weight: bold;
    }
    
    .btn-checkout {
        background: white;
        color: #667eea;
        border: none;
        padding: 15px 40px;
        font-size: 18px;
        font-weight: bold;
        border-radius: 10px;
        margin-top: 15px;
        width: 100%;
        transition: all 0.3s;
    }
    
    .btn-checkout:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 255, 255, 0.3);
        color: #667eea;
    }
    
    .btn-checkout:disabled {
        background: #ccc;
        color: #666;
        cursor: not-allowed;
    }
    
    .btn-continue {
        background: white;
        border: 2px solid #667eea;
        color: #667eea;
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s;
        margin-top: 15px;
    }
    
    .btn-continue:hover {
        background: #667eea;
        color: white;
    }
    
    .empty-cart {
        text-align: center;
        padding: 60px 20px;
    }
    
    .empty-cart i {
        font-size: 80px;
        color: #ddd;
        margin-bottom: 20px;
    }
    
    .empty-cart h3 {
        color: #666;
        font-weight: 600;
        margin-bottom: 10px;
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
    <a href="{{ route('productos.index') }}" class="btn btn-continue mb-3">
        <i class="fas fa-arrow-left"></i> Seguir Comprando
    </a>

    @if (session('success'))
        <div class="alert alert-success alert-modern alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-modern alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (empty($carrito))
        <div class="cart-container">
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h3>Tu carrito está vacío</h3>
                <p class="text-muted">¡Agrega productos para comenzar!</p>
                <a href="{{ route('productos.index') }}" class="btn btn-continue mt-3">
                    <i class="fas fa-shopping-bag"></i> Explorar Productos
                </a>
            </div>
        </div>
    @else
        <div class="cart-container">
            <div class="cart-header">
                <h3><i class="fas fa-shopping-bag"></i> Tus Productos</h3>
            </div>
            @php
                $total = 0;
                $hayProblemasStock = false;
            @endphp
            @foreach ($carrito as $index => $item)
                @php
                    $producto = $productos[$item['producto_id']];
                    $talla = $tallas[$item['talla_id']];
                    $subtotal = $item['cantidad'] * $producto->precioP;
                    $total += $subtotal;

                    // Obtener stock de esta talla específica
                    $keyStock = $item['producto_id'] . '_' . $item['talla_id'];
                    $stockTalla = $stocksPorTalla[$keyStock] ?? 0;

                    // Calcular cuántas unidades de este producto+talla están en el carrito
                    $cantidadEnCarritoMismaTalla = 0;
                    foreach ($carrito as $c) {
                        if (
                            $c['producto_id'] == $item['producto_id'] &&
                            $c['talla_id'] == $item['talla_id']
                        ) {
                            $cantidadEnCarritoMismaTalla += $c['cantidad'];
                        }
                    }

                    // Stock disponible después de este pedido
                    $stockDisponible = $stockTalla - $cantidadEnCarritoMismaTalla;

                    // Verificar si hay stock suficiente
                    $stockSuficiente = $stockTalla >= $item['cantidad'];
                    if (!$stockSuficiente) {
                        $hayProblemasStock = true;
                    }
                @endphp
                
                <div class="product-card fila-carrito-{{ $index }} {{ !$stockSuficiente ? 'stock-warning' : '' }}" data-index="{{ $index }}">
                    <div class="product-name td-producto-{{ $index }}">
                        {{ $producto->descripcionP }}
                        @if (!$stockSuficiente)
                            <span class="badge bg-danger ms-2">
                                <i class="fas fa-exclamation-triangle"></i> Stock Insuficiente
                            </span>
                        @endif
                    </div>
                    
                    <div class="product-details">
                        <!-- Talla -->
                        <div class="detail-group">
                            <div class="detail-label">Talla</div>
                            <div class="talla-control">
                                <button class="btn-control talla btn-cambiar-talla"
                                    data-index="{{ $index }}" data-accion="anterior" title="Talla anterior">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <span class="badge-custom bg-primary talla-badge-{{ $index }}">
                                    {{ $talla->descripcion }}
                                </span>
                                <button class="btn-control talla btn-cambiar-talla"
                                    data-index="{{ $index }}" data-accion="siguiente" title="Talla siguiente">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Cantidad -->
                        <div class="detail-group">
                            <div class="detail-label">Cantidad</div>
                            <div class="cantidad-control">
                                <button class="btn-control minus btn-actualizar-cantidad"
                                    data-index="{{ $index }}" data-accion="disminuir"
                                    {{ $item['cantidad'] <= 1 ? 'disabled' : '' }}>
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="badge-custom cantidad-badge-{{ $index }} {{ $stockSuficiente ? 'bg-success' : 'bg-danger' }}"
                                    data-stock="{{ $stockTalla }}" data-precio="{{ $producto->precioP }}"
                                    data-producto-id="{{ $item['producto_id'] }}"
                                    data-talla-id="{{ $item['talla_id'] }}">
                                    {{ $item['cantidad'] }}
                                </span>
                                <button class="btn-control plus btn-actualizar-cantidad btn-aumentar-{{ $index }}"
                                    data-index="{{ $index }}" data-accion="aumentar"
                                    data-producto-id="{{ $item['producto_id'] }}"
                                    data-talla-id="{{ $item['talla_id'] }}"
                                    {{ $item['cantidad'] >= $stockTalla ? 'disabled' : '' }}>
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Stock -->
                        <div class="detail-group td-stock-{{ $index }}">
                            <div class="detail-label">Stock</div>
                            <div class="stock-info">
                                <span class="stock-badge bg-secondary stock-total-badge-{{ $index }}">
                                    Disponible: <span class="stock-total-valor-{{ $index }}">{{ $stockTalla }}</span>
                                </span>
                                <span class="stock-badge stock-disponible-badge-{{ $index }} {{ $stockDisponible >= 0 ? 'bg-info' : 'bg-danger' }}">
                                    Quedarán: <span class="stock-disponible-valor-{{ $index }}">{{ $stockDisponible }}</span>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Precio Unitario -->
                        <div class="detail-group">
                            <div class="detail-label">Precio Unitario</div>
                            <div class="detail-value">S/ {{ number_format($producto->precioP, 2) }}</div>
                        </div>
                        
                        <!-- Subtotal -->
                        <div class="detail-group td-subtotal-{{ $index }}">
                            <div class="detail-label">Subtotal</div>
                            <div class="detail-value" style="color: #667eea; font-size: 20px;">
                                S/ <span class="subtotal-valor-{{ $index }}">{{ number_format($subtotal, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Acciones -->
                    <div class="product-actions">
                        @php
                            $puedeSerDuplicado = $puedenDuplicarse[$item['producto_id']] ?? false;
                        @endphp
                        <button type="button" class="btn btn-duplicate btn-sm btn-duplicar-item"
                            data-index="{{ $index }}"
                            title="{{ $puedeSerDuplicado ? 'Duplicar con siguiente talla disponible' : 'Todas las tallas ya están en el carrito' }}"
                            {{ !$puedeSerDuplicado ? 'disabled' : '' }}>
                            <i class="fas fa-copy"></i> Duplicar
                        </button>
                        <form action="{{ route('carrito.remover', $index) }}" method="POST" style="display:inline; margin: 0;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-remove btn-sm" type="submit">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach

            @if ($hayProblemasStock)
                <div class="alert alert-danger alert-modern mt-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Atención:</strong> Algunos productos no tienen stock suficiente. Ajusta las cantidades antes de continuar.
                </div>
            @endif

            <div class="cart-summary">
                <div class="total-label">TOTAL A PAGAR</div>
                <div class="total-value">S/ <span id="total-general">{{ number_format($total, 2) }}</span></div>
                <small><i class="fas fa-info-circle"></i> Precio incluye IGV</small>
                
                <a href="{{ route('ventas.create') }}"
                    class="btn btn-checkout {{ $hayProblemasStock ? 'disabled' : '' }}"
                    {{ $hayProblemasStock ? 'onclick="return false;"' : '' }}>
                    <i class="fas fa-arrow-right me-2"></i> Continuar con la Venta
                </a>
            </div>
        </div>
    @endif
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Configurar CSRF token para todas las peticiones AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Manejador para botones de aumentar/disminuir
            $('.btn-actualizar-cantidad').on('click', function() {
                const btn = $(this);
                const index = btn.data('index');
                const accion = btn.data('accion');

                // Obtener datos actuales
                const badgeCantidad = $(`.cantidad-badge-${index}`);
                const cantidadActual = parseInt(badgeCantidad.text());
                const stock = parseInt(badgeCantidad.data('stock'));

                // Validar ANTES de enviar la petición
                if (accion === 'disminuir' && cantidadActual <= 1) {
                    return; // No hacer nada si ya está en el mínimo
                }

                if (accion === 'aumentar' && cantidadActual >= stock) {
                    return; // No hacer nada si ya está en el máximo
                }

                // Deshabilitar botón temporalmente
                btn.prop('disabled', true);

                $.ajax({
                    url: `/carrito/actualizar/${index}`,
                    method: 'POST',
                    data: {
                        accion: accion
                    },
                    success: function(response) {
                        if (response.success) {
                            const nuevaCantidad = response.cantidad;

                            // Actualizar la cantidad en el badge
                            badgeCantidad.text(nuevaCantidad);

                            // Obtener stock y precio del badge
                            const precio = parseFloat(badgeCantidad.data('precio'));

                            // Actualizar estado de botones según cantidad
                            const btnDisminuir = $(
                                `.btn-actualizar-cantidad[data-index="${index}"][data-accion="disminuir"]`
                                );
                            const btnAumentar = $(
                                `.btn-actualizar-cantidad[data-index="${index}"][data-accion="aumentar"]`
                                );

                            // Deshabilitar disminuir si cantidad <= 1
                            if (nuevaCantidad <= 1) {
                                btnDisminuir.prop('disabled', true);
                            } else {
                                btnDisminuir.prop('disabled', false);
                            }

                            // Deshabilitar aumentar si cantidad >= stock
                            if (nuevaCantidad >= stock) {
                                btnAumentar.prop('disabled', true);
                            } else {
                                btnAumentar.prop('disabled', false);
                            }

                            // Calcular nuevo subtotal
                            const nuevoSubtotal = nuevaCantidad * precio;
                            $(`.subtotal-valor-${index}`).text(nuevoSubtotal.toFixed(2));

                            // Actualizar stock disponible
                            const stockDisponible = stock - nuevaCantidad;
                            $(`.stock-disponible-valor-${index}`).text(stockDisponible);

                            // Actualizar color del badge de stock disponible
                            const stockBadge = $(`.stock-disponible-badge-${index}`);
                            stockBadge.removeClass('bg-info bg-danger');
                            stockBadge.addClass(stockDisponible >= 0 ? 'bg-info' : 'bg-danger');

                            // Actualizar color del badge de cantidad
                            const stockSuficiente = stock >= nuevaCantidad;
                            badgeCantidad.removeClass('bg-success bg-danger');
                            badgeCantidad.addClass(stockSuficiente ? 'bg-success' :
                            'bg-danger');

                            // Actualizar fila con color de advertencia si no hay stock
                            const fila = $(`.fila-carrito-${index}`);
                            if (!stockSuficiente) {
                                fila.addClass('table-danger');
                            } else {
                                fila.removeClass('table-danger');
                            }

                            // Recalcular total general
                            let totalGeneral = 0;
                            $('[class*="subtotal-valor-"]').each(function() {
                                const valor = parseFloat($(this).text().replace(',',
                                    ''));
                                if (!isNaN(valor)) {
                                    totalGeneral += valor;
                                }
                            });
                            $('#total-general').text(totalGeneral.toFixed(2));

                        } else {
                            // No hacer nada, el botón ya está en su límite
                        }

                        // Rehabilitar botón solo si la acción fue exitosa
                        if (response.success) {
                            btn.prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        alert(
                        'Error al actualizar la cantidad. Por favor, intente nuevamente.');
                        btn.prop('disabled', false);
                    }
                });
            });

            // Manejador para botones de cambiar talla
            $('.btn-cambiar-talla').on('click', function() {
                const btn = $(this);
                const index = btn.data('index');
                const accion = btn.data('accion');

                // Deshabilitar botón temporalmente
                btn.prop('disabled', true);

                $.ajax({
                    url: `/carrito/cambiar-talla/${index}`,
                    method: 'POST',
                    data: {
                        accion: accion
                    },
                    success: function(response) {
                        if (response.success) {
                            // Actualizar el nombre de la talla
                            $(`.talla-badge-${index}`).text(response.talla_nombre);

                            // Actualizar data attributes del badge de cantidad
                            const badgeCantidad = $(`.cantidad-badge-${index}`);
                            badgeCantidad.attr('data-stock', response.stock);
                            badgeCantidad.attr('data-talla-id', response.talla_id);
                            badgeCantidad.text(response.cantidad);

                            // Actualizar botones de cantidad según nuevo stock
                            const btnDisminuir = $(
                                `.btn-actualizar-cantidad[data-index="${index}"][data-accion="disminuir"]`
                                );
                            const btnAumentar = $(
                                `.btn-actualizar-cantidad[data-index="${index}"][data-accion="aumentar"]`
                                );

                            // Deshabilitar/habilitar botones correctamente
                            if (response.cantidad <= 1) {
                                btnDisminuir.prop('disabled', true);
                            } else {
                                btnDisminuir.prop('disabled', false);
                            }

                            if (response.cantidad >= response.stock) {
                                btnAumentar.prop('disabled', true);
                            } else {
                                btnAumentar.prop('disabled', false);
                            }

                            // Actualizar data-talla-id en botones
                            btnAumentar.attr('data-talla-id', response.talla_id);

                            // Actualizar stock total mostrado
                            $(`.stock-total-valor-${index}`).text(response.stock);

                            // Actualizar stock disponible
                            const stockDisponible = response.stock - response.cantidad;
                            $(`.stock-disponible-valor-${index}`).text(stockDisponible);

                            // Actualizar color del badge de stock
                            const stockBadge = $(`.stock-disponible-badge-${index}`);
                            stockBadge.removeClass('bg-info bg-danger');
                            stockBadge.addClass(stockDisponible >= 0 ? 'bg-info' : 'bg-danger');

                            // Actualizar color del badge de cantidad
                            const stockSuficiente = response.stock >= response.cantidad;
                            badgeCantidad.removeClass('bg-success bg-danger');
                            badgeCantidad.addClass(stockSuficiente ? 'bg-success' :
                            'bg-danger');

                            // Actualizar fila
                            const fila = $(`.fila-carrito-${index}`);
                            if (!stockSuficiente) {
                                fila.addClass('table-danger');
                            } else {
                                fila.removeClass('table-danger');
                            }

                            // Recalcular subtotal si la cantidad cambió
                            const precio = parseFloat(badgeCantidad.data('precio'));
                            const nuevoSubtotal = response.cantidad * precio;
                            $(`.subtotal-valor-${index}`).text(nuevoSubtotal.toFixed(2));

                            // Recalcular total general
                            let totalGeneral = 0;
                            $('[class*="subtotal-valor-"]').each(function() {
                                const valor = parseFloat($(this).text().replace(',',
                                    ''));
                                if (!isNaN(valor)) {
                                    totalGeneral += valor;
                                }
                            });
                            $('#total-general').text(totalGeneral.toFixed(2));
                        }

                        // Rehabilitar botón
                        btn.prop('disabled', false);
                    },
                    error: function(xhr) {
                        alert('Error al cambiar la talla. Por favor, intente nuevamente.');
                        btn.prop('disabled', false);
                    }
                });
            });

            // Manejador para duplicar item
            $('.btn-duplicar-item').on('click', function() {
                const btn = $(this);
                const index = btn.data('index');

                // Deshabilitar botón temporalmente
                btn.prop('disabled', true);
                btn.html('<i class="fas fa-spinner fa-spin"></i>');

                $.ajax({
                    url: `/carrito/duplicar/${index}`,
                    method: 'POST',
                    success: function(response) {
                        if (response.success) {
                            // Recargar la página para mostrar el item duplicado
                            location.reload();
                        } else {
                            alert(response.message || 'No se pudo duplicar el item.');
                            btn.prop('disabled', false);
                            btn.html('<i class="fas fa-copy"></i> Duplicar');
                        }
                    },
                    error: function(xhr) {
                        alert('Error al duplicar el producto. Por favor, intente nuevamente.');
                        btn.prop('disabled', false);
                        btn.html('<i class="fas fa-copy"></i> Duplicar');
                    }
                });
            });
        });
    </script>
@stop
