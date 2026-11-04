@extends('adminlte::page')

@section('title', 'Ventas')

@section('content_header')
    <!-- <h1>Gestión de Ventas</h1> -->
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="alert alert-secondary" role="alert" style="font-size: 0.9rem;">
        Presiona <strong>F1</strong> para acceder a la <a href="#" class="text-primary"
            onclick="window.open('/guiaventas/index.htm', '_blank'); return false;">Guía de Ventas</a>
    </div>

    <!-- Barra de acciones -->
    <div class="action-bar">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h4 class="mb-0" style="color: #2C2C2C;">
                    <i class="fas fa-shopping-cart" style="color: #D4AF37;"></i> Ventas Registradas
                </h4>
                <small class="text-muted">Total: <span class="badge bg-dark" id="totalVentas">{{ $ventas->total() }}</span></small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('carrito.ver') }}" class="btn btn-boutique-gold">
                    <i class="fas fa-cart-plus"></i> Ver Carrito
                </a>
                
                <a href="{{ route('tiempoVentas.pdf', request()->query()) }}" target="_blank" class="btn btn-boutique-dark">
                    <i class="fas fa-clock"></i> Tiempo de Ventas
                </a>
                
                <a href="{{ route('ventas.pdf', request()->query()) }}" target="_blank" class="btn btn-boutique-dark">
                    <i class="fas fa-file-pdf"></i> Generar Reporte
                </a>
            </div>
        </div>
    </div>

    <!-- Barra de búsqueda y filtros -->
    <div class="action-bar mt-3">
        <form method="GET" action="{{ route('ventas.index') }}" id="formFiltros">
            <div class="row g-3 align-items-center">
                <!-- Buscador -->
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-search" style="color: #D4AF37;"></i>
                        </span>
                        <input type="text" name="search" id="buscarVenta" class="form-control" 
                            placeholder="Buscar por código..." value="{{ request('search') }}">
                    </div>
                </div>
                
                <!-- Ordenar por fecha -->
                <div class="col-md-3">
                    <select name="orden" id="ordenarVenta" class="form-select" onchange="this.form.submit()">
                        <option value="reciente" {{ request('orden', 'reciente') == 'reciente' ? 'selected' : '' }}>Más recientes</option>
                        <option value="antigua" {{ request('orden') == 'antigua' ? 'selected' : '' }}>Más antiguas</option>
                    </select>
                </div>
                
                <!-- Filtrar por fecha -->
                <div class="col-md-3">
                    <input type="date" name="fecha" id="filtrarFecha" class="form-control" 
                        value="{{ request('fecha') }}" onchange="this.form.submit()">
                </div>
                
                <!-- Botón limpiar filtros -->
                <div class="col-md-3">
                    <a href="{{ route('ventas.index') }}" class="btn btn-boutique-dark w-100">
                        <i class="fas fa-redo"></i> Limpiar Filtros
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Grid de ventas -->
    <div class="container-fluid">
        <div class="row g-3" id="ventasGrid">
            @foreach ($ventas as $venta)
                @php
                    $comprobanteDescripcion = $venta->pago->comprobante->descripcionCOM ?? 'Sin comprobante';
                    $estadoDescripcion = $venta->estadoTransaccion->descripcionET;
                    $badgeSoftClass =
                        $estadoDescripcion == 'Pendiente'
                            ? 'badge-soft-warning'
                            : ($estadoDescripcion == 'Pagado'
                                ? 'badge-soft-success'
                                : 'badge-soft-danger');
                    $estadoIcon =
                        $estadoDescripcion == 'Pendiente'
                            ? 'fa-clock'
                            : ($estadoDescripcion == 'Pagado'
                                ? 'fa-check-circle'
                                : 'fa-ban');
                @endphp
                <div class="col-lg-4 col-md-6 col-sm-12 venta-item">
                    <div class="boutique-card">
                        <!-- Header con estado -->
                        <div class="boutique-card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="icon"><i class="fas fa-receipt"></i> {{ $venta->codigoVenta }}</h5>
                            </div>
                            <span class="badge-soft {{ $badgeSoftClass }}">
                                <i class="fas {{ $estadoIcon }}"></i> {{ $estadoDescripcion }}
                            </span>
                        </div>

                        <div class="boutique-card-body">
                            <!-- Cliente -->
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-circle" style="width: 45px; height: 45px; font-size: 1.1rem;">
                                    @if($venta->cliente)
                                        {{ strtoupper(substr($venta->cliente->nombreCliente, 0, 1)) }}
                                    @else
                                        <i class="fas fa-user-slash"></i>
                                    @endif
                                </div>
                                <div style="flex: 1;">
                                    @if($venta->cliente)
                                        <strong style="color: #2C2C2C;">{{ $venta->cliente->nombreCliente }}
                                            {{ $venta->cliente->apellidoCliente }}</strong>
                                    @else
                                        <strong style="color: #dc3545;">Cliente Eliminado</strong>
                                    @endif
                                    <br><small
                                        class="text-muted">{{ \Carbon\Carbon::parse($venta->created_at)->format('d/m/Y h:i A') }}</small>
                                </div>
                            </div>

                            <!-- Información -->
                            <div>
                                <div class="info-row">
                                    <i class="fas fa-file-invoice icon"></i>
                                    <span class="label">Comprobante: </span>
                                    <span class="value">{{ $comprobanteDescripcion }}</span>
                                </div>
                                <div class="info-row">
                                    <i class="fas fa-dollar-sign icon"></i>
                                    <span class="label">Monto Total:</span>
                                    <span class="value" style="color: #D4AF37; font-weight: 700; font-size: 1.1rem;">S/
                                        {{ number_format($venta->montoTotal, 2) }}</span>
                                </div>
                            </div>

                            <!-- Acciones según estado -->
                            <div class="card-actions d-flex gap-1">
                                <button type="button" class="btn btn-card-view btn-ver-detalle" data-venta-id="{{ $venta->id }}" title="Ver Detalle en Panel Lateral">
                                    <i class="fas fa-eye"></i> Detalle
                                </button>
                                @if ($estadoDescripcion == 'Pendiente')
                                    <a href="{{ route('ventas.edit', $venta) }}" class="btn btn-card-edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('pagos.create', [$venta->id, 'venta']) }}" class="btn btn-card-view">
                                        <i class="fas fa-credit-card"></i> Pagar
                                    </a>
                                    <form action="{{ route('ventas.anular', $venta->id) }}" method="POST" style="flex: 1;"
                                        class="anular-form" onsubmit="return confirm('¿Anular venta?');">
                                        @csrf
                                        <button type="submit" class="btn btn-card-delete w-100">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </form>
                                @elseif($estadoDescripcion == 'Pagado')
                                    <div class="d-flex gap-1 w-100">
                                        <a href="{{ route('ventas.ticket', $venta) }}" target="_blank"
                                            class="btn btn-boutique-gold flex-grow-1" title="Imprimir en formato Ticketera">
                                            <i class="fas fa-print"></i> Ticket ({{ $comprobanteDescripcion }})
                                        </a>
                                        <a href="{{ route('ventas.comprobante', $venta) }}" target="_blank"
                                            class="btn btn-outline-secondary px-3" title="Descargar PDF 80mm">
                                            <i class="fas fa-file-pdf text-danger"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Paginación --}}
        {{ $ventas->links('pagination.boutique') }}
    </div>

    <!-- Offcanvas Detalle Venta (Panel Lateral Deslizable) -->
    <div class="offcanvas offcanvas-end offcanvas-boutique" tabindex="-1" id="offcanvasDetalleVenta" aria-labelledby="offcanvasDetalleVentaLabel">
        <div class="offcanvas-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="offcanvas-title fw-bold text-dark mb-0" id="offcanvasDetalleVentaLabel">
                    <i class="fas fa-receipt text-warning me-2"></i> Venta <span id="drawerCodigoVenta">#---</span>
                </h5>
                <small class="text-muted" id="drawerFechaVenta">--/--/---- --:--</small>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body" id="drawerContent">
            <!-- Skeleton Loader -->
            <div id="drawerLoader" class="py-4">
                <div class="skeleton-line mb-3" style="width: 70%;"></div>
                <div class="skeleton-line mb-3" style="width: 90%;"></div>
                <div class="skeleton-line mb-3" style="width: 50%;"></div>
                <div class="skeleton-line mb-3" style="width: 80%;"></div>
            </div>

            <div id="drawerData" style="display: none;">
                <!-- Info Cliente -->
                <div class="card border-0 bg-light p-3 rounded-3 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle me-3 bg-white text-dark shadow-sm" style="width: 42px; height: 42px; font-weight: bold; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                            <span id="drawerAvatar">C</span>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark" id="drawerClienteNombre">Cliente</h6>
                            <small class="text-muted" id="drawerClienteDni">DNI: ---</small>
                        </div>
                    </div>
                </div>

                <!-- Resumen de Productos -->
                <h6 class="fw-bold mb-3 text-secondary" style="font-size: 0.85rem; letter-spacing: 0.05em; text-transform: uppercase;">
                    Productos (<span id="drawerCantProductos">0</span>)
                </h6>
                <div id="drawerItemsList" class="mb-4">
                    <!-- Ítems inyectados por JS -->
                </div>

                <!-- Desglose de Totales -->
                <div class="card border-0 bg-white p-3 rounded-3 shadow-sm mb-4">
                    <div class="d-flex justify-content-between mb-2 text-muted" style="font-size: 0.9rem;">
                        <span>Subtotal:</span>
                        <span id="drawerSubtotal">S/ 0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-muted" style="font-size: 0.9rem;">
                        <span>IGV (18%):</span>
                        <span id="drawerIgv">S/ 0.00</span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between fw-bold text-dark fs-5">
                        <span>Total:</span>
                        <span class="text-warning" id="drawerTotal">S/ 0.00</span>
                    </div>
                </div>

                <!-- Comprobante -->
                <div id="drawerSunatBox" class="mb-4 p-3 rounded-3 border" style="background: #FAF9F6;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted d-block">Comprobante</small>
                            <strong id="drawerComprobanteTipo" class="text-dark">---</strong>
                        </div>
                        <div id="drawerSunatBadge"></div>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="d-grid gap-2" id="drawerActions">
                    <a href="#" id="drawerBtnImprimir" target="_blank" class="btn btn-boutique-gold fw-bold py-2">
                        <i class="fas fa-print me-1"></i> Generar / Ver Ticket
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/boutique-cards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom-boutique-ui.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('venta_rapida_exito'))
                @php
                    $infoVenta = session('venta_rapida_exito');
                @endphp
                Swal.fire({
                    title: '¡Venta Cobrada con Éxito!',
                    html: `
                        <div class="text-center py-2">
                            <div class="badge bg-success fs-6 mb-3 px-3 py-2">
                                <i class="fas fa-check-circle me-1"></i> {{ $infoVenta['codigo'] }} &bull; {{ $infoVenta['tipo_comprobante'] }}
                            </div>
                            <div class="row g-2 text-start p-3 bg-light rounded-3 mb-3 border" style="font-size: 0.95rem;">
                                <div class="col-6 text-muted">Total Venta:</div>
                                <div class="col-6 text-end fw-bold">S/ {{ $infoVenta['total'] }}</div>
                                <div class="col-6 text-muted">Importe Recibido:</div>
                                <div class="col-6 text-end fw-bold">S/ {{ $infoVenta['importe'] }}</div>
                                <div class="col-12"><hr class="my-1"></div>
                                <div class="col-6 fs-5 fw-bold text-success">VUELTO:</div>
                                <div class="col-6 fs-5 text-end fw-bold text-success">S/ {{ $infoVenta['vuelto'] }}</div>
                            </div>
                            <p class="text-muted small mb-0"><i class="fas fa-cash-register me-1"></i> Registrado en Caja Diaria con éxito.</p>
                        </div>
                    `,
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonColor: '#D4AF37',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-print me-1"></i> Imprimir Ticket (Ticketera)',
                    cancelButtonText: '<i class="fas fa-check me-1"></i> Listo / Cerrar',
                    customClass: {
                        confirmButton: 'btn btn-boutique-gold px-4 py-2 me-2',
                        cancelButton: 'btn btn-secondary px-4 py-2'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.open("{{ $infoVenta['url_ticket'] ?? $infoVenta['url_pdf'] }}", '_blank');
                    }
                });
            @endif

            // Búsqueda con debounce
            let searchTimeout;
            const searchInput = document.getElementById('buscarVenta');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        document.getElementById('formFiltros').submit();
                    }, 600);
                });
            }

            // Bootstrap Offcanvas instance
            const offcanvasEl = document.getElementById('offcanvasDetalleVenta');
            const bsOffcanvas = new bootstrap.Offcanvas(offcanvasEl);

            document.querySelectorAll('.btn-ver-detalle').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const ventaId = this.getAttribute('data-venta-id');

                    // Mostrar Loader y abrir Drawer
                    document.getElementById('drawerLoader').style.display = 'block';
                    document.getElementById('drawerData').style.display = 'none';
                    bsOffcanvas.show();

                    fetch(`/api/ventas/${ventaId}`)
                        .then(res => res.json())
                        .then(data => {
                            document.getElementById('drawerCodigoVenta').innerText = '#' + data.codigoVenta;
                            
                            const fecha = new Date(data.created_at);
                            document.getElementById('drawerFechaVenta').innerText = fecha.toLocaleString('es-PE');

                            // Cliente
                            if (data.cliente) {
                                document.getElementById('drawerClienteNombre').innerText = `${data.cliente.nombreCliente} ${data.cliente.apellidoCliente}`;
                                document.getElementById('drawerClienteDni').innerText = `DNI: ${data.cliente.dniCliente || 'Sin DNI'}`;
                                document.getElementById('drawerAvatar').innerText = data.cliente.nombreCliente.charAt(0).toUpperCase();
                            } else {
                                document.getElementById('drawerClienteNombre').innerText = 'Cliente Eliminado';
                                document.getElementById('drawerClienteDni').innerText = 'DNI: N/A';
                                document.getElementById('drawerAvatar').innerText = '?';
                            }

                            // Items
                            const itemsContainer = document.getElementById('drawerItemsList');
                            itemsContainer.innerHTML = '';

                            let cantTotal = 0;
                            if (data.detalles && data.detalles.length > 0) {
                                data.detalles.forEach(item => {
                                    cantTotal += item.cantidad;
                                    const prodNombre = item.producto ? item.producto.descripcionP : 'Producto';
                                    const tallaNom = item.talla ? item.talla.descripcionTalla : '-';
                                    
                                    const card = document.createElement('div');
                                    card.className = 'receipt-item-card d-flex justify-content-between align-items-center';
                                    card.innerHTML = `
                                        <div>
                                            <strong class="d-block text-dark" style="font-size: 0.95rem;">${prodNombre}</strong>
                                            <small class="text-muted">Talla: <span class="badge bg-light text-dark border">${tallaNom}</span> | Cant: <strong>${item.cantidad}</strong></small>
                                        </div>
                                        <div class="text-end">
                                            <strong class="text-dark">S/ ${(parseFloat(item.precioUnitario) * item.cantidad).toFixed(2)}</strong>
                                            <br><small class="text-muted">S/ ${parseFloat(item.precioUnitario).toFixed(2)} c/u</small>
                                        </div>
                                    `;
                                    itemsContainer.appendChild(card);
                                });
                            } else {
                                itemsContainer.innerHTML = '<p class="text-muted small">No hay ítems registrados.</p>';
                            }
                            document.getElementById('drawerCantProductos').innerText = cantTotal;

                            // Totales
                            document.getElementById('drawerSubtotal').innerText = 'S/ ' + parseFloat(data.subTotal).toFixed(2);
                            document.getElementById('drawerIgv').innerText = 'S/ ' + parseFloat(data.IGV).toFixed(2);
                            document.getElementById('drawerTotal').innerText = 'S/ ' + parseFloat(data.montoTotal).toFixed(2);

                            // Comprobante
                            const compTipo = (data.pago && data.pago.comprobante) ? data.pago.comprobante.descripcionCOM : 'Ticket Simple';
                            document.getElementById('drawerComprobanteTipo').innerText = compTipo;

                            // Imprimir link
                            document.getElementById('drawerBtnImprimir').href = `/ventas/${data.id}/comprobante`;

                            // Ocultar Loader y mostrar Data
                            document.getElementById('drawerLoader').style.display = 'none';
                            document.getElementById('drawerData').style.display = 'block';
                        })
                        .catch(err => {
                            console.error('Error al cargar detalle de venta:', err);
                            document.getElementById('drawerLoader').innerHTML = '<div class="alert alert-danger">Error al cargar información.</div>';
                        });
                });
            });
        });
    </script>
@stop
