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

    <!-- Barra de búsqueda y filtros -->
    <div class="action-bar mt-3">
        <div class="row g-3 align-items-center">
            <!-- Buscador -->
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="fas fa-search" style="color: #D4AF37;"></i>
                    </span>
                    <input type="text" id="buscarVenta" class="form-control" placeholder="Buscar por ID...">
                </div>
            </div>
            
            <!-- Ordenar por fecha -->
            <div class="col-md-3">
                <select id="ordenarVenta" class="form-select">
                    <option value="reciente">Más recientes</option>
                    <option value="antigua">Más antiguas</option>
                </select>
            </div>
            
            <!-- Filtrar por fecha -->
            <div class="col-md-3">
                <input type="date" id="filtrarFecha" class="form-control" placeholder="Filtrar por fecha">
            </div>
            
            <!-- Botón limpiar filtros -->
            <div class="col-md-3">
                <button type="button" id="limpiarFiltros" class="btn btn-boutique-dark w-100">
                    <i class="fas fa-redo"></i> Limpiar Filtros
                </button>
            </div>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Función para aplicar todos los filtros
            function aplicarFiltros() {
                console.log('Aplicando filtros...');
                const searchTerm = document.getElementById('buscarVenta').value.trim();
                const ordenar = document.getElementById('ordenarVenta').value;
                const fechaFiltro = document.getElementById('filtrarFecha').value;
                
                console.log('Ordenar:', ordenar);
                
                const grid = document.getElementById('ventasGrid');
                let items = Array.from(document.querySelectorAll('.venta-item'));
                let visibleCount = 0;
                
                console.log('Total items:', items.length);
                
                // Primero ordenar TODOS los items
                items.sort((a, b) => {
                    const fechaTextA = a.querySelector('.text-muted').textContent.trim();
                    const fechaTextB = b.querySelector('.text-muted').textContent.trim();
                    
                    // Formato: dd/mm/yyyy hh:mm AM/PM
                    const partsA = fechaTextA.match(/(\d+)\/(\d+)\/(\d+)\s+(\d+):(\d+)\s+(AM|PM)/);
                    const partsB = fechaTextB.match(/(\d+)\/(\d+)\/(\d+)\s+(\d+):(\d+)\s+(AM|PM)/);
                    
                    if (!partsA || !partsB) {
                        console.error('Error parseando fechas');
                        return 0;
                    }
                    
                    const [, diaA, mesA, anioA, horaA, minA, ampmA] = partsA;
                    const [, diaB, mesB, anioB, horaB, minB, ampmB] = partsB;
                    
                    // Convertir hora de 12h a 24h
                    let hora24A = parseInt(horaA);
                    if (ampmA === 'PM' && hora24A !== 12) hora24A += 12;
                    if (ampmA === 'AM' && hora24A === 12) hora24A = 0;
                    
                    let hora24B = parseInt(horaB);
                    if (ampmB === 'PM' && hora24B !== 12) hora24B += 12;
                    if (ampmB === 'AM' && hora24B === 12) hora24B = 0;
                    
                    // Crear Date objects con fecha y hora completa
                    const fechaA = new Date(parseInt(anioA), parseInt(mesA) - 1, parseInt(diaA), hora24A, parseInt(minA));
                    const fechaB = new Date(parseInt(anioB), parseInt(mesB) - 1, parseInt(diaB), hora24B, parseInt(minB));
                    
                    console.log('Date A:', fechaA.toLocaleString(), 'Date B:', fechaB.toLocaleString());
                    
                    if (ordenar === 'reciente') {
                        return fechaB - fechaA; // Más recientes primero
                    } else {
                        return fechaA - fechaB; // Más antiguas primero
                    }
                });
                
                // Limpiar el grid completamente
                grid.innerHTML = '';
                
                // Reinsertar items en el nuevo orden
                items.forEach(item => {
                    grid.appendChild(item);
                });
                
                console.log('Items reordenados');
                
                // Luego aplicar filtros de visibilidad
                items.forEach(item => {
                    const codigo = item.dataset.codigo;
                    let visible = true;
                    
                    // Filtrar por búsqueda de ID
                    if (searchTerm !== '') {
                        const codigoNum = parseInt(codigo);
                        const searchAsNum = parseInt(searchTerm);
                        
                        if (!isNaN(searchAsNum) && !isNaN(codigoNum)) {
                            visible = codigoNum.toString().startsWith(searchAsNum.toString());
                        } else {
                            visible = codigo.toLowerCase().includes(searchTerm.toLowerCase());
                        }
                    }
                    
                    // Filtrar por fecha
                    if (visible && fechaFiltro) {
                        const fechaTexto = item.querySelector('.text-muted').textContent.trim();
                        const fechaVenta = fechaTexto.split(' ')[0]; // Formato: dd/mm/yyyy
                        const [dia, mes, anio] = fechaVenta.split('/');
                        const fechaVentaISO = `${anio}-${mes.padStart(2, '0')}-${dia.padStart(2, '0')}`;
                        visible = fechaVentaISO === fechaFiltro;
                    }
                    
                    item.style.display = visible ? '' : 'none';
                    if (visible) visibleCount++;
                });
                
                document.getElementById('totalVentas').textContent = visibleCount;
            }

            // Event listeners
            document.getElementById('buscarVenta').addEventListener('input', aplicarFiltros);
            document.getElementById('ordenarVenta').addEventListener('change', function() {
                console.log('Select changed to:', this.value);
                aplicarFiltros();
            });
            document.getElementById('filtrarFecha').addEventListener('change', aplicarFiltros);
            
            // Limpiar filtros
            document.getElementById('limpiarFiltros').addEventListener('click', function() {
                document.getElementById('buscarVenta').value = '';
                document.getElementById('ordenarVenta').value = 'reciente';
                document.getElementById('filtrarFecha').value = '';
                aplicarFiltros();
            });
            
            // Aplicar filtros al cargar la página (orden inicial)
            aplicarFiltros();
        });
    </script>
@stop
