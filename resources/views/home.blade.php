@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
{{-- <h1 class="text-center">Reporte Diario</h1> --}}
@stop


@section('content')
<div class="container">
    <!-- Botones para abrir y cerrar caja -->
    <div class="row justify-content-center mt-4">
        <div class="col-md-2">
            <form action="{{ route('caja.abrir') }}" method="POST">
                @csrf
                <button 
                    type="submit" 
                    class="btn btn-success btn-block"
                    {{ $cajaAbierta ? 'disabled' : '' }}>
                    Abrir caja
                </button>
            </form>
        </div>
        <div class="col-md-2">
            <form action="{{ route('caja.cerrar') }}" method="POST">
                @csrf
                <input type="hidden" name="clientesHoy" value="{{ $clientesCount }}">
                <input type="hidden" name="productosVendidos" value="{{ $productosCount }}">
                <input type="hidden" name="ingresoDiario" value="{{ $ingresoDiario }}">
                <button 
                    type="submit" 
                    class="btn btn-danger btn-block"
                    {{ !$cajaAbierta || session('cajaCerrada') ? 'disabled' : '' }}>
                    Cerrar caja
                </button>
            </form>
        </div>
    </div>

    <!-- Mensajes de éxito y error -->
    @if(session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger mt-3">
            {{ session('error') }}
        </div>
    @endif


    <!-- Sección 1: Información General -->
    <div class="row mt-4 mb-4 justify-content-center">
        <div class="col-md-3">
            <div class="card border-primary shadow-sm text-center">
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                    <h5 class="card-title mb-1">Clientes de hoy</h5>
                    <p class="card-text h4">{{ $clientesCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success shadow-sm text-center">
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <i class="fas fa-box-open fa-2x text-success mb-2"></i>
                    <h5 class="card-title mb-1">Productos vendidos</h5>
                    <p class="card-text h4">{{ $productosCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning shadow-sm text-center">
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <i class="fas fa-coins fa-2x text-warning mb-2"></i>
                    <h5 class="card-title mb-1">Ingresos del día</h5>
                    <p class="card-text h4">S/ {{ number_format($ingresoDiario, 2) }}</p>
                </div>
            </div>
        </div>
    </div>


    <!-- Sección 2: Gráfico de productos -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="text-center">Productos con stock mínimo</h4>
                    <canvas id="stockMinimoGrafico"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="text-center">Top 5 Productos más vendidos</h4>
                    <canvas id="productosMasVendidosGrafico"></canvas>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="text-center">Stock actual de productos</h4>
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
            // Gráfico de productos con stock mínimo
            const ctx1 = document.getElementById('stockMinimoGrafico').getContext('2d');
            const productos = @json($productosStockMinimo);
            const stockLabels = productos.map(p => p.descripcionP);
            const stockValues = productos.map(p => p.stockP);

            // Colores predefinidos para los productos
            const colores = [
                'rgba(124, 252, 0, 0.3)',
                'rgba(255, 99, 132, 0.3)',
                'rgba(54, 162, 235, 0.3)',
                'rgba(255, 206, 86, 0.3)',
                'rgba(75, 192, 192, 0.3)',
                'rgba(153, 102, 255, 0.3)',
                'rgba(255, 159, 64, 0.3)',
                'rgba(255, 20, 147, 0.3)',
                'rgba(0, 191, 255, 0.3)',
                'rgba(238, 130, 238, 0.3)'
            ];

            // Asignar colores cíclicamente
            const stockColors = productos.map((_, index) => colores[index % colores.length]);

            const stockMinimoGrafico = new Chart(ctx1, {
                type: 'pie',
                data: {
                    labels: stockLabels,
                    datasets: [{
                        label: 'Stock Mínimo',
                        data: stockValues,
                        backgroundColor: stockColors,
                        borderColor: stockColors.map(color => color.replace(/0\.3/, '1')), // Borde con opacidad total
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        title: {
                            display: true,
                            // text: 'Productos con Stock Mínimo'
                        },
                        datalabels: {
                            color: '#000',
                            anchor: 'center',
                            align: 'center',
                            font: {
                                size: 16
                            },
                            formatter: (value, context) => {
                                return value;
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });



            // Gráfico de productos más vendidos
            const ctx2 = document.getElementById('productosMasVendidosGrafico').getContext('2d');
            const productosMasVendidos = @json($productosMasVendidos);
            const vendidosLabels = productosMasVendidos.map(p => p.producto.descripcionP);
            const vendidosValues = productosMasVendidos.map(p => p.total_vendido);
            
            const productosMasVendidosGrafico = new Chart(ctx2, {
                type: 'polarArea',
                data: {
                    labels: vendidosLabels,
                    datasets: [{
                        label: 'Productos Más Vendidos',
                        data: vendidosValues,
                        backgroundColor: [
                        'rgba(178, 34, 34, 0.3)',
                        'rgba(75, 0, 130, 0.3)',
                        'rgba(0, 123, 255, 0.3)',
                        'rgba(40, 167, 69, 0.3)',
                        'rgba(255, 193, 7, 0.3)',
                    ],
                    borderColor: [
                        'rgba(178, 34, 34, 1)',
                        'rgba(75, 0, 130, 1)',
                        'rgba(0, 123, 255, 1)',
                        'rgba(40, 167, 69, 1)',
                        'rgba(255, 193, 7, 1)',
                    ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        title: {
                            display: true
                            // text: 'Top 5 Productos Más Vendidos'
                        }
                    }
                }
            });


            // Gráfico de barras para el stock de cada producto
            const ctx3 = document.getElementById('stockProductosGrafico').getContext('2d');
            const productosStock = @json($productosStockActual); // Datos de los productos con su stock actual
            const stockProductosLabels = productosStock.map(p => p.descripcionP); // Nombres de los productos
            const stockProductosValues = productosStock.map(p => p.stockP); // Valores de stock

            const stockProductosGrafico = new Chart(ctx3, {
                type: 'bar',
                data: {
                    labels: stockProductosLabels,
                    datasets: [{
                        label: 'Stock actual de productos',
                        data: stockProductosValues,
                        backgroundColor: 'rgba(54, 162, 235, 0.4)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Cantidad de stock'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Productos'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
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
@stop


@section('js')
    {{-- <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script> --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
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
