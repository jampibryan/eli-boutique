@extends('adminlte::page')

@section('title', 'Productos')

@section('content_header')
    <!-- Catálogo de Productos -->
@stop

@section('content')
    <!-- Barra de acciones: Título y botones principales -->
    <div class="action-bar">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h4 class="mb-0" style="color: #2C2C2C;">
                    <i class="fas fa-store" style="color: #D4AF37;"></i> Catálogo de Productos
                </h4>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('carrito.ver') }}" class="btn btn-boutique-gold">
                    <i class="fas fa-shopping-cart"></i> Carrito ({{ count(session('carrito', [])) }})
                </a>
                <a href="{{ route('productos.pdf') }}" target="_blank" class="btn btn-boutique-dark">
                    <i class="fas fa-file-pdf"></i> Generar Reporte
                </a>
            </div>
        </div>
    </div>

    <!-- Segunda barra de acciones: Filtros y búsqueda -->
    <div class="card shadow-sm mb-4" style="border: none; border-left: 4px solid #D4AF37;">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('productos.index') }}" id="formFiltros">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <a href="{{ route('productos.create') }}" class="btn btn-boutique-gold w-100">
                            <i class="fas fa-plus-circle"></i> Nuevo Producto
                        </a>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-filter" style="color: #D4AF37;"></i>
                            </span>
                            <select name="categoria" id="categoria" class="form-select border-start-0"
                                onchange="this.form.submit()" style="cursor: pointer;">
                                <option value="">📦 Todas las Categorías</option>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}"
                                        {{ request('categoria') == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->nombreCP }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-search" style="color: #D4AF37;"></i>
                            </span>
                            <input type="text" name="search" id="buscarProducto" class="form-control" 
                                placeholder="Buscar producto..." value="{{ request('search') }}">
                            @if(request('search'))
                                <a href="{{ route('productos.index', request()->except('search', 'page')) }}" 
                                    class="input-group-text bg-white" style="cursor:pointer;">
                                    <i class="fas fa-times text-danger"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3 text-end">
                        <span class="badge bg-light text-dark px-3 py-2">
                            <i class="fas fa-boxes" style="color: #D4AF37;"></i>
                            <span id="totalProductos">{{ $productos->total() }}</span> producto(s)
                        </span>
                        <span class="badge bg-light text-dark px-3 py-2 mt-1">
                            <i class="fas fa-warehouse" style="color: #28a745;"></i>
                            <span id="totalStock">{{ $totalStock }}</span>
                            en stock
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Grid de productos premium -->
    <div class="row g-4" id="productosGrid">
        @foreach ($productos as $producto)
            <div class="col-lg-3 col-md-4 col-sm-6 producto-item">
                <div class="producto-card">
                    <!-- Badge de categoría -->
                    <div class="categoria-badge">
                        {{ $producto->categoriaProducto->nombreCP }}
                    </div>

                    <!-- Imagen del producto -->
                    <div class="producto-imagen-wrapper">
                        <img src="{{ $producto->imagenP ?? '/img/productos/Producto por defecto.webp' }}"
                            class="producto-imagen" alt="{{ $producto->descripcionP }}"
                            onerror="this.src='/img/productos/Producto por defecto.webp'">
                        <div class="producto-overlay">
                            <button type="button" class="btn btn-overlay" data-bs-toggle="modal"
                                data-bs-target="#modalDetalle{{ $producto->id }}">
                                <i class="fas fa-search-plus"></i> Ver Detalle
                            </button>
                        </div>
                    </div>

                    <!-- Info del producto -->
                    <div class="producto-info">
                        <div class="producto-header">
                            <span class="producto-codigo">{{ $producto->codigoP }}</span>
                            <span
                                class="stock-badge {{ $producto->stock_total > 10 ? 'stock-alto' : ($producto->stock_total > 0 ? 'stock-medio' : 'stock-bajo') }}">
                                <i class="fas fa-cubes"></i> {{ $producto->stock_total }}
                            </span>
                        </div>
                        <h6 class="producto-nombre">{{ $producto->descripcionP }}</h6>
                        <div class="producto-precio">S/. {{ number_format($producto->precioP, 2) }}</div>

                        <!-- Botones de acción -->
                        <div class="producto-acciones">
                            @if ($producto->stock_total > 0)
                                <button type="button" class="btn btn-carrito btn-agregar-carrito"
                                    data-producto-id="{{ $producto->id }}">
                                    <i class="fas fa-cart-plus"></i> Agregar
                                </button>
                            @else
                                <button type="button" class="btn btn-sin-stock" disabled>
                                    <i class="fas fa-times-circle"></i> Sin Stock
                                </button>
                            @endif
                            <div class="btn-group" role="group">
                                <a href="{{ route('productos.edit', $producto) }}" class="btn btn-accion" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-accion btn-eliminar"
                                    onclick="confirmarEliminacion('{{ $producto->id }}', '{{ addslashes($producto->descripcionP) }}', 'producto')"
                                    title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <form id="form-eliminar-producto-{{ $producto->id }}"
                                    action="{{ route('productos.destroy', $producto) }}" method="post"
                                    style="display: none;">
                                    @csrf
                                    @method('delete')
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if ($productos->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
            <h5 class="text-muted">No hay productos en esta categoría</h5>
        </div>
    @endif

    {{-- Paginación --}}
    {{ $productos->links('pagination.boutique') }}

    <!-- Modal de Confirmación de Eliminación -->
    <div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white border-0">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Confirmar Eliminación
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-warning border-0 shadow-sm mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>¡Atención!</strong> Esta acción marcará el registro como eliminado.
                    </div>

                    <p class="mb-3">Estás a punto de eliminar:</p>
                    <div class="alert alert-light border shadow-sm">
                        <strong id="nombreElemento" class="text-danger"></strong>
                    </div>

                    <p class="mb-2"><strong>Para confirmar, escribe:</strong></p>
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-light">
                            <i class="fas fa-keyboard"></i>
                        </span>
                        <input type="text" id="confirmacionTexto" class="form-control" placeholder="Escribe ELIMINAR"
                            autocomplete="off">
                    </div>

                    <small class="text-muted">
                        <i class="fas fa-shield-alt me-1"></i>
                        Los registros históricos seguirán mostrando esta información.
                    </small>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-danger" id="btnConfirmarEliminar" disabled>
                        <i class="fas fa-trash-alt"></i> Confirmar Eliminación
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modales de detalle de productos -->
    @foreach ($productos as $producto)
        <div class="modal fade" id="modalDetalle{{ $producto->id }}" tabindex="-1"
            aria-labelledby="modalDetalleLabel{{ $producto->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header"
                        style="background: linear-gradient(135deg, #2C2C2C 0%, #3d3d3d 100%); border: none;">
                        <h5 class="modal-title text-white" id="modalDetalleLabel{{ $producto->id }}">
                            <i class="fas fa-box" style="color: #D4AF37;"></i> Detalle del Producto

                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- Imagen del producto -->
                            <div class="col-md-5 text-center">
                                <img src="{{ $producto->imagenP ?? '/img/productos/Producto por defecto.webp' }}"
                                    class="img-fluid rounded" alt="{{ $producto->descripcionP }}"
                                    onerror="this.src='/img/productos/Producto por defecto.webp'"
                                    style="max-height: 300px; object-fit: contain;">
                            </div>

                            <!-- Información del producto -->
                            <div class="col-md-7">
                                <h4 class="mb-3">{{ $producto->descripcionP }}</h4>

                                <table class="table table-borderless table-sm">
                                    <tbody>
                                        <tr>
                                            <td><strong><i class="fas fa-barcode"></i> Código:</strong></td>
                                            <td>{{ $producto->codigoP }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong><i class="fas fa-tag"></i> Categoría:</strong></td>
                                            <td>{{ $producto->categoriaProducto->nombreCP }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong><i class="fas fa-dollar-sign"></i> Precio:</strong></td>
                                            <td><span class="badge bg-success fs-6">S/.
                                                    {{ number_format($producto->precioP, 2) }}</span></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <!-- Stock por talla -->
                                <h5 class="mt-4 mb-3"><i class="fas fa-boxes"></i> Stock por Talla</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th class="text-center">Talla</th>
                                                <th class="text-center">Stock Disponible</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $stockTotal = 0;
                                                // Orden personalizado de tallas
                                                $ordenTallas = [
                                                    'XS' => 1,
                                                    'S' => 2,
                                                    'M' => 3,
                                                    'L' => 4,
                                                    'XL' => 5,
                                                    'XXL' => 6,
                                                ];
                                                $tallasOrdenadas = $producto->tallaStocks->sortBy(function (
                                                    $tallaStock,
                                                ) use ($ordenTallas) {
                                                    $descripcion = strtoupper($tallaStock->talla->descripcion);
                                                    return $ordenTallas[$descripcion] ?? 999; // Si no está en el orden, va al final
                                                });
                                            @endphp
                                            @foreach ($tallasOrdenadas as $tallaStock)
                                                @php
                                                    $stockTotal += $tallaStock->stock;
                                                @endphp
                                                <tr>
                                                    <td class="text-center">
                                                        <strong>{{ $tallaStock->talla->descripcion }}</strong>
                                                    </td>
                                                    <td class="text-center">
                                                        <span
                                                            class="badge {{ $tallaStock->stock > 0 ? 'bg-success' : 'bg-danger' }} fs-6">
                                                            {{ $tallaStock->stock }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr class="table-secondary">
                                                <td class="text-center"><strong>TOTAL</strong></td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary fs-6">{{ $stockTotal }}</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/boutique-cards.css') }}">
    <style>
        /* === Botones Boutique === */
        .btn-boutique-gold {
            background: linear-gradient(135deg, #D4AF37 0%, #F4D03F 100%);
            border: none;
            color: #2C2C2C;
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(212, 175, 55, 0.3);
        }

        .btn-boutique-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.5);
            color: #1a1a1a;
        }

        .btn-boutique-dark {
            background: #2C2C2C;
            border: 1px solid #D4AF37;
            color: #D4AF37;
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-boutique-dark:hover {
            background: #3d3d3d;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(44, 44, 44, 0.3);
            color: #F4D03F;
        }

        /* === Card de Producto Premium === */
        .producto-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .producto-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 24px rgba(212, 175, 55, 0.2), 0 0 0 2px #D4AF37;
        }

        /* Badge de categoría */
        .categoria-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: rgba(44, 44, 44, 0.9);
            color: #D4AF37;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            z-index: 2;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Contenedor de imagen */
        .producto-imagen-wrapper {
            position: relative;
            height: 280px;
            overflow: hidden;
            background: linear-gradient(to bottom, #f8f8f8, #fff);
        }

        .producto-imagen {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .producto-card:hover .producto-imagen {
            transform: scale(1.05);
        }

        /* Overlay al hover */
        .producto-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(44, 44, 44, 0.95), rgba(44, 44, 44, 0.3));
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .producto-card:hover .producto-overlay {
            opacity: 1;
        }

        .btn-overlay {
            background: #D4AF37;
            border: none;
            color: #2C2C2C;
            padding: 12px 24px;
            border-radius: 25px;
            font-weight: 600;
            transform: translateY(10px);
            transition: all 0.3s ease;
        }

        .producto-card:hover .btn-overlay {
            transform: translateY(0);
        }

        .btn-overlay:hover {
            background: #F4D03F;
            color: #1a1a1a;
        }

        /* Info del producto */
        .producto-info {
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .producto-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .producto-codigo {
            font-size: 0.75rem;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stock-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .stock-alto {
            background: #d4edda;
            color: #155724;
        }

        .stock-medio {
            background: #fff3cd;
            color: #856404;
        }

        .stock-bajo {
            background: #f8d7da;
            color: #721c24;
        }

        .producto-nombre {
            font-size: 1rem;
            font-weight: 600;
            color: #2C2C2C;
            margin-bottom: 0.75rem;
            min-height: 48px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .producto-precio {
            font-size: 1.5rem;
            font-weight: 700;
            color: #D4AF37;
            margin-bottom: 1rem;
        }

        /* Botones de acción */
        .producto-acciones {
            display: flex;
            gap: 8px;
            margin-top: auto;
        }

        .btn-carrito {
            flex: 1;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
            padding: 0.6rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-carrito:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
            color: white;
        }

        .btn-carrito:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
        }

        .btn-sin-stock {
            flex: 1;
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border: none;
            color: white;
            padding: 0.6rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: not-allowed;
            opacity: 0.8;
        }

        .btn-accion {
            background: white;
            border: 1px solid #dee2e6;
            color: #6c757d;
            padding: 0.6rem 0.75rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-accion:hover {
            background: #f8f9fa;
            color: #495057;
            border-color: #D4AF37;
        }

        .btn-eliminar:hover {
            background: #dc3545;
            color: white;
            border-color: #dc3545;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .producto-imagen-wrapper {
                height: 220px;
            }

            .producto-precio {
                font-size: 1.25rem;
            }
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Búsqueda con debounce
            let searchTimeout;
            document.getElementById('buscarProducto').addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    document.getElementById('formFiltros').submit();
                }, 600);
            });
        });

        $(document).ready(function() {
            // Configurar CSRF token para AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Verificar productos que ya están en el carrito al cargar la página
            const productosEnCarrito = @json(collect(session('carrito', []))->pluck('producto_id')->unique()->toArray());

            productosEnCarrito.forEach(function(productoId) {
                const btn = $(`.btn-agregar-carrito[data-producto-id="${productoId}"]`);
                if (btn.length) {
                    btn.html('<i class="fas fa-check"></i> ¡Agregado!');
                    btn.removeClass('btn-success').addClass('btn-primary');
                    btn.prop('disabled', true);
                }
            });

            // Manejador para agregar al carrito
            $('.btn-agregar-carrito').on('click', function(e) {
                e.preventDefault();

                const btn = $(this);
                const productoId = btn.data('producto-id');

                // Deshabilitar botón temporalmente
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Agregando...');

                // Enviar petición AJAX
                $.ajax({
                    url: '{{ route('carrito.agregar') }}',
                    method: 'POST',
                    data: {
                        producto_id: productoId
                    },
                    success: function(response) {
                        // Mostrar mensaje de éxito y mantenerlo
                        btn.html('<i class="fas fa-check"></i> ¡Agregado!');
                        btn.removeClass('btn-success').addClass('btn-primary');
                        // Mantener el botón deshabilitado para evitar duplicados
                        btn.prop('disabled', true);

                        // Actualizar contador del carrito
                        const carritoUrl = "{{ route('carrito.ver') }}";
                        const carritoBtn = $(`a[href="${carritoUrl}"]`);
                        const currentText = carritoBtn.text();
                        const match = currentText.match(/\((\d+)\)/);
                        if (match) {
                            const currentCount = parseInt(match[1]);
                            const newCount = currentCount + 1;
                            carritoBtn.html(
                                `<i class="fas fa-shopping-cart"></i> Ver Carrito (${newCount})`
                            );
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Error al agregar al carrito.';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMsg = xhr.responseJSON.error;
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        alert(errorMsg);

                        // Restaurar botón
                        btn.html('<i class="fas fa-cart-plus"></i> Agregar al Carrito');
                        btn.prop('disabled', false);
                    }
                });
            });
        });

        // Sistema de confirmación de eliminación
        let modalEliminar;
        let elementoActual = {
            id: null,
            tipo: null
        };

        document.addEventListener('DOMContentLoaded', function() {
            modalEliminar = new bootstrap.Modal(document.getElementById('modalEliminar'));

            document.getElementById('confirmacionTexto').addEventListener('input', function() {
                const texto = this.value.trim().toUpperCase();
                document.getElementById('btnConfirmarEliminar').disabled = texto !== 'ELIMINAR';
            });

            document.getElementById('btnConfirmarEliminar').addEventListener('click', function() {
                document.getElementById(`form-eliminar-${elementoActual.tipo}-${elementoActual.id}`)
                .submit();
            });

            document.getElementById('modalEliminar').addEventListener('hidden.bs.modal', function() {
                document.getElementById('confirmacionTexto').value = '';
                document.getElementById('btnConfirmarEliminar').disabled = true;
            });
        });

        function confirmarEliminacion(id, nombre, tipo) {
            elementoActual = {
                id,
                tipo
            };
            document.getElementById('nombreElemento').textContent = nombre;
            modalEliminar.show();
            setTimeout(() => document.getElementById('confirmacionTexto').focus(), 500);
        }
    </script>
@stop
