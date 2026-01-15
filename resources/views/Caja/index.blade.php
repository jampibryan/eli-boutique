@extends('adminlte::page')

@section('title', 'Cajas')

@section('content_header')
    <!-- <h1>Gestión de Caja</h1> -->
@stop

@section('content')
    <!-- Barra de acciones -->
    <div class="action-bar">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h4 class="mb-0" style="color: #2C2C2C;">
                    <i class="fas fa-cash-register" style="color: #D4AF37;"></i> Historial de Caja
                </h4>
                <small class="text-muted">Registros: <span class="badge bg-dark" id="totalCajas">{{ $cajas->count() }}</span></small>
            </div>
            <div>
                <a href="{{ route('cajas.pdf') }}" target="_blank" class="btn btn-boutique-dark">
                    <i class="fas fa-file-pdf"></i> Generar Reporte
                </a>
            </div>
        </div>
    </div>

    <!-- Barra de búsqueda -->
    <div class="action-bar mt-3">
        <div class="row g-2">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="fas fa-search" style="color: #D4AF37;"></i>
                    </span>
                    <input type="text" id="buscarCaja" class="form-control" placeholder="Buscar por ID de caja...">
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="fas fa-calendar" style="color: #D4AF37;"></i>
                    </span>
                    <input type="date" id="buscarFecha" class="form-control" placeholder="Seleccionar fecha...">
                </div>
            </div>
        </div>
    </div>

    <!-- Grid de cajas -->
    <div class="container-fluid">
        <div class="row g-3" id="cajasGrid">
            @foreach ($cajas as $caja)
                <div class="col-lg-3 col-md-4 col-sm-6 caja-item"
                     data-codigo="{{ $caja->codigoCaja }}"
                     data-fecha="{{ $caja->fecha }}">
                    <div class="boutique-card">
                        <!-- Header con fecha -->
                        <div class="boutique-card-header text-center">
                            <h5 class="icon mb-0">
                                <i class="fas fa-calendar-day"></i> {{ $caja->codigoCaja }}
                            </h5>
                            <small>{{ \Carbon\Carbon::parse($caja->fecha)->format('d/m/Y') }}</small>
                        </div>

                        <div class="boutique-card-body">
                            <!-- Métricas principales -->
                            <div class="metric-box mb-3"
                                style="background: linear-gradient(135deg, #fff3cd 0%, #fffbeb 100%); border-color: #D4AF37;">
                                <p class="metric-value" style="color: #2C2C2C;">
                                    S/ {{ number_format($caja->ingresoDiario, 2) }}
                                </p>
                                <p class="metric-label" style="color: #6c757d;">
                                    <i class="fas fa-money-bill-wave"></i> Ingreso Total
                                </p>
                            </div>

                            <!-- Métricas secundarias -->
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="metric-box" style="background: #e8f5e9; border-color: #28a745;">
                                        <p class="metric-value" style="font-size: 1.5rem; color: #28a745;">
                                            {{ $caja->clientesHoy }}
                                        </p>
                                        <p class="metric-label" style="font-size: 0.75rem;">
                                            <i class="fas fa-users"></i> Clientes
                                        </p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="metric-box" style="background: #d1ecf1; border-color: #17a2b8;">
                                        <p class="metric-value" style="font-size: 1.5rem; color: #17a2b8;">
                                            {{ $caja->productosVendidos }}
                                        </p>
                                        <p class="metric-label" style="font-size: 0.75rem;">
                                            <i class="fas fa-box"></i> Productos
                                        </p>
                                    </div>
                                </div>
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

    <style>
        body {
            background: #f4f6f9;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filtrarCajas() {
            const searchId = document.getElementById('buscarCaja').value.trim();
            const searchFecha = document.getElementById('buscarFecha').value;
            const items = document.querySelectorAll('.caja-item');
            let visibleCount = 0;
            
            items.forEach(item => {
                const codigo = item.dataset.codigo;
                const fecha = item.dataset.fecha;
                let mostrar = true;
                
                // Filtrar por ID (ignorando ceros a la izquierda)
                if (searchId !== '') {
                    const searchAsNum = parseInt(searchId);
                    const codigoNum = parseInt(codigo);
                    
                    if (!isNaN(searchAsNum) && !isNaN(codigoNum)) {
                        mostrar = mostrar && codigoNum.toString().startsWith(searchAsNum.toString());
                    } else {
                        mostrar = mostrar && codigo.toLowerCase().includes(searchId.toLowerCase());
                    }
                }
                
                // Filtrar por fecha
                if (searchFecha !== '') {
                    mostrar = mostrar && fecha === searchFecha;
                }
                
                if (mostrar) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });
            
            document.getElementById('totalCajas').textContent = visibleCount;
        }
        
        document.getElementById('buscarCaja').addEventListener('input', filtrarCajas);
        document.getElementById('buscarFecha').addEventListener('change', filtrarCajas);
    </script>
@stop
