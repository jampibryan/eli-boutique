@extends('adminlte::page')

@section('title', 'Compras')

@section('content_header')
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Barra de acciones -->
    <div class="action-bar">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h4 class="mb-0" style="color: #2C2C2C;">
                    <i class="fas fa-box" style="color: #D4AF37;"></i> Órdenes de Compra
                </h4>
                <small class="text-muted">Total: <span class="badge bg-dark" id="totalCompras">{{ $compras->total() }}</span></small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('compras.create') }}" class="btn btn-boutique-gold">
                    <i class="fas fa-plus"></i> Nueva Orden
                </a>
                <!--
                <a href="{{ route('tiempoCompras.pdf') }}" target="_blank" class="btn btn-boutique-dark">
                    <i class="fas fa-clock"></i> Tiempo de Orden de Compras
                </a>
                -->
                <a href="{{ route('compras.pdf') }}" target="_blank" class="btn btn-boutique-dark">
                    <i class="fas fa-file-pdf"></i> Generar Reporte
                </a>
            </div>
        </div>
    </div>

    <!-- Barra de búsqueda y filtros -->
    <div class="action-bar mt-3">
        <form method="GET" action="{{ route('compras.index') }}" id="formFiltros">
            <div class="row g-3 align-items-center">
                <!-- Buscador -->
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-search" style="color: #D4AF37;"></i>
                        </span>
                        <input type="text" name="search" id="buscarCompra" class="form-control" 
                            placeholder="Buscar por código..." value="{{ request('search') }}">
                    </div>
                </div>
                
                <!-- Filtrar por estado -->
                <div class="col-md-3">
                    <select name="estado" id="filtrarEstado" class="form-select" onchange="this.form.submit()">
                        <option value="">Todos los estados</option>
                        <option value="Borrador" {{ request('estado') == 'Borrador' ? 'selected' : '' }}>Borrador</option>
                        <option value="Enviada" {{ request('estado') == 'Enviada' ? 'selected' : '' }}>Enviada</option>
                        <option value="Cotizada" {{ request('estado') == 'Cotizada' ? 'selected' : '' }}>Cotizada</option>
                        <option value="Aprobada" {{ request('estado') == 'Aprobada' ? 'selected' : '' }}>Aprobada</option>
                        <option value="Recibida" {{ request('estado') == 'Recibida' ? 'selected' : '' }}>Recibida</option>
                        <option value="Pagada" {{ request('estado') == 'Pagada' ? 'selected' : '' }}>Pagada</option>
                        <option value="Anulada" {{ request('estado') == 'Anulada' ? 'selected' : '' }}>Anulada</option>
                        <option value="Pendiente" {{ request('estado') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                    </select>
                </div>
                
                <!-- Ordenar por fecha -->
                <div class="col-md-3">
                    <select name="orden" id="ordenarCompra" class="form-select" onchange="this.form.submit()">
                        <option value="reciente" {{ request('orden', 'reciente') == 'reciente' ? 'selected' : '' }}>Más recientes</option>
                        <option value="antigua" {{ request('orden') == 'antigua' ? 'selected' : '' }}>Más antiguas</option>
                    </select>
                </div>
                
                <!-- Botón limpiar filtros -->
                <div class="col-md-3">
                    <a href="{{ route('compras.index') }}" class="btn btn-boutique-dark w-100">
                        <i class="fas fa-redo"></i> Limpiar Filtros
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Grid de compras -->
    <div class="container-fluid">
        <div class="row g-3" id="comprasGrid">
            @foreach ($compras as $compra)
                @php
                    $estadoDescripcion = $compra->estadoTransaccion->descripcionET;
                    // Determinar clase de color según el estado
                    $estadoClass = match($estadoDescripcion) {
                        'Borrador' => 'status-warning',
                        'Enviada' => 'status-info',
                        'Cotizada', 'Aprobada' => 'status-primary',
                        'Pagada' => 'status-success',
                        'Recibida' => 'status-success',
                        'Anulada' => 'status-danger',
                        default => 'status-secondary'
                    };
                    
                    // Definir descripción del comprobante
                    $comprobanteDescripcion = $compra->comprobante->descripcionCOM ?? 'Por definir';
                    
                    // Definir monto total de la compra
                    $pagoCompra = $compra->total ?? 0;
                @endphp
                <div class="col-lg-4 col-md-6 col-sm-12 compra-item">
                    <div class="boutique-card">
                        <!-- Header con estado -->
                        <div class="boutique-card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="icon"><i class="fas fa-file-invoice"></i> {{ $compra->codigoCompra }}</h5>
                            </div>
                            <span class="status-badge {{ $estadoClass }}">{{ $estadoDescripcion }}</span>
                        </div>

                        <div class="boutique-card-body">
                            <!-- Proveedor -->
                            <div class="d-flex align-items-center mb-3">
                                @if($compra->proveedor)
                                    <div class="avatar-circle"
                                        style="width: 45px; height: 45px; font-size: 1.1rem; background: linear-gradient(135deg, #28a745, #5cb85c);">
                                        {{ strtoupper(substr($compra->proveedor->nombreEmpresa ?? $compra->proveedor->nombreProveedor, 0, 1)) }}
                                    </div>
                                    <div style="flex: 1;">
                                        <strong style="color: #2C2C2C;">{{ $compra->proveedor->nombreEmpresa }}</strong>
                                        <br><small style="color: #6c757d;">{{ $compra->proveedor->nombreProveedor }}
                                            {{ $compra->proveedor->apellidoProveedor }}</small>
                                        <br><small
                                            class="text-muted">{{ \Carbon\Carbon::parse($compra->created_at)->format('d/m/Y') }}</small>
                                    </div>
                                @else
                                    <div class="avatar-circle"
                                        style="width: 45px; height: 45px; font-size: 1.1rem; background: linear-gradient(135deg, #dc3545, #c82333);">
                                        <i class="fas fa-truck-slash"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <strong style="color: #dc3545;">Proveedor Eliminado</strong>
                                        <br><small class="text-muted">{{ \Carbon\Carbon::parse($compra->created_at)->format('d/m/Y') }}</small>
                                    </div>
                                @endif
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
                                    <span class="value" style="color: #28a745; font-weight: 700; font-size: 1.1rem;">S/
                                        {{ number_format($pagoCompra, 2) }}</span>
                                </div>
                            </div>

                            <!-- Acciones según estado -->
                            <div class="card-actions">
                                {{-- Borrador: Editar, Enviar, Eliminar --}}
                                @if($estadoDescripcion == 'Borrador')
                                    <a href="{{ route('ordenCompras.pdf', $compra) }}" target="_blank"
                                        class="btn btn-card-view">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <a href="{{ route('compras.edit', $compra) }}" class="btn btn-card-edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('compras.enviar', $compra) }}" method="POST" style="flex: 2;"
                                        id="form-enviar-{{ $compra->id }}">
                                        @csrf
                                        <button type="button" class="btn btn-success w-100 btn-accion-compra"
                                            data-accion="enviar" 
                                            data-id="{{ $compra->id }}" 
                                            data-nombre="Compra #{{ $compra->codigoCompra }}">
                                            <i class="fas fa-paper-plane"></i> Enviar
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-card-delete" 
                                        onclick="confirmarEliminacion('{{ $compra->id }}', 'Compra #{{ $compra->codigoCompra }}', 'compra')">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                    <form id="form-eliminar-compra-{{ $compra->id }}" 
                                        action="{{ route('compras.anular', $compra->id) }}" 
                                        method="POST" style="display: none;">
                                        @csrf
                                    </form>

                                {{-- Enviada: Ver Orden, Cotizar (simulado) --}}
                                @elseif($estadoDescripcion == 'Enviada')
                                    <a href="{{ route('ordenCompras.pdf', $compra) }}" target="_blank"
                                        class="btn btn-card-view">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <a href="{{ route('compras.cotizar', $compra) }}" class="btn btn-warning w-100" style="flex: 3;">
                                        <i class="fas fa-file-invoice-dollar"></i> Cotizar
                                    </a>

                                {{-- Cotizada: Ver Cotización, Aprobar --}}
                                @elseif($estadoDescripcion == 'Cotizada')
                                    <a href="{{ route('ordenCompras.pdf', $compra) }}" target="_blank"
                                        class="btn btn-card-view">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    @if($compra->pdf_cotizacion)
                                        <a href="{{ asset('storage/' . $compra->pdf_cotizacion) }}" target="_blank"
                                            class="btn btn-warning">
                                            <i class="fas fa-file-invoice"></i> Ver Cotización
                                        </a>
                                    @endif
                                    <form action="{{ route('compras.aprobar', $compra) }}" method="POST" style="flex: 1;"
                                        id="form-aprobar-{{ $compra->id }}">
                                        @csrf
                                        <button type="button" class="btn btn-info w-100"
                                            onclick="confirmarAccion('aprobar', '{{ $compra->id }}', 'Compra #{{ $compra->codigoCompra }}')">
                                            <i class="fas fa-check"></i> Aprobar
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-card-delete" 
                                        onclick="confirmarEliminacion('{{ $compra->id }}', 'Compra #{{ $compra->codigoCompra }}', 'compra')">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                    <form id="form-eliminar-compra-{{ $compra->id }}" 
                                        action="{{ route('compras.anular', $compra->id) }}" 
                                        method="POST" style="display: none;">
                                        @csrf
                                    </form>

                                {{-- Aprobada: Recibir mercaderia primero --}}
                                @elseif($estadoDescripcion == 'Aprobada')
                                    <a href="{{ route('ordenCompras.pdf', $compra) }}" target="_blank"
                                        class="btn btn-card-view">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <form action="{{ route('compras.recibir', $compra) }}" method="POST" style="flex: 3;"
                                        id="form-recibir-{{ $compra->id }}">
                                        @csrf
                                        <button type="button" class="btn btn-boutique-gold w-100 btn-accion-compra"
                                            data-accion="recibir" 
                                            data-id="{{ $compra->id }}" 
                                            data-nombre="Compra #{{ $compra->codigoCompra }}">
                                            <i class="fas fa-box-open"></i> Recibir Mercaderia
                                        </button>
                                    </form>

                                {{-- Recibida: Ahora si pagar --}}
                                @elseif($estadoDescripcion == 'Recibida')
                                    <a href="{{ route('ordenCompras.pdf', $compra) }}" target="_blank"
                                        class="btn btn-card-view">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <a href="{{ route('pagos.create', [$compra->id, 'compra']) }}" class="btn btn-success w-100" style="flex: 3;">
                                        <i class="fas fa-credit-card"></i> Pagar al Proveedor
                                    </a>

                                {{-- Pagada o Anulada: Solo ver --}}
                                @elseif($estadoDescripcion == 'Pagada' || $estadoDescripcion == 'Anulada')
                                    <a href="{{ route('ordenCompras.pdf', $compra) }}" target="_blank"
                                        class="btn btn-card-view" style="flex: 2;">
                                        <i class="fas fa-file-pdf"></i> Ver Orden
                                    </a>
                                    @if($compra->pdf_cotizacion)
                                        <a href="{{ asset('storage/' . $compra->pdf_cotizacion) }}" target="_blank"
                                            class="btn btn-warning" style="flex: 2;">
                                            <i class="fas fa-file-invoice"></i> Ver Cotización
                                        </a>
                                    @endif
                                    @if($estadoDescripcion == 'Pagada')
                                        <span class="badge bg-success p-2" style="flex: 1; font-size: 0.9rem;">
                                            <i class="fas fa-check-circle"></i> Completada
                                        </span>
                                    @else
                                        <span class="badge bg-danger p-2" style="flex: 1; font-size: 0.9rem;">
                                            <i class="fas fa-ban"></i> Anulada
                                        </span>
                                    @endif

                                {{-- Estados del flujo anterior (Pendiente, Pagado, Recibido) --}}
                                @elseif($estadoDescripcion == 'Pendiente')
                                    <a href="{{ route('ordenCompras.pdf', $compra) }}" target="_blank"
                                        class="btn btn-card-view" style="flex: 2;">
                                        <i class="fas fa-print"></i> Orden
                                    </a>
                                    <a href="{{ route('compras.edit', $compra) }}" class="btn btn-card-edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('pagos.create', [$compra->id, 'compra']) }}" class="btn"
                                        style="flex: 1; background: white; border: 1px solid #28a745; color: #28a745;">
                                        <i class="fas fa-credit-card"></i> Pagar
                                    </a>
                                    <button type="button" class="btn btn-card-delete w-100" 
                                        onclick="confirmarEliminacion('{{ $compra->id }}', 'Compra #{{ $compra->codigoCompra }}', 'compra')" 
                                        style="flex: 1;">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                    <form id="form-eliminar-compra-{{ $compra->id }}" 
                                        action="{{ route('compras.anular', $compra->id) }}" 
                                        method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                @elseif(id="form-recibir-pagado-{{ $compra->id }}">
                                        @csrf
                                        <button type="button" class="btn btn-boutique-gold w-100"
                                            onclick="confirmarAccion('recibir', '{{ $compra->id }}', 'Compra #{{ $compra->codigoCompra }}')r recepción?');">
                                        @csrf
                                        <button type="submit" class="btn btn-boutique-gold w-100">
                                            <i class="fas fa-check-circle"></i> Confirmar Recepción
                                        </button>
                                    </form>
                                @elseif($estadoDescripcion == 'Recibido')
                                    <a href="{{ route('ordenCompras.pdf', $compra) }}" target="_blank"
                                        class="btn btn-boutique-dark w-100">
                                        <i class="fas fa-file-pdf"></i> Ver Orden de Compra
                                    </a>

                                {{-- Estados adicionales (En Tránsito, etc.) --}}
                                @else
                                    <a href="{{ route('ordenCompras.pdf', $compra) }}" target="_blank"
                                        class="btn btn-boutique-dark w-100">
                                        <i class="fas fa-file-pdf"></i> Ver Orden
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Paginación --}}
        {{ $compras->links('pagination.boutique') }}
    </div>

    <!-- Modal de Confirmación de Anulación -->
    <div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white border-0">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Confirmar Anulación
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-warning border-0 shadow-sm mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>¡Atención!</strong> Esta acción anulará permanentemente la compra.
                    </div>
                    
                    <p class="mb-3">Estás a punto de anular:</p>
                    <div class="alert alert-light border shadow-sm">
                        <strong id="nombreElemento" class="text-danger"></strong>
                    </div>
                    
                    <p class="mb-2"><strong>Para confirmar, escribe:</strong></p>
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-light">
                            <i class="fas fa-keyboard"></i>
                        </span>
                        <input type="text" id="confirmacionTexto" class="form-control" 
                            placeholder="Escribe ANULAR" autocomplete="off">
                    </div>
                    
                    <small class="text-muted">
                        <i class="fas fa-shield-alt me-1"></i>
                        La compra quedará con estado "Anulado" en el historial.
                    </small>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-danger" id="btnConfirmarEliminar" disabled>
                        <i class="fas fa-ban"></i> Confirmar Anulación
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación de Acciones -->
    <div class="modal fade" id="modalConfirmar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0" id="modalConfirmarHeader">
                    <h5 class="modal-title" id="modalConfirmarTitulo">
                        <i class="fas fa-question-circle me-2"></i>
                        Confirmar Acción
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert border-0 shadow-sm mb-3" id="modalConfirmarAlerta">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong id="modalConfirmarMensaje"></strong>
                    </div>
                    
                    <p class="mb-3" id="modalConfirmarPregunta"></p>
                    <div class="alert alert-light border shadow-sm">
                        <strong id="modalConfirmarNombre"></strong>
                    </div>
                    
                    <div class="alert alert-warning bg-light border-warning mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        <small id="modalConfirmarNotaTexto"></small>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="button" class="btn" id="btnConfirmarAccion">
                        <i class="fas fa-check"></i> Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/boutique-cards.css') }}">
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
        let modalEliminar;
        let modalConfirmar;
        let elementoActual = {id: null, tipo: null};
        let accionActual = {tipo: null, id: null};

        document.addEventListener('DOMContentLoaded', function() {
            modalEliminar = new bootstrap.Modal(document.getElementById('modalEliminar'));
            modalConfirmar = new bootstrap.Modal(document.getElementById('modalConfirmar'));
            
            // Event delegation para botones de accion
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-accion-compra') || e.target.closest('.btn-accion-compra')) {
                    const btn = e.target.classList.contains('btn-accion-compra') ? e.target : e.target.closest('.btn-accion-compra');
                    const accion = btn.dataset.accion;
                    const id = btn.dataset.id;
                    const nombre = btn.dataset.nombre;
                    confirmarAccion(accion, id, nombre);
                }
            });
            
            // Habilitar/deshabilitar botón según el texto ingresado (ANULAR para compras)
            document.getElementById('confirmacionTexto').addEventListener('input', function() {
                const texto = this.value.trim().toUpperCase();
                document.getElementById('btnConfirmarEliminar').disabled = texto !== 'ANULAR';
            });

            // Confirmar anulación
            document.getElementById('btnConfirmarEliminar').addEventListener('click', function() {
                document.getElementById(`form-eliminar-${elementoActual.tipo}-${elementoActual.id}`).submit();
            });

            // Limpiar modal al cerrarse
            document.getElementById('modalEliminar').addEventListener('hidden.bs.modal', function() {
                document.getElementById('confirmacionTexto').value = '';
                document.getElementById('btnConfirmarEliminar').disabled = true;
            });

// Confirmar accion
            document.getElementById('btnConfirmarAccion').addEventListener('click', function() {
                const formId = 'form-' + accionActual.tipo + '-' + accionActual.id;
                const form = document.getElementById(formId);
                if (form) {
                    form.submit();
                } else {
                    alert('Error: No se encontro el formulario ' + formId);
                }
            });

            // Búsqueda con debounce
            let searchTimeout;
            document.getElementById('buscarCompra').addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    document.getElementById('formFiltros').submit();
                }, 600);
            });
        });

        function confirmarAccion(accion, id, nombre) {
            accionActual = {tipo: accion, id: id};
            
            var configs = {};
            configs.enviar = {
                titulo: 'Enviar Orden al Proveedor',
                mensaje: 'Esta a punto de enviar esta orden',
                pregunta: 'La orden sera marcada como Enviada:',
                nota: 'Despues de enviar, podra proceder a cargar la cotizacion del proveedor.',
                color: 'bg-success',
                textColor: 'text-white',
                btnClass: 'btn-success',
                btnText: 'Enviar Orden'
            };
            configs.aprobar = {
                titulo: 'Aprobar Cotizacion',
                mensaje: 'Esta a punto de aprobar esta cotizacion',
                pregunta: 'Se aprobara la cotizacion de:',
                nota: 'Despues de aprobar, debera proceder a registrar el pago de la orden.',
                color: 'bg-info',
                textColor: 'text-white',
                btnClass: 'btn-info',
                btnText: 'Aprobar Cotizacion'
            };
            configs.recibir = {
                titulo: 'Confirmar Recepcion',
                mensaje: 'Esta a punto de confirmar la recepcion',
                pregunta: 'Se actualizara el stock de los productos de:',
                nota: 'El stock se incrementara automaticamente. Esta accion no se puede deshacer.',
                color: 'bg-warning',
                textColor: 'text-dark',
                btnClass: 'btn-success',
                btnText: 'Confirmar Recepcion'
            };
            
            var config = configs[accion];
            document.getElementById('modalConfirmarTitulo').innerHTML = '<i class="fas fa-question-circle me-2"></i>' + config.titulo;
            document.getElementById('modalConfirmarHeader').className = 'modal-header border-0 ' + config.color + ' ' + config.textColor;
            document.getElementById('modalConfirmarMensaje').textContent = config.mensaje;
            document.getElementById('modalConfirmarPregunta').textContent = config.pregunta;
            document.getElementById('modalConfirmarNombre').textContent = nombre;
            document.getElementById('modalConfirmarNotaTexto').textContent = config.nota;
            document.getElementById('btnConfirmarAccion').className = 'btn ' + config.btnClass;
            document.getElementById('btnConfirmarAccion').innerHTML = '<i class="fas fa-check"></i> ' + config.btnText;
            
            modalConfirmar.show();
        }

        function confirmarEliminacion(id, nombre, tipo) {
            elementoActual = { id, tipo };
            document.getElementById('nombreElemento').textContent = nombre;
            modalEliminar.show();
            setTimeout(() => document.getElementById('confirmacionTexto').focus(), 500);
        }
    </script>
@stop
