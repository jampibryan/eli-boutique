@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
{{-- <h1 class="text-center">Reporte Diario</h1> --}}
@stop


@section('content')
<div class="container">
    <!-- Botones para abrir y cerrar caja -->
    <div class="row justify-content-center mt-4 mb-4">
        <div class="col-md-2">
            <form action="{{ route('caja.abrir') }}" method="POST" class="d-grid">
                @csrf
                <button 
                    type="submit" 
                    class="btn btn-success btn-lg"
                    {{ $cajaAbierta ? 'disabled' : '' }}>
                    <i class="fas fa-lock-open me-2"></i>Abrir caja
                </button>
            </form>
        </div>
        <div class="col-md-2">
            <form action="{{ route('caja.cerrar') }}" method="POST" class="d-grid">
                @csrf
                <input type="hidden" name="clientesHoy" value="{{ $clientesCount }}">
                <input type="hidden" name="productosVendidos" value="{{ $productosCount }}">
                <input type="hidden" name="ingresoDiario" value="{{ $ingresoDiario }}">
                <button 
                    type="submit" 
                    class="btn btn-danger btn-lg"
                    {{ !$cajaAbierta || session('cajaCerrada') ? 'disabled' : '' }}>
                    <i class="fas fa-lock me-2"></i>Cerrar caja
                </button>
            </form>
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


    <!-- Sección 1: Información General (KPIs) -->
    <div class="row mt-5 mb-5">
        <div class="col-xl-10 col-xxl-8 mx-auto">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="kpi-card shadow-md animate-fadeInUp">
                        <div class="kpi-icon-large">
                            <i class="fas fa-users"></i>
                        </div>
                        <h6 class="kpi-label">Clientes de hoy</h6>
                        <p class="kpi-value">{{ $clientesCount }}</p>
                        <small class="text-muted">Total de clientes únicos</small>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="kpi-card shadow-md success animate-fadeInUp">
                        <div class="kpi-icon-large text-success">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <h6 class="kpi-label">Productos vendidos</h6>
                        <p class="kpi-value">{{ $productosCount }}</p>
                        <small class="text-muted">Cantidad total vendida</small>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="kpi-card shadow-md warning animate-fadeInUp">
                        <div class="kpi-icon-large text-warning">
                            <i class="fas fa-coins"></i>
                        </div>
                        <h6 class="kpi-label">Ingresos del día</h6>
                        <p class="kpi-value">S/ {{ number_format($ingresoDiario, 2) }}</p>
                        <small class="text-muted">Ventas completadas</small>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Sección 2: Gráficos de Productos -->
    <div class="row g-4 mb-5">
        <div class="col-lg-6">
            <div class="card shadow-md card-primary-top h-100">
                <div class="card-header bg-gradient">
                    <h4 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Productos con stock mínimo
                    </h4>
                </div>
                <div class="card-body">
                    <canvas id="stockMinimoGrafico"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-md card-success-top h-100">
                <div class="card-header bg-gradient">
                    <h4 class="mb-0">
                        <i class="fas fa-star me-2"></i>
                        Top 5 Productos más vendidos
                    </h4>
                </div>
                <div class="card-body">
                    <canvas id="productosMasVendidosGrafico"></canvas>
                </div>
            </div>
        </div>
    </div>
        
    <div class="row mb-5">
        <div class="col-lg-12">
            <div class="card shadow-md card-info-top">
                <div class="card-header bg-gradient">
                    <h4 class="mb-0">
                        <i class="fas fa-warehouse me-2"></i>
                        Stock actual de productos
                    </h4>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="stockProductosGrafico"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    
    <!-- JavaScript para confirmar y deshabilitar botones -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const abrirCajaForm = document.querySelector('form[action="{{ route("caja.abrir") }}"]');
            const cerrarCajaForm = document.querySelector('form[action="{{ route("caja.cerrar") }}"]');
    
            if (abrirCajaForm) {
                abrirCajaForm.addEventListener('submit', function(event) {
                    event.preventDefault(); // Evitar el envío inmediato del formulario
                    if (confirm("¿Estás seguro de que quieres abrir la caja?")) {
                        abrirCajaForm.submit(); // Enviar el formulario si el usuario confirma
                    }
                });
            }
    
            if (cerrarCajaForm) {
                cerrarCajaForm.addEventListener('submit', function(event) {
                    event.preventDefault(); // Evitar el envío inmediato del formulario
                    if (confirm("¿Estás seguro de que quieres cerrar la caja?")) {
                        cerrarCajaForm.submit(); // Enviar el formulario si el usuario confirma
                    }
                });
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

            // Gráfico 1: Productos con stock mínimo - COLORES VIBRANTES
            const ctx1 = document.getElementById('stockMinimoGrafico').getContext('2d');
            const productos = @json($productosStockMinimo);
            const stockLabels = productos.map(p => p.descripcionP);
            const stockValues = productos.map(p => p.stockP);

            // Colores VIBRANTES para stock mínimo (rojo, naranja, amarillo, verde)
            const stockColors = [
                'rgba(255, 59, 48, 0.8)',     // Rojo vibrante
                'rgba(33, 150, 243, 0.8)',   // Azul vibrante
                'rgba(76, 175, 80, 0.8)',    // Verde vibrante
                'rgba(255, 87, 34, 0.8)',    // Naranja fuerte
                'rgba(255, 193, 7, 0.8)',    // Amarillo dorado
                'rgba(255, 152, 0, 0.8)',    // Naranja dorado
                'rgba(0, 188, 212, 0.8)',    // Celeste vibrante
                'rgba(244, 67, 54, 0.8)',    // Rojo oscuro
                'rgba(156, 39, 176, 0.8)',   // Púrpura vibrante
                'rgba(233, 30, 99, 0.8)'     // Rosa vibrante
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
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: { family: "'Inter', sans-serif", size: 11, weight: '600' },
                                color: colorPalette.primary,
                                padding: 15,
                                usePointStyle: true
                            }
                        },
                        datalabels: {
                            color: '#fff',
                            anchor: 'center',
                            align: 'center',
                            font: { size: 13, weight: 'bold' },
                            formatter: (value) => value
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });

            // Gráfico 2: Productos más vendidos - COLORES VIBRANTES
            const ctx2 = document.getElementById('productosMasVendidosGrafico').getContext('2d');
            const productosMasVendidos = @json($productosMasVendidos);
            const vendidosLabels = productosMasVendidos.map(p => p.producto.descripcionP);
            const vendidosValues = productosMasVendidos.map(p => p.total_vendido);

            const productosMasVendidosGrafico = new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: vendidosLabels,
                    datasets: [{
                        label: 'Cantidad vendida',
                        data: vendidosValues,
                        backgroundColor: [
                            'rgba(33, 150, 243, 0.9)',   // Azul vibrante
                            'rgba(76, 175, 80, 0.9)',    // Verde vibrante
                            'rgba(255, 152, 0, 0.9)',    // Naranja vibrante
                            'rgba(156, 39, 176, 0.8)',   // Púrpura
                            'rgba(0, 188, 212, 0.8)'     // Celeste
                        ],
                        borderColor: [
                            '#1976d2',
                            '#43a047',
                            '#f57c00',
                            '#7b1fa2',
                            '#00acc1'
                        ],
                        borderWidth: 2,
                        borderRadius: 8
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'end',
                            color: colorPalette.primary,
                            font: { size: 12, weight: 'bold' },
                            formatter: (value) => value
                        }
                    },
                    scales: {
                        x: {
                            ticks: { font: { family: "'Inter', sans-serif" } },
                            grid: { color: 'rgba(0, 0, 0, 0.05)' }
                        },
                        y: {
                            ticks: { font: { family: "'Inter', sans-serif", weight: '600' } },
                            grid: { display: false }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });

            // Gráfico 3: Stock actual de productos - COLORES VIBRANTES
            const ctx3 = document.getElementById('stockProductosGrafico').getContext('2d');
            const productosStock = @json($productosStockActual);
            const stockProductosLabels = productosStock.map(p => p.descripcionP);
            const stockProductosValues = productosStock.map(p => p.stockP);

            // Crear gradiente para el área con colores vibrantes
            const gradient = ctx3.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(33, 150, 243, 0.3)');   // Azul vibrante
            gradient.addColorStop(1, 'rgba(33, 150, 243, 0.01)');

            const stockProductosGrafico = new Chart(ctx3, {
                type: 'line',
                data: {
                    labels: stockProductosLabels,
                    datasets: [{
                        label: 'Stock actual',
                        data: stockProductosValues,
                        borderColor: '#2196f3',                    // Azul vibrante
                        backgroundColor: gradient,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: '#ff5722',           // Naranja vibrante
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 8,
                        pointHoverBackgroundColor: '#4caf50',      // Verde vibrante
                        pointHoverBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                font: { family: "'Inter', sans-serif", size: 12, weight: '600' },
                                color: colorPalette.primary,
                                usePointStyle: true,
                                padding: 15
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
                                font: { family: "'Inter', sans-serif" },
                                color: colorPalette.gray_medium
                            },
                            grid: { color: 'rgba(0, 0, 0, 0.05)' }
                        },
                        x: {
                            ticks: { 
                                font: { family: "'Inter', sans-serif", size: 10 },
                                color: colorPalette.gray_medium,
                                maxRotation: 45,
                                minRotation: 0
                            },
                            grid: { display: false }
                        }
                    }
                }
            });
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
