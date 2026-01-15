@extends('adminlte::page')

@section('title', 'Compras')

@section('content_header')
    <!-- <h1>Gestión de Compras</h1> -->
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
                <small class="text-muted">Total: <span class="badge bg-dark" id="totalCompras">{{ $compras->count() }}</span></small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('compras.create') }}" class="btn btn-boutique-gold">
                    <i class="fas fa-plus"></i> Nueva Orden
                </a>
                <a href="{{ route('compras.pdf') }}" target="_blank" class="btn btn-boutique-dark">
                    <i class="fas fa-file-pdf"></i> Generar Reporte
                </a>
            </div>
        </div>
    </div>

    <!-- Barra de búsqueda -->
    <div class="action-bar mt-3">
        <div class="input-group">
            <span class="input-group-text bg-white">
                <i class="fas fa-search" style="color: #D4AF37;"></i>
            </span>
            <input type="text" id="buscarCompra" class="form-control" placeholder="Buscar por ID, empresa o contacto...">
        </div>
    </div>

    <!-- Grid de compras -->
    <div class="container-fluid">
        <div class="row g-3" id="comprasGrid">
            @foreach ($compras as $compra)
                @php
                    $comprobanteDescripcion = $compra->pago->comprobante->descripcionCOM ?? 'Sin comprobante';
                    $estadoDescripcion = $compra->estadoTransaccion->descripcionET;
                    $pagoCompra = $compra->pago->importe ?? 0;
                    $estadoClass =
                        $estadoDescripcion == 'Pendiente'
                            ? 'status-warning'
                            : ($estadoDescripcion == 'Pagado'
                                ? 'status-info'
                                : 'status-success');
                @endphp
                <div class="col-lg-4 col-md-6 col-sm-12 compra-item"
                     data-codigo="{{ $compra->codigoCompra }}"
                     data-empresa="{{ strtolower($compra->proveedor->nombreEmpresa) }}"
                     data-contacto="{{ strtolower($compra->proveedor->nombreProveedor . ' ' . $compra->proveedor->apellidoProveedor) }}">
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
                            </div>

                            <!-- Información -->
                            <div>
                                <div class="info-row">
                                    <i class="fas fa-file-invoice icon"></i>
                                    <span class="label">Comprobante:</span>
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
                                @if ($estadoDescripcion == 'Pendiente')
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
                                    <form action="{{ route('compras.anular', $compra->id) }}" method="POST"
                                        class="anular-form" onsubmit="return confirm('¿Anular compra?');" style="flex: 1;">
                                        @csrf
                                        <button type="submit" class="btn btn-card-delete w-100">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </form>
                                @elseif($estadoDescripcion == 'Pagado')
                                    <form action="{{ route('compras.recibir', $compra) }}" method="POST"
                                        class="recibir-form" onsubmit="return confirm('¿Confirmar recepción?');">
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
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
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
        // Función para normalizar texto (eliminar acentos)
        function normalizeText(text) {
            return text.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        }

        // Búsqueda inteligente de compras
        document.getElementById('buscarCompra').addEventListener('input', function() {
            const searchTerm = this.value.trim();
            const searchTermNormalized = normalizeText(searchTerm.toLowerCase());
            const items = document.querySelectorAll('.compra-item');
            let visibleCount = 0;
            
            items.forEach(item => {
                const codigo = item.dataset.codigo;
                const empresa = normalizeText(item.dataset.empresa);
                const contacto = normalizeText(item.dataset.contacto);
                
                if (searchTerm === '') {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    let mostrar = false;
                    
                    // Buscar en texto (empresa y contacto)
                    if (empresa.includes(searchTermNormalized) || contacto.includes(searchTermNormalized)) {
                        mostrar = true;
                    }
                    
                    // Buscar por número en código (ignorando ceros)
                    const searchAsNum = parseInt(searchTerm);
                    const codigoNum = parseInt(codigo);
                    if (!isNaN(searchAsNum) && !isNaN(codigoNum)) {
                        if (codigoNum.toString().startsWith(searchAsNum.toString())) {
                            mostrar = true;
                        }
                    } else if (codigo.toLowerCase().includes(searchTerm.toLowerCase())) {
                        mostrar = true;
                    }
                    
                    if (mostrar) {
                        item.style.display = '';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                }
            });
            
            document.getElementById('totalCompras').textContent = visibleCount;
        });
    </script>
@stop
