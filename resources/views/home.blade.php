@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
{{-- <h1 class="text-center">Reporte Diario</h1> --}}
@stop


@section('content')
<div class="dashboard-background">
    <div class="container">
        <!-- Sistema de Control de Caja -->
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6">
                <div class="caja-control-card">
                    <div class="caja-status">
                        <div class="status-indicator {{ $cajaAbierta ? 'active' : ($cajaCerrada ? 'closed' : 'pending') }}">
                            <i class="fas {{ $cajaAbierta ? 'fa-check-circle' : ($cajaCerrada ? 'fa-lock' : 'fa-clock') }}"></i>
                        </div>
                        <div class="status-info">
                            <h5 class="mb-1 fw-bold">Estado de Caja</h5>
                            <p class="mb-0 {{ $cajaAbierta ? 'text-success' : ($cajaCerrada ? 'text-danger' : 'text-warning') }}">
                                @if($cajaAbierta)
                                    Caja Abierta
                                @elseif($cajaCerrada)
                                    Caja Cerrada
                                @else
                                    Pendiente de Apertura
                                @endif
                            </p>
                            <small class="text-muted">{{ now()->format('d/m/Y') }}</small>
                        </div>
                    </div>
                    
                    <div class="caja-actions">
                        <form action="{{ route('caja.abrir') }}" method="POST" class="caja-form">
                            @csrf
                            <button 
                                type="submit" 
                                class="btn-caja btn-abrir"
                                {{ $cajaAbierta || $cajaCerrada ? 'disabled' : '' }}>
                                <div class="btn-icon">
                                    <i class="fas fa-lock-open"></i>
                                </div>
                                <span class="btn-text">Abrir Caja</span>
                            </button>
                        </form>
                        
                        <form action="{{ route('caja.cerrar') }}" method="POST" class="caja-form">
                            @csrf
                            <input type="hidden" name="clientesHoy" value="{{ $clientesCount }}">
                            <input type="hidden" name="productosVendidos" value="{{ $productosCount }}">
                            <input type="hidden" name="ingresoDiario" value="{{ $ingresoDiario }}">
                            <button 
                                type="submit" 
                                class="btn-caja btn-cerrar"
                                {{ !$cajaAbierta ? 'disabled' : '' }}>
                                <div class="btn-icon">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <span class="btn-text">Cerrar Caja</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    <!-- Mensajes de éxito y error -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif


    <!-- Sección: Panel Financiero Mejorado -->
    <div class="row mt-5 mb-5">
        <div class="col-12">
            <div class="card shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">
                <div class="card-header bg-gradient-primary text-white py-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h4 class="mb-0"><i class="fas fa-chart-line me-2"></i>Panel Financiero del Día</h4>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Columna Izquierda: KPIs -->
                        <div class="col-lg-5">
                            <div class="row g-3">
                                <!-- Clientes de hoy -->
                                <div class="col-6">
                                    <div class="metric-card">
                                        <div class="metric-icon bg-primary">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <div class="metric-content">
                                            <span class="metric-label">Clientes</span>
                                            <h3 class="metric-value">{{ $clientesCount }}</h3>
                                            <small class="text-muted">únicos hoy</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Productos vendidos -->
                                <div class="col-6">
                                    <div class="metric-card">
                                        <div class="metric-icon bg-success">
                                            <i class="fas fa-box-open"></i>
                                        </div>
                                        <div class="metric-content">
                                            <span class="metric-label">Productos</span>
                                            <h3 class="metric-value">{{ $productosCount }}</h3>
                                            <small class="text-muted">vendidos</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Compras recibidas -->
                                <div class="col-6">
                                    <div class="metric-card">
                                        <div class="metric-icon bg-info">
                                            <i class="fas fa-shopping-cart"></i>
                                        </div>
                                        <div class="metric-content">
                                            <span class="metric-label">Compras</span>
                                            <h3 class="metric-value">{{ $comprasRecibidasCount }}</h3>
                                            <small class="text-muted">recibidas</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Balance del día -->
                                <div class="col-6">
                                    <div class="metric-card {{ $balanceDiario >= 0 ? 'balance-positive' : 'balance-negative' }}">
                                        <div class="metric-icon {{ $balanceDiario >= 0 ? 'bg-success' : 'bg-danger' }}">
                                            <i class="fas fa-balance-scale"></i>
                                        </div>
                                        <div class="metric-content">
                                            <span class="metric-label">Balance</span>
                                            <h3 class="metric-value {{ $balanceDiario >= 0 ? 'text-success' : 'text-danger' }}">
                                                S/ {{ number_format(abs($balanceDiario), 2) }}
                                            </h3>
                                            <small class="text-muted">{{ $balanceDiario >= 0 ? 'ganancia' : 'pérdida' }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Columna Derecha: Gráfico Comparativo -->
                        <div class="col-lg-7">
                            <div class="chart-container-financial">
                                <h5 class="text-center mb-3 fw-bold">Comparativo Ingresos vs Gastos</h5>
                                <div class="financial-summary mb-3">
                                    <div class="financial-item">
                                        <div class="d-flex align-items-center">
                                            <div class="financial-indicator bg-success"></div>
                                            <span class="fw-bold me-2">Ingresos:</span>
                                            <span class="text-success fs-5 fw-bold">S/ {{ number_format($ingresoDiario, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="financial-item">
                                        <div class="d-flex align-items-center">
                                            <div class="financial-indicator bg-danger"></div>
                                            <span class="fw-bold me-2">Gastos:</span>
                                            <span class="text-danger fs-5 fw-bold">S/ {{ number_format($gastosDiario, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <canvas id="ingresosGastosGrafico" style="max-height: 280px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Sección 2: Gráficos de Productos -->
    <div class="row g-4 mb-5">
        <div class="col-lg-6">
            <div class="card modern-card border-0 shadow-lg h-100">
                <div class="card-header-modern bg-warning-gradient">
                    <div class="d-flex align-items-center">
                        <div class="header-icon bg-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">Productos con Stock Mínimo</h5>
                            <small class="text-white-50">Requieren reabastecimiento</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <canvas id="stockMinimoGrafico"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card modern-card border-0 shadow-lg h-100">
                <div class="card-header-modern bg-success-gradient">
                    <div class="d-flex align-items-center">
                        <div class="header-icon bg-success">
                            <i class="fas fa-fire"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">Top 5 Productos Más Vendidos</h5>
                            <small class="text-white-50">Los favoritos de tus clientes</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <canvas id="productosMasVendidosGrafico"></canvas>
                </div>
            </div>
        </div>
    </div>
        
    <div class="row mb-5">
        <div class="col-lg-12">
            <div class="card modern-card border-0 shadow-lg">
                <div class="card-header-modern bg-primary-gradient">
                    <div class="d-flex align-items-center">
                        <div class="header-icon bg-primary">
                            <i class="fas fa-warehouse"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">Inventario Completo</h5>
                            <small class="text-white-50">Stock actual de todos los productos</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="chart-container-full">
                        <canvas id="stockProductosGrafico"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modales de Confirmación -->
<div class="modal fade" id="modalAbrirCaja" tabindex="-1" aria-labelledby="modalAbrirCajaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 bg-success-gradient text-white" style="border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold" id="modalAbrirCajaLabel">
                    <i class="fas fa-lock-open me-2"></i>Abrir Caja
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="modal-icon-large bg-success bg-opacity-10 mx-auto mb-3">
                        <i class="fas fa-lock-open text-success"></i>
                    </div>
                    <h6 class="fw-bold mb-2">¿Iniciar operaciones del día?</h6>
                    <p class="text-muted mb-0">Se registrará la apertura de caja para el día {{ now()->format('d/m/Y') }}</p>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-success px-4" id="confirmarAbrirCaja">
                    <i class="fas fa-check me-2"></i>Sí, Abrir Caja
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCerrarCaja" tabindex="-1" aria-labelledby="modalCerrarCajaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 bg-danger text-white" style="border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold" id="modalCerrarCajaLabel">
                    <i class="fas fa-lock me-2"></i>Cerrar Caja
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="modal-icon-large bg-danger bg-opacity-10 mx-auto mb-3">
                        <i class="fas fa-lock text-danger"></i>
                    </div>
                    <h6 class="fw-bold mb-2">¿Finalizar operaciones del día?</h6>
                    <p class="text-muted mb-0">Se generará el reporte de cierre para el día {{ now()->format('d/m/Y') }}</p>
                </div>
                
                <div class="row g-3 mb-3">
                    <!-- Resumen de Ventas -->
                    <div class="col-md-6">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body">
                                <h6 class="text-success fw-bold mb-3">
                                    <i class="fas fa-shopping-cart me-2"></i>Resumen de Ventas
                                </h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Clientes atendidos:</span>
                                    <strong>{{ $clientesCount }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Productos vendidos:</span>
                                    <strong>{{ $productosCount }}</strong>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">Ingresos totales:</span>
                                    <strong class="text-success fs-5">S/ {{ number_format($ingresoDiario, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Resumen de Compras -->
                    <div class="col-md-6">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body">
                                <h6 class="text-danger fw-bold mb-3">
                                    <i class="fas fa-box me-2"></i>Resumen de Compras
                                </h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Compras recibidas:</span>
                                    <strong>{{ $comprasRecibidasCount }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">&nbsp;</span>
                                    <span>&nbsp;</span>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">Gastos totales:</span>
                                    <strong class="text-danger fs-5">S/ {{ number_format($gastosDiario, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Balance Final -->
                <div class="card bg-{{ $balanceDiario >= 0 ? 'success' : 'danger' }} bg-opacity-10 border-{{ $balanceDiario >= 0 ? 'success' : 'danger' }} border-2">
                    <div class="card-body text-center py-3">
                        <h6 class="text-muted mb-2">Balance del Día</h6>
                        <h3 class="fw-bold mb-0 text-{{ $balanceDiario >= 0 ? 'success' : 'danger' }}">
                            S/ {{ number_format($balanceDiario, 2) }}
                        </h3>
                        <small class="text-muted">{{ $balanceDiario >= 0 ? 'Ganancia neta' : 'Pérdida neta' }}</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-danger px-4" id="confirmarCerrarCaja">
                    <i class="fas fa-check me-2"></i>Sí, Cerrar Caja
                </button>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Fondo blanco completo */
        .dashboard-background {
            background: #ffffff;
            min-height: 100vh;
            padding: 40px 0;
        }
        
        /* Sistema de Control de Caja */
        .caja-control-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 35px 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            border: 1px solid #f0f0f0;
            margin-bottom: 40px;
        }
        
        .caja-status {
            display: flex;
            align-items: center;
            padding: 20px 0;
            margin-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .status-indicator {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin-right: 20px;
            transition: all 0.3s ease;
        }
        
        .status-indicator.active {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            box-shadow: 0 8px 20px rgba(17, 153, 142, 0.3);
            animation: pulse 2s infinite;
        }
        
        .status-indicator.inactive {
            background: linear-gradient(135deg, #c33764 0%, #e94057 100%);
            color: white;
            box-shadow: 0 8px 20px rgba(233, 64, 87, 0.3);
        }
        
        .status-indicator.closed {
            background: linear-gradient(135deg, #c33764 0%, #e94057 100%);
            color: white;
            box-shadow: 0 8px 20px rgba(233, 64, 87, 0.3);
        }
        
        .status-indicator.pending {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            box-shadow: 0 8px 20px rgba(240, 147, 251, 0.3);
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .status-info h5 {
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .caja-actions {
            display: flex;
            gap: 20px;
            justify-content: center;
        }
        
        .caja-form {
            flex: 1;
        }
        
        .btn-caja {
            width: 100%;
            border: none;
            padding: 20px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-caja::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            transition: left 0.5s ease;
        }
        
        .btn-caja:hover::before {
            left: 100%;
        }
        
        .btn-abrir {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            box-shadow: 0 8px 20px rgba(17, 153, 142, 0.3);
        }
        
        .btn-abrir:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(17, 153, 142, 0.4);
        }
        
        .btn-cerrar {
            background: linear-gradient(135deg, #c33764 0%, #e94057 100%);
            color: white;
            box-shadow: 0 8px 20px rgba(233, 64, 87, 0.3);
        }
        
        .btn-cerrar:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(233, 64, 87, 0.4);
        }
        
        .btn-caja:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }
        
        .btn-icon {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }
        
        .btn-text {
            font-size: 16px;
            letter-spacing: 0.5px;
        }
        
        /* Tarjetas de métricas mejoradas */
        .metric-card {
            background: #fff;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 1px solid #f0f0f0;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        
        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        
        .metric-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        }
        
        .metric-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            font-size: 24px;
            color: white;
        }
        
        .metric-content {
            flex: 1;
        }
        
        .metric-label {
            font-size: 13px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .metric-value {
            font-size: 32px;
            font-weight: 700;
            color: #2c3e50;
            margin: 8px 0 5px 0;
            line-height: 1;
        }
        
        .balance-positive {
            background: linear-gradient(135deg, #f5fff8 0%, #e8f8f0 100%);
        }
        
        .balance-negative {
            background: linear-gradient(135deg, #fff5f5 0%, #ffe8e8 100%);
        }
        
        /* Contenedor del gráfico financiero */
        .chart-container-financial {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .financial-summary {
            display: flex;
            justify-content: space-around;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .financial-item {
            flex: 1;
            min-width: 200px;
            padding: 15px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .financial-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
        
        /* Animaciones */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .metric-card {
            animation: fadeInUp 0.6s ease-out;
        }
        
        .metric-card:nth-child(1) { animation-delay: 0.1s; }
        .metric-card:nth-child(2) { animation-delay: 0.2s; }
        .metric-card:nth-child(3) { animation-delay: 0.3s; }
        .metric-card:nth-child(4) { animation-delay: 0.4s; }
        
        /* Tarjetas modernas para gráficos */
        .modern-card {
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 35px rgba(0,0,0,0.15) !important;
        }
        
        .card-header-modern {
            padding: 25px;
            color: white;
            border: none;
        }
        
        .bg-warning-gradient {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .bg-success-gradient {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .bg-primary-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .header-icon {
            width: 55px;
            height: 55px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: white;
            margin-right: 15px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }
        
        .chart-container-full {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
        }
        
        /* Estilos para modales */
        .modal-icon-large {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
        }
        
        .bg-success-gradient {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    
    <!-- JavaScript para modales de caja -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var abrirCajaForm = document.querySelector('form[action="{{ route("caja.abrir") }}"]');
            var cerrarCajaForm = document.querySelector('form[action="{{ route("caja.cerrar") }}"]');
            
            var modalAbrirCaja = new bootstrap.Modal(document.getElementById('modalAbrirCaja'));
            var modalCerrarCaja = new bootstrap.Modal(document.getElementById('modalCerrarCaja'));
            
            var btnConfirmarAbrir = document.getElementById('confirmarAbrirCaja');
            var btnConfirmarCerrar = document.getElementById('confirmarCerrarCaja');
    
            if (abrirCajaForm) {
                abrirCajaForm.addEventListener('submit', function(event) {
                    event.preventDefault();
                    modalAbrirCaja.show();
                });
                
                if (btnConfirmarAbrir) {
                    btnConfirmarAbrir.addEventListener('click', function() {
                        modalAbrirCaja.hide();
                        setTimeout(function() {
                            abrirCajaForm.submit();
                        }, 300);
                    });
                }
            }
    
            if (cerrarCajaForm) {
                cerrarCajaForm.addEventListener('submit', function(event) {
                    event.preventDefault();
                    modalCerrarCaja.show();
                });
                
                if (btnConfirmarCerrar) {
                    btnConfirmarCerrar.addEventListener('click', function() {
                        modalCerrarCaja.hide();
                        setTimeout(function() {
                            cerrarCajaForm.submit();
                        }, 300);
                    });
                }
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Definir paleta de colores VIBRANTES
            const colorPalette = {
                primary: '#2C2C2C',
                gold: '#D4AF37',
                goldLight: '#E8C547',
                success: '#B29F8C',
                danger: '#C1666B',
                warning: '#F4A460',
                info: '#48A9A6',
                beige: '#E8D4C4'
            };

            // Gráfico 1: Productos con stock mínimo - REDISEÑADO
            const ctx1 = document.getElementById('stockMinimoGrafico').getContext('2d');
            const productos = @json($productosStockMinimo);
            const stockLabels = productos.map(p => p.descripcionP);
            const stockValues = productos.map(p => p.stockP);

            // Colores vibrantes modernos
            const stockColors = [
                '#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', 
                '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E2',
                '#F8B739', '#52B788'
            ];

            const stockMinimoGrafico = new Chart(ctx1, {
                type: 'doughnut',
                data: {
                    labels: stockLabels,
                    datasets: [{
                        label: 'Stock Mínimo',
                        data: stockValues,
                        backgroundColor: stockColors.slice(0, stockValues.length),
                        borderColor: '#ffffff',
                        borderWidth: 3,
                        hoverBorderWidth: 4,
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                font: { family: "'Inter', sans-serif", size: 12, weight: '600' },
                                color: '#2c3e50',
                                padding: 15,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.parsed + ' unidades';
                                }
                            }
                        },
                        datalabels: {
                            color: '#fff',
                            anchor: 'center',
                            align: 'center',
                            font: { size: 14, weight: 'bold' },
                            formatter: (value) => value
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });

            // Gráfico 2: Productos más vendidos - REDISEÑADO
            const ctx2 = document.getElementById('productosMasVendidosGrafico').getContext('2d');
            const productosMasVendidos = @json($productosMasVendidos);
            const vendidosLabels = productosMasVendidos.map(p => p.producto.descripcionP);
            const vendidosValues = productosMasVendidos.map(p => p.total_vendido);

            // Gradientes modernos
            const vendidosColors = [
                '#667eea', '#764ba2', '#f093fb', '#4facfe', '#43e97b'
            ];

            const productosMasVendidosGrafico = new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: vendidosLabels,
                    datasets: [{
                        label: 'Cantidad vendida',
                        data: vendidosValues,
                        backgroundColor: vendidosColors.map(c => c + 'dd'),
                        borderColor: vendidosColors,
                        borderWidth: 2,
                        borderRadius: 12,
                        barThickness: 40
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            callbacks: {
                                label: function(context) {
                                    return 'Vendidos: ' + context.parsed.x + ' unidades';
                                }
                            }
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'end',
                            color: '#2c3e50',
                            font: { size: 13, weight: 'bold' },
                            formatter: (value) => value + ' unidades',
                            offset: 8
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { 
                                font: { family: "'Inter', sans-serif", size: 11 },
                                color: '#6c757d'
                            },
                            grid: { color: 'rgba(0, 0, 0, 0.05)' }
                        },
                        y: {
                            ticks: { 
                                font: { family: "'Inter', sans-serif", size: 12, weight: '600' },
                                color: '#2c3e50'
                            },
                            grid: { display: false }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });

            // Gráfico 3: Stock actual de productos - REDISEÑADO
            const ctx3 = document.getElementById('stockProductosGrafico').getContext('2d');
            const productosStock = @json($productosStockActual);
            const stockProductosLabels = productosStock.map(p => p.descripcionP);
            const stockProductosValues = productosStock.map(p => p.stockP);

            // Crear gradiente vibrante
            const gradient = ctx3.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(102, 126, 234, 0.4)');
            gradient.addColorStop(0.5, 'rgba(118, 75, 162, 0.2)');
            gradient.addColorStop(1, 'rgba(102, 126, 234, 0.01)');

            const stockProductosGrafico = new Chart(ctx3, {
                type: 'line',
                data: {
                    labels: stockProductosLabels,
                    datasets: [{
                        label: 'Stock actual',
                        data: stockProductosValues,
                        borderColor: '#667eea',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 6,
                        pointBackgroundColor: '#764ba2',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 3,
                        pointHoverRadius: 10,
                        pointHoverBackgroundColor: '#f093fb',
                        pointHoverBorderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                font: { family: "'Inter', sans-serif", size: 13, weight: '600' },
                                color: '#2c3e50',
                                usePointStyle: true,
                                pointStyle: 'circle',
                                padding: 15
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            callbacks: {
                                label: function(context) {
                                    return 'Stock: ' + context.parsed.y + ' unidades';
                                }
                            }
                        },
                        datalabels: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { 
                                font: { family: "'Inter', sans-serif", size: 11 },
                                color: '#6c757d',
                                callback: function(value) {
                                    return value + ' un.';
                                }
                            },
                            grid: { 
                                color: 'rgba(0, 0, 0, 0.05)',
                                drawBorder: false
                            }
                        },
                        x: {
                            ticks: { 
                                font: { family: "'Inter', sans-serif", size: 11 },
                                color: '#6c757d',
                                maxRotation: 45,
                                minRotation: 0
                            },
                            grid: { display: false }
                        }
                    }
                }
            });

            // Grafico 4: Comparacion Ingresos vs Gastos (Mejorado)
            const ctx4 = document.getElementById('ingresosGastosGrafico');
            if (ctx4) {
                const ingresoDiario = {{ $ingresoDiario }};
                const gastosDiario = {{ $gastosDiario }};
                
                const ingresosGastosGrafico = new Chart(ctx4.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: ['Finanzas del Día'],
                        datasets: [
                            {
                                label: 'Ingresos',
                                data: [ingresoDiario],
                                backgroundColor: 'rgba(76, 175, 80, 0.85)',
                                borderColor: '#43a047',
                                borderWidth: 2,
                                borderRadius: 10,
                                barThickness: 60
                            },
                            {
                                label: 'Gastos',
                                data: [gastosDiario],
                                backgroundColor: 'rgba(244, 67, 54, 0.85)',
                                borderColor: '#e53935',
                                borderWidth: 2,
                                borderRadius: 10,
                                barThickness: 60
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: { size: 14, weight: 'bold' },
                                bodyFont: { size: 13 },
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': S/ ' + context.parsed.y.toFixed(2);
                                    }
                                }
                            },
                            datalabels: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    font: { family: "'Inter', sans-serif", size: 12 },
                                    color: '#6c757d',
                                    callback: function(value) {
                                        return 'S/ ' + value.toFixed(0);
                                    }
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)',
                                    drawBorder: false
                                }
                            },
                            x: {
                                ticks: {
                                    font: { family: "'Inter', sans-serif", size: 12, weight: '600' },
                                    color: '#2c3e50'
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    },
                    plugins: [ChartDataLabels]
                });
            }
        });
    </script>
@stop


@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/boutique-theme.css') }}">
@stop


@section('js')
    {{-- <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script> --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script src="{{ asset('js/animations.js') }}"></script>
@stop






{{-- @extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}
