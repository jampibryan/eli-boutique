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
                <small class="text-muted">Total: <span class="badge bg-dark" id="totalVentas">{{ $ventas->count() }}</span></small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('carrito.ver') }}" class="btn btn-boutique-gold">
                    <i class="fas fa-cart-plus"></i> Ver Carrito
                </a>
                <a href="{{ route('ventas.pdf') }}" target="_blank" class="btn btn-boutique-dark">
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
            <input type="text" id="buscarVenta" class="form-control" placeholder="Buscar por ID de venta (ej: 1, 12)...">
        </div>
    </div>

    <!-- Grid de ventas -->
    <div class="container-fluid">
        <div class="row g-3" id="ventasGrid">
            @foreach ($ventas as $venta)
                @php
                    $comprobanteDescripcion = $venta->pago->comprobante->descripcionCOM ?? 'Sin comprobante';
                    $estadoDescripcion = $venta->estadoTransaccion->descripcionET;
                    $estadoClass =
                        $estadoDescripcion == 'Pendiente'
                            ? 'status-warning'
                            : ($estadoDescripcion == 'Pagado'
                                ? 'status-success'
                                : 'status-danger');
                @endphp
                <div class="col-lg-4 col-md-6 col-sm-12 venta-item" data-codigo="{{ $venta->codigoVenta }}">
                    <div class="boutique-card">
                        <!-- Header con estado -->
                        <div class="boutique-card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="icon"><i class="fas fa-receipt"></i> {{ $venta->codigoVenta }}</h5>
                            </div>
                            <span class="status-badge {{ $estadoClass }}">{{ $estadoDescripcion }}</span>
                        </div>

                        <div class="boutique-card-body">
                            <!-- Cliente -->
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-circle" style="width: 45px; height: 45px; font-size: 1.1rem;">
                                    {{ strtoupper(substr($venta->cliente->nombreCliente, 0, 1)) }}
                                </div>
                                <div style="flex: 1;">
                                    <strong style="color: #2C2C2C;">{{ $venta->cliente->nombreCliente }}
                                        {{ $venta->cliente->apellidoCliente }}</strong>
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
                            <div class="card-actions">
                                @if ($estadoDescripcion == 'Pendiente')
                                    <a href="{{ route('ventas.edit', $venta) }}" class="btn btn-card-edit">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <a href="{{ route('pagos.create', [$venta->id, 'venta']) }}" class="btn btn-card-view">
                                        <i class="fas fa-credit-card"></i> Pagar
                                    </a>
                                    <form action="{{ route('ventas.anular', $venta->id) }}" method="POST" style="flex: 1;"
                                        class="anular-form" onsubmit="return confirm('¿Anular venta?');">
                                        @csrf
                                        <button type="submit" class="btn btn-card-delete w-100">
                                            <i class="fas fa-ban"></i> Anular
                                        </button>
                                    </form>
                                @elseif($estadoDescripcion == 'Pagado')
                                    <a href="{{ route('ventas.comprobante', $venta) }}" target="_blank"
                                        class="btn btn-boutique-gold w-100">
                                        <i class="fas fa-print"></i> Generar {{ $comprobanteDescripcion }}
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
        // Búsqueda inteligente de ventas (ignora ceros a la izquierda)
        document.getElementById('buscarVenta').addEventListener('input', function() {
            const searchTerm = this.value.trim();
            const items = document.querySelectorAll('.venta-item');
            let visibleCount = 0;
            
            items.forEach(item => {
                const codigo = item.dataset.codigo;
                
                if (searchTerm === '') {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    // Buscar por número (ignorando ceros) o por texto exacto en código
                    const codigoNum = parseInt(codigo);
                    const searchAsNum = parseInt(searchTerm);
                    
                    // Si ambos son números válidos, comparar numéricamente
                    if (!isNaN(searchAsNum) && !isNaN(codigoNum)) {
                        // Convertir a string para comparación de inicio
                        if (codigoNum.toString().startsWith(searchAsNum.toString())) {
                            item.style.display = '';
                            visibleCount++;
                        } else {
                            item.style.display = 'none';
                        }
                    } else {
                        // Búsqueda de texto normal
                        if (codigo.toLowerCase().includes(searchTerm.toLowerCase())) {
                            item.style.display = '';
                            visibleCount++;
                        } else {
                            item.style.display = 'none';
                        }
                    }
                }
            });
            
            document.getElementById('totalVentas').textContent = visibleCount;
        });
    </script>
@stop
