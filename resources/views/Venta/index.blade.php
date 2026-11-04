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
                
                {{-- Botón Tiempo de Ventas desactivado temporalmente
                <a href="{{ route('tiempoVentas.pdf', request()->query()) }}" target="_blank" class="btn btn-boutique-dark">
                    <i class="fas fa-clock"></i> Tiempo de Ventas
                </a>
                --}}
                
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
                                <button type="button" class="btn btn-card-view btn-ver-detalle" data-venta-id="{{ $venta->id }}" data-detail-url="{{ route('ventas.show', $venta) }}" data-print-url="{{ route('ventas.comprobante', $venta) }}" title="Ver Detalle en Panel Lateral">
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
                                    <div class="d-flex gap-1 flex-grow-1 align-items-center">
                                        <a href="{{ route('ventas.ticket', $venta) }}" target="_blank"
                                            class="btn btn-boutique-gold btn-ticket-action flex-grow-1" title="Imprimir en formato Ticketera">
                                            <i class="fas fa-print"></i> <span>Ticket ({{ $comprobanteDescripcion }})</span>
                                        </a>
                                        <a href="{{ route('ventas.comprobante', $venta) }}" target="_blank"
                                            class="btn btn-pdf-action" title="Descargar PDF 80mm">
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

        /* Acciones de Venta Card */
        .card-actions {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            margin-top: 1rem;
            padding-top: 0.85rem;
            border-top: 1px solid #f0f0f0;
        }

        /* Botón Detalle */
        .btn-ver-detalle {
            min-height: 38px;
            padding: 0.45rem 0.75rem;
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 8px;
            border: 1px solid #17a2b8;
            background: #ffffff;
            color: #17a2b8;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 5px;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .btn-ver-detalle:hover {
            background: #17a2b8;
            color: #ffffff;
        }

        /* Botón Ticket Térmico */
        .btn-ticket-action {
            min-height: 38px;
            padding: 0.45rem 0.85rem;
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 8px;
            background: linear-gradient(135deg, #D4AF37 0%, #F4D03F 100%);
            border: none;
            color: #2C2C2C !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            text-align: center;
            gap: 7px;
            box-shadow: 0 2px 6px rgba(212, 175, 55, 0.25);
            transition: all 0.2s ease;
            text-decoration: none !important;
            white-space: nowrap;
        }
        .btn-ticket-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(212, 175, 55, 0.4);
            color: #111111 !important;
        }
        .btn-ticket-action i {
            font-size: 0.95rem;
            line-height: 1;
            margin: 0;
            display: inline-flex;
            align-items: center;
        }

        /* Botón Descarga PDF */
        .btn-pdf-action {
            width: 38px;
            min-width: 38px;
            height: 38px;
            padding: 0 !important;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            text-align: center;
            transition: all 0.2s ease;
            text-decoration: none !important;
            flex-shrink: 0;
        }
        .btn-pdf-action:hover {
            background: #fff1f2;
            border-color: #f43f5e;
            transform: translateY(-1px);
        }
        .btn-pdf-action i {
            font-size: 1.15rem;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('js/venta-detail.js') }}"></script>
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

            let detailRequest;
            document.querySelectorAll('.btn-ver-detalle').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (detailRequest) detailRequest.abort();
                    detailRequest = new AbortController();
                    const request = detailRequest;
                    const printUrl = this.dataset.printUrl;
                    document.getElementById('drawerLoader').innerHTML = '<div class="spinner-border text-warning" role="status"></div><p>Cargando venta…</p>';

                    // Mostrar Loader y abrir Drawer
                    document.getElementById('drawerLoader').style.display = 'block';
                    document.getElementById('drawerData').style.display = 'none';
                    bsOffcanvas.show();

                    VentaDetail.load(this.dataset.detailUrl, request.signal)
                        .then(sale => {
                            if (request.signal.aborted) return;
                            VentaDetail.render(sale, printUrl, document);
                            document.getElementById('drawerLoader').style.display = 'none';
                            document.getElementById('drawerData').style.display = 'block';
                        })
                        .catch(err => {
                            if (request.signal.aborted) return;
                            const message = document.createElement('div');
                            message.className = 'alert alert-danger';
                            message.textContent = err instanceof SyntaxError ? 'No se recibió un detalle válido. Vuelve a iniciar sesión.' : err.message;
                            document.getElementById('drawerLoader').replaceChildren(message);
                        });
                });
            });
        });
    </script>
@stop
