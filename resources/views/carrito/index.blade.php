@extends('adminlte::page')

@section('title', 'Carrito de Compras')

@section('content_header')
    <!-- h1>Carrito de Compras</h1> -->
@stop

@section('content')
    <a href="{{ route('productos.index') }}" class="btn btn-secondary mb-3">Seguir Comprando</a>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (empty($carrito))
        <div class="alert alert-info">
            <i class="fas fa-shopping-cart me-2"></i>
            El carrito está vacío. ¡Agrega productos para comenzar!
        </div>
    @else
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title">Productos en el Carrito</h3>
            </div>
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
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
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
                            <tr class="fila-carrito-{{ $index }} {{ !$stockSuficiente ? 'table-danger' : '' }}"
                                data-index="{{ $index }}">
                                <td class="td-producto-{{ $index }}">
                                    {{ $producto->descripcionP }}
                                    @if (!$stockSuficiente)
                                        <br><small class="text-danger fw-bold">
                                            <i class="fas fa-exclamation-triangle"></i> Stock insuficiente
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <!-- Botón Talla Anterior -->
                                        <button class="btn btn-sm btn-outline-secondary btn-cambiar-talla"
                                            data-index="{{ $index }}" data-accion="anterior" title="Talla anterior">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>

                                        <!-- Talla Actual -->
                                        <span class="badge bg-primary talla-badge-{{ $index }}"
                                            style="font-size: 1rem; padding: 0.5rem 0.75rem;">
                                            {{ $talla->descripcion }}
                                        </span>

                                        <!-- Botón Talla Siguiente -->
                                        <button class="btn btn-sm btn-outline-secondary btn-cambiar-talla"
                                            data-index="{{ $index }}" data-accion="siguiente"
                                            title="Talla siguiente">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <!-- Botón Disminuir -->
                                        <button class="btn btn-sm btn-outline-danger btn-actualizar-cantidad"
                                            data-index="{{ $index }}" data-accion="disminuir"
                                            title="Disminuir cantidad" {{ $item['cantidad'] <= 1 ? 'disabled' : '' }}>
                                            <i class="fas fa-minus"></i>
                                        </button>

                                        <!-- Cantidad Actual -->
                                        <span
                                            class="badge cantidad-badge-{{ $index }} {{ $stockSuficiente ? 'bg-success' : 'bg-danger' }}"
                                            style="font-size: 1rem; padding: 0.5rem 0.75rem;"
                                            data-stock="{{ $stockTalla }}" data-precio="{{ $producto->precioP }}"
                                            data-producto-id="{{ $item['producto_id'] }}"
                                            data-talla-id="{{ $item['talla_id'] }}">
                                            {{ $item['cantidad'] }}
                                        </span>

                                        <!-- Botón Aumentar -->
                                        <button
                                            class="btn btn-sm btn-outline-success btn-actualizar-cantidad btn-aumentar-{{ $index }}"
                                            data-index="{{ $index }}" data-accion="aumentar"
                                            data-producto-id="{{ $item['producto_id'] }}"
                                            data-talla-id="{{ $item['talla_id'] }}" title="Aumentar cantidad"
                                            {{ $item['cantidad'] >= $stockTalla ? 'disabled' : '' }}>
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="td-stock-{{ $index }}">
                                    <div>
                                        <span class="badge bg-secondary">
                                            Stock total: {{ $stockTalla }}
                                        </span>
                                        <br>
                                        <span
                                            class="badge stock-disponible-badge-{{ $index }} {{ $stockDisponible >= 0 ? 'bg-info' : 'bg-danger' }}"
                                            title="Stock después de este pedido">
                                            Quedarán: <span
                                                class="stock-disponible-valor-{{ $index }}">{{ $stockDisponible }}</span>
                                        </span>
                                    </div>
                                </td>
                                <td>S/. {{ number_format($producto->precioP, 2) }}</td>
                                <td class="td-subtotal-{{ $index }}">S/. <span
                                        class="subtotal-valor-{{ $index }}">{{ number_format($subtotal, 2) }}</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        @php
                                            $puedeSerDuplicado = $puedenDuplicarse[$item['producto_id']] ?? false;
                                        @endphp
                                        <button type="button" class="btn btn-info btn-sm btn-duplicar-item"
                                            data-index="{{ $index }}"
                                            title="{{ $puedeSerDuplicado ? 'Duplicar con siguiente talla disponible' : 'Todas las tallas ya están en el carrito' }}"
                                            {{ !$puedeSerDuplicado ? 'disabled' : '' }}>
                                            <i class="fas fa-copy"></i> Duplicar
                                        </button>
                                        <form action="{{ route('carrito.remover', $index) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Remover
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <th colspan="5" class="text-end">Total:</th>
                            <th colspan="2">S/. <span id="total-general">{{ number_format($total, 2) }}</span></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if ($hayProblemasStock)
            <div class="alert alert-warning mt-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Atención:</strong> Algunos productos en tu carrito no tienen stock suficiente. Por favor, ajusta las
                cantidades o remueve los productos antes de continuar.
            </div>
        @endif

        <div class="mt-3">
            <a href="{{ route('ventas.create') }}"
                class="btn btn-success btn-lg {{ $hayProblemasStock ? 'disabled' : '' }}">
                <i class="fas fa-shopping-cart me-2"></i> Continuar con la Venta
            </a>
        </div>
    @endif
@stop

@section('css')
    <style>
        /* Eliminar cursor de prohibido en botones deshabilitados */
        button:disabled {
            cursor: default !important;
        }
    </style>
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

                            // Actualizar stock mostrado
                            $(`.td-stock-${index} .badge.bg-secondary`).text(
                                `Stock total: ${response.stock}`);

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
