@extends('adminlte::page')

@section('title', 'Productos')

@section('content_header')
    <!--<h1>Lista de productos</h1>-->
@stop

@section('content')

    <div class="d-flex justify-content-between mb-3">
        <a href="{{ route('productos.create') }}" class="btn btn-danger">REGISTRAR PRODUCTO</a>
        <div>
            <a href="{{ route('carrito.ver') }}" class="btn btn-primary">
                <i class="fas fa-shopping-cart"></i> Ver Carrito ({{ count(session('carrito', [])) }})
            </a>
            <a href="{{ route('productos.pdf') }}" target="_blank" class="btn btn-primary">GENERAR REPORTE</a>
        </div>
    </div>

    <!-- Formulario de búsqueda por categoría -->
    <form method="GET" class="mb-3">
        <div class="form-group col-md-3"">
            <label for="categoria">Filtrar por Categoría:</label>
            <select name="categoria" id="categoria" class="form-control" onchange="this.form.submit()">
                <option value="">Todas las Categorías</option>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id }}" {{ request('categoria') == $categoria->id ? 'selected' : '' }}>
                        {{ $categoria->nombreCP }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    <div class="row">
        @foreach ($productos as $producto)
            <div class="col-md-6 mb-4">
                <div class="card" style="height: 100%; padding: 10px;">
                    <h5 class="card-title text-center mt-2 mb-2">
                        <strong>{{ $producto->categoriaProducto->nombreCP }}</strong></h5>
                    <img src="{{ $producto->imagenP }}" class="card-img-top" alt="Imagen del producto"
                        style="height: 200px; width: 100%; object-fit: contain;">
                    <div class="card-body p-2">
                        <p class="card-text text-center mb-1"><strong>Código:</strong> {{ $producto->codigoP }}</p>
                        <p class="card-text text-center mb-1"><strong>Descripción:</strong> {{ $producto->descripcionP }}
                        </p>
                        <p class="card-text text-center mb-1"><strong>Precio:</strong> S/.
                            {{ number_format($producto->precioP, 2) }}</p>
                        <p class="card-text text-center mb-2"><strong>Stock Total:</strong> {{ $producto->stock_total }}
                        </p>

                        <!-- Botón para ver detalle del producto -->
                        <button type="button" class="btn btn-info btn-sm w-100 mb-2" data-bs-toggle="modal"
                            data-bs-target="#modalDetalle{{ $producto->id }}">
                            <i class="fas fa-info-circle"></i> Ver Detalle
                        </button>

                        <!-- Botón para agregar al carrito -->
                        <button type="button" class="btn btn-success btn-sm w-100 mb-2 btn-agregar-carrito"
                            data-producto-id="{{ $producto->id }}">
                            <i class="fas fa-cart-plus"></i> Agregar al Carrito
                        </button>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('productos.edit', $producto) }}" class="btn btn-info btn-sm">Editar</a>
                            <form action="{{ route('productos.destroy', $producto) }}" method="post">
                                @csrf
                                @method('delete')
                                <button class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Modales de detalle de productos -->
    @foreach ($productos as $producto)
        <div class="modal fade" id="modalDetalle{{ $producto->id }}" tabindex="-1"
            aria-labelledby="modalDetalleLabel{{ $producto->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalDetalleLabel{{ $producto->id }}">
                            <i class="fas fa-box"></i> Detalle del Producto
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- Imagen del producto -->
                            <div class="col-md-5 text-center">
                                <img src="{{ $producto->imagenP }}" class="img-fluid rounded"
                                    alt="{{ $producto->descripcionP }}" style="max-height: 300px; object-fit: contain;">
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
                                            <td><strong><i class="fas fa-venus-mars"></i> Género:</strong></td>
                                            <td>{{ $producto->productoGenero->tipoGenero->descripcion ?? 'No especificado' }}
                                            </td>
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
                                            @endphp
                                            @foreach ($producto->tallaStocks->sortBy('talla.descripcion') as $tallaStock)
                                                @php
                                                    $stockTotal += $tallaStock->stock;
                                                @endphp
                                                <tr>
                                                    <td class="text-center">
                                                        <strong>{{ $tallaStock->talla->descripcion }}</strong></td>
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
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">
    </script>

    <script>
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
    </script>
@stop
