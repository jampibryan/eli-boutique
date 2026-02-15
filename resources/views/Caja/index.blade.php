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
                <small class="text-muted">Registros: <span class="badge bg-dark"
                        id="totalCajas">{{ $cajas->count() }}</span></small>
            </div>
            <div>
                <a href="{{ route('cajas.pdf') }}" target="_blank" class="btn btn-boutique-dark" id="btnReporte">
                    <i class="fas fa-file-pdf"></i> Generar Reporte
                </a>
            </div>
        </div>
    </div>

    <!-- Filtros de búsqueda -->
    <div class="action-bar mt-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="fas fa-search" style="color: #D4AF37;"></i>
                    </span>
                    <input type="text" id="buscarCaja" class="form-control" placeholder="Buscar por código de caja...">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label mb-1" style="font-size:0.8rem; color:#666;">
                    <i class="fas fa-sort" style="color:#D4AF37;"></i> Orden
                </label>
                <select id="ordenCaja" class="form-select">
                    <option value="recientes" selected>Más recientes</option>
                    <option value="antiguos">Más antiguos</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label mb-1" style="font-size:0.8rem; color:#666;">
                    <i class="fas fa-calendar" style="color:#D4AF37;"></i> Desde
                </label>
                <input type="date" id="fechaDesde" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1" style="font-size:0.8rem; color:#666;">
                    <i class="fas fa-calendar-check" style="color:#D4AF37;"></i> Hasta
                </label>
                <input type="date" id="fechaHasta" class="form-control">
            </div>
            <div class="col-md-1">
                <button id="btnLimpiarFiltros" class="btn btn-outline-secondary w-100" title="Limpiar filtros">
                    <i class="fas fa-eraser"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Grid de cajas -->
    <div class="container-fluid">
        <div class="row g-3" id="cajasGrid">
            @foreach ($cajas as $caja)
                <div class="col-lg-3 col-md-4 col-sm-6 caja-item" data-codigo="{{ $caja->codigoCaja }}"
                    data-fecha="{{ $caja->fecha }}">
                    <div class="boutique-card">
                        <!-- Header con fecha -->
                        <div class="boutique-card-header text-center">
                            <h5 class="icon mb-0">
                                <i class="fas fa-calendar-day"></i> {{ \Carbon\Carbon::parse($caja->fecha)->format('d/m/Y') }}
                            </h5>
                            <small>Caja #{{ $caja->codigoCaja }}</small>
                        </div>

                        <div class="boutique-card-body">
                            <!-- Métricas financieras -->
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <div class="metric-box"
                                        style="background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%); border-color: #28a745;">
                                        <p class="metric-value" style="font-size: 1.2rem; color: #28a745;">
                                            S/ {{ number_format($caja->ingresoDiario, 2) }}
                                        </p>
                                        <p class="metric-label" style="font-size: 0.7rem;">
                                            <i class="fas fa-arrow-up"></i> Ingresos
                                        </p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="metric-box"
                                        style="background: linear-gradient(135deg, #fce4ec 0%, #fff0f0 100%); border-color: #e74c3c;">
                                        <p class="metric-value" style="font-size: 1.2rem; color: #e74c3c;">
                                            S/ {{ number_format($caja->egresoDiario, 2) }}
                                        </p>
                                        <p class="metric-label" style="font-size: 0.7rem;">
                                            <i class="fas fa-arrow-down"></i> Gastos
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Balance -->
                            <div class="metric-box mb-2"
                                style="background: linear-gradient(135deg, #fff3cd 0%, #fffbeb 100%); border-color: #D4AF37;">
                                <p class="metric-value" style="color: {{ $caja->balanceDiario >= 0 ? '#2C2C2C' : '#e74c3c' }};">
                                    S/ {{ number_format($caja->balanceDiario, 2) }}
                                </p>
                                <p class="metric-label" style="color: #6c757d;">
                                    <i class="fas fa-balance-scale"></i> Balance del Día
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
                            <!-- Botón de informe completo -->
                            <div class="mt-3 text-center">
                                <a href="{{ route('cajas.informe', $caja->id) }}" target="_blank"
                                    class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-file-alt"></i> Ver informe completo
                                </a>
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
        function ordenarCajas() {
            const grid = document.getElementById('cajasGrid');
            const items = Array.from(grid.querySelectorAll('.caja-item'));
            const orden = document.getElementById('ordenCaja').value;

            items.sort((a, b) => {
                const fechaA = a.dataset.fecha;
                const fechaB = b.dataset.fecha;
                return orden === 'recientes' ? fechaB.localeCompare(fechaA) : fechaA.localeCompare(fechaB);
            });

            items.forEach(item => grid.appendChild(item));
        }

        function filtrarCajas() {
            const searchId = document.getElementById('buscarCaja').value.trim();
            const fechaDesde = document.getElementById('fechaDesde').value;
            const fechaHasta = document.getElementById('fechaHasta').value;
            const items = document.querySelectorAll('.caja-item');
            let visibleCount = 0;

            items.forEach(item => {
                const codigo = item.dataset.codigo;
                const fecha = item.dataset.fecha;
                let mostrar = true;

                // Filtrar por código
                if (searchId !== '') {
                    const searchAsNum = parseInt(searchId);
                    const codigoNum = parseInt(codigo);

                    if (!isNaN(searchAsNum) && !isNaN(codigoNum)) {
                        mostrar = mostrar && codigoNum.toString().startsWith(searchAsNum.toString());
                    } else {
                        mostrar = mostrar && codigo.toLowerCase().includes(searchId.toLowerCase());
                    }
                }

                // Filtrar por rango de fechas
                if (fechaDesde !== '') {
                    mostrar = mostrar && fecha >= fechaDesde;
                }
                if (fechaHasta !== '') {
                    mostrar = mostrar && fecha <= fechaHasta;
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

        function actualizarReporteUrl() {
            const desde = document.getElementById('fechaDesde').value;
            const hasta = document.getElementById('fechaHasta').value;
            const btn = document.getElementById('btnReporte');
            let url = "{{ route('cajas.pdf') }}";
            const params = [];
            if (desde) params.push('desde=' + desde);
            if (hasta) params.push('hasta=' + hasta);
            if (params.length > 0) url += '?' + params.join('&');
            btn.href = url;
        }

        document.getElementById('buscarCaja').addEventListener('input', filtrarCajas);
        document.getElementById('fechaDesde').addEventListener('change', function() {
            filtrarCajas();
            actualizarReporteUrl();
        });
        document.getElementById('fechaHasta').addEventListener('change', function() {
            filtrarCajas();
            actualizarReporteUrl();
        });
        document.getElementById('ordenCaja').addEventListener('change', ordenarCajas);

        document.getElementById('btnLimpiarFiltros').addEventListener('click', function() {
            document.getElementById('buscarCaja').value = '';
            document.getElementById('fechaDesde').value = '';
            document.getElementById('fechaHasta').value = '';
            document.getElementById('ordenCaja').value = 'recientes';
            ordenarCajas();
            filtrarCajas();
            actualizarReporteUrl();
        });
    </script>
@stop
