@extends('adminlte::page')

@section('title', 'Reporte Gráfico de Compras')

@section('content_header')
@stop

@section('content')
    <div class="container-fluid py-3">

        {{-- Título principal --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between flex-wrap"
                    style="background:#fff;border-radius:12px;padding:20px 28px;box-shadow:0 2px 12px rgba(0,0,0,0.06);border-left:5px solid #e67e22;">
                    <div class="d-flex align-items-center">
                        <div
                            style="width:50px;height:50px;border-radius:12px;background:linear-gradient(135deg,#e67e22,#e74c3c);display:flex;align-items:center;justify-content:center;margin-right:16px;">
                            <i class="fas fa-chart-bar" style="color:#fff;font-size:22px;"></i>
                        </div>
                        <div>
                            <h4 class="mb-0" style="color:#2c2c2c;font-weight:700;">Reporte Gráfico de Compras</h4>
                            <small class="text-muted">Analiza las compras realizadas por mes o por día</small>
                        </div>
                    </div>
                    <div class="text-end mt-2 mt-md-0">
                        <span class="badge"
                            style="background:#e67e22;color:#fff;font-size:12px;padding:6px 14px;border-radius:20px;">
                            <i class="fas fa-user me-1"></i> {{ Auth::user()->name ?? 'Sistema' }}
                        </span>
                        <br>
                        <small class="text-muted">{{ date('d/m/Y - h:i A') }}</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Panel de filtros --}}
        <div class="row mb-4">
            <div class="col-lg-10 mx-auto">
                <div class="card border-0 shadow-sm" style="border-radius:12px;overflow:hidden;">
                    <div class="card-header py-3 d-flex align-items-center justify-content-between"
                        style="background:linear-gradient(135deg,#e67e22,#e74c3c);border:none;">
                        <h6 class="mb-0 text-white fw-bold"><i class="fas fa-filter me-2"></i>Filtros de búsqueda</h6>
                        <button type="button" id="btnToggleRango" class="btn btn-sm px-3 py-1"
                            style="background:rgba(255,255,255,0.2);color:#fff;border:1px solid rgba(255,255,255,0.4);border-radius:20px;font-size:12px;transition:all 0.3s;">
                            <i class="fas fa-calendar-plus me-1"></i>
                            <span id="txtToggle">Agregar mes final</span>
                        </button>
                    </div>
                    <div class="card-body px-4 py-4" style="background:#fffaf5;">
                        {{-- Fila de inputs --}}
                        <div class="row g-3 align-items-end mb-3">
                            {{-- Tipo de gráfico --}}
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-muted mb-1" style="font-size:13px;">
                                    <i class="fas fa-chart-bar me-1" style="color:#e67e22;"></i> Tipo de gráfico
                                </label>
                                <select id="tipoGrafica" class="form-select"
                                    style="border-radius:8px;border:1px solid #d0d5dd;font-size:14px;height:42px;">
                                    <option value="mes">📅 Por Mes</option>
                                    <option value="dia">📆 Por Día</option>
                                </select>
                            </div>

                            {{-- Filtros por MES --}}
                            <div class="col-md-3 filtro-grupo" id="filtrosMes">
                                <label class="form-label fw-bold text-muted mb-1" style="font-size:13px;">
                                    <i class="fas fa-calendar me-1" style="color:#e67e22;"></i> Mes inicio
                                </label>
                                <input type="month" id="mesInicio" class="form-control"
                                    style="border-radius:8px;border:1px solid #d0d5dd;font-size:14px;height:42px;" />
                            </div>
                            <div class="col-md-3 filtro-grupo" id="filtrosMesFinal" style="display:none;">
                                <label class="form-label fw-bold text-muted mb-1" style="font-size:13px;">
                                    <i class="fas fa-calendar-check me-1" style="color:#e74c3c;"></i> Mes final
                                </label>
                                <input type="month" id="mesFinal" class="form-control"
                                    style="border-radius:8px;border:1px solid #d0d5dd;font-size:14px;height:42px;" />
                            </div>

                            {{-- Filtros por DÍA --}}
                            <div class="col-md-3 filtro-grupo" id="filtrosDiaInicio" style="display:none;">
                                <label class="form-label fw-bold text-muted mb-1" style="font-size:13px;">
                                    <i class="fas fa-calendar me-1" style="color:#e67e22;"></i> Desde
                                </label>
                                <input type="date" id="diaInicio" class="form-control"
                                    style="border-radius:8px;border:1px solid #d0d5dd;font-size:14px;height:42px;" />
                            </div>
                            <div class="col-md-3 filtro-grupo" id="filtrosDiaFinal" style="display:none;">
                                <label class="form-label fw-bold text-muted mb-1" style="font-size:13px;">
                                    <i class="fas fa-calendar-check me-1" style="color:#e74c3c;"></i> Hasta
                                </label>
                                <input type="date" id="diaFinal" class="form-control"
                                    style="border-radius:8px;border:1px solid #d0d5dd;font-size:14px;height:42px;" />
                            </div>
                        </div>

                        {{-- Separador y botón de acción --}}
                        <hr style="border-color:#f0e0d0;margin:16px 0;">
                        <div class="text-center">
                            <button id="btnBuscar" class="btn text-white fw-bold px-5"
                                style="background:linear-gradient(135deg,#e67e22,#e74c3c);border:none;border-radius:10px;padding:12px 40px;font-size:15px;box-shadow:0 4px 15px rgba(230,126,34,0.35);transition:all 0.3s;">
                                <i class="fas fa-search me-2"></i> Mostrar Gráfica
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tarjetas de resumen --}}
        @php
            $totalCompras = count($valuesMes) ? array_sum($valuesMes) : array_sum($valuesDia);
            $cantRegistros = count($labelsMes) ? count($labelsMes) : count($labelsDia);
            $promedio = $cantRegistros > 0 ? $totalCompras / $cantRegistros : 0;
            $maxCompra = count($valuesMes)
                ? (count($valuesMes)
                    ? max($valuesMes)
                    : 0)
                : (count($valuesDia)
                    ? max($valuesDia)
                    : 0);
        @endphp
        <div class="row mb-4">
            <div class="col-lg-10 mx-auto">
                <div class="row g-3">
                    <div class="col-md-3 col-6">
                        <div class="card border-0 shadow-sm text-center py-3" style="border-radius:12px;">
                            <div style="font-size:28px;margin-bottom:4px;">
                                <i class="fas fa-shopping-cart" style="color:#e67e22;"></i>
                            </div>
                            <div style="font-size:11px;color:#888;text-transform:uppercase;letter-spacing:1px;">Total
                                Compras</div>
                            <div style="font-size:22px;font-weight:700;color:#2c2c2c;">S/
                                {{ number_format($totalCompras, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card border-0 shadow-sm text-center py-3" style="border-radius:12px;">
                            <div style="font-size:28px;margin-bottom:4px;">
                                <i class="fas fa-list-ol" style="color:#e74c3c;"></i>
                            </div>
                            <div style="font-size:11px;color:#888;text-transform:uppercase;letter-spacing:1px;">Registros
                            </div>
                            <div style="font-size:22px;font-weight:700;color:#2c2c2c;">{{ $cantRegistros }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card border-0 shadow-sm text-center py-3" style="border-radius:12px;">
                            <div style="font-size:28px;margin-bottom:4px;">
                                <i class="fas fa-balance-scale" style="color:#28a745;"></i>
                            </div>
                            <div style="font-size:11px;color:#888;text-transform:uppercase;letter-spacing:1px;">Promedio
                            </div>
                            <div style="font-size:22px;font-weight:700;color:#2c2c2c;">S/
                                {{ number_format($promedio, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card border-0 shadow-sm text-center py-3" style="border-radius:12px;">
                            <div style="font-size:28px;margin-bottom:4px;">
                                <i class="fas fa-arrow-up" style="color:#9b59b6;"></i>
                            </div>
                            <div style="font-size:11px;color:#888;text-transform:uppercase;letter-spacing:1px;">Mayor
                                Compra</div>
                            <div style="font-size:22px;font-weight:700;color:#2c2c2c;">S/
                                {{ number_format($maxCompra, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Área del gráfico --}}
        <div class="row mb-4">
            <div class="col-lg-10 mx-auto">
                <div class="card border-0 shadow-sm" style="border-radius:12px;">
                    <div class="card-body p-4">
                        <div id="sinDatos" class="text-center py-5" style="display:none;">
                            <i class="fas fa-chart-area" style="font-size:60px;color:#dee2e6;"></i>
                            <p class="mt-3 text-muted">Selecciona un rango de fechas y presiona <strong>Mostrar
                                    Gráfica</strong></p>
                        </div>
                        <canvas id="comprasGraficoMes" style="display:none;"></canvas>
                        <canvas id="comprasGraficoDia" style="display:none;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Botón PDF --}}
        <div class="row mb-4">
            <div class="col-lg-10 mx-auto text-center">
                <button class="btn btn-lg text-white px-5" id="btnGenerarPdf"
                    style="display:none;background:linear-gradient(135deg,#e74c3c,#c0392b);border:none;border-radius:10px;font-size:15px;box-shadow:0 4px 12px rgba(231,76,60,0.3);">
                    <i class="fas fa-file-pdf me-2"></i> Descargar Reporte en PDF
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // ============================
        // Variables de estado
        // ============================
        let rangoActivo = false;

        // ============================
        // Leer parámetros de URL
        // ============================
        function getUrlParams() {
            const params = new URLSearchParams(window.location.search);
            return {
                mesInicio: params.get('mesInicio') || '',
                mesFinal: params.get('mesFinal') || '',
                diaInicio: params.get('diaInicio') || '',
                diaFinal: params.get('diaFinal') || '',
            };
        }

        // ============================
        // Gestión de LocalStorage
        // ============================
        function saveToLocalStorage() {
            const tipo = document.getElementById('tipoGrafica').value;
            localStorage.setItem('comprasGraf_tipo', tipo);
            localStorage.setItem('comprasGraf_rangoActivo', rangoActivo ? '1' : '0');
            if (tipo === 'mes') {
                localStorage.setItem('comprasGraf_mesInicio', document.getElementById('mesInicio').value);
                localStorage.setItem('comprasGraf_mesFinal', document.getElementById('mesFinal').value);
            } else {
                localStorage.setItem('comprasGraf_diaInicio', document.getElementById('diaInicio').value);
                localStorage.setItem('comprasGraf_diaFinal', document.getElementById('diaFinal').value);
            }
        }

        function loadFilters() {
            const urlParams = getUrlParams();
            const tieneUrlMes = urlParams.mesInicio !== '';
            const tieneUrlDia = urlParams.diaInicio !== '';

            let tipo;
            if (tieneUrlDia) {
                tipo = 'dia';
            } else if (tieneUrlMes) {
                tipo = 'mes';
            } else {
                tipo = localStorage.getItem('comprasGraf_tipo') || 'mes';
            }
            document.getElementById('tipoGrafica').value = tipo;

            if (tipo === 'mes') {
                const mesIni = urlParams.mesInicio || localStorage.getItem('comprasGraf_mesInicio') || '';
                const mesFin = urlParams.mesFinal || localStorage.getItem('comprasGraf_mesFinal') || '';
                document.getElementById('mesInicio').value = mesIni;
                document.getElementById('mesFinal').value = mesFin;

                if (mesFin && mesFin !== mesIni) {
                    rangoActivo = true;
                } else {
                    rangoActivo = localStorage.getItem('comprasGraf_rangoActivo') === '1';
                }
            } else {
                const diaIni = urlParams.diaInicio || localStorage.getItem('comprasGraf_diaInicio') || '';
                const diaFin = urlParams.diaFinal || localStorage.getItem('comprasGraf_diaFinal') || '';
                document.getElementById('diaInicio').value = diaIni;
                document.getElementById('diaFinal').value = diaFin;
            }

            document.getElementById('txtToggle').textContent = rangoActivo ? 'Quitar mes final' : 'Agregar mes final';
            updateUI(tipo);
        }

        // ============================
        // Actualizar UI según tipo
        // ============================
        function updateUI(tipo) {
            const isMes = tipo === 'mes';
            document.getElementById('filtrosMes').style.display = isMes ? '' : 'none';
            document.getElementById('filtrosMesFinal').style.display = (isMes && rangoActivo) ? '' : 'none';
            document.getElementById('filtrosDiaInicio').style.display = isMes ? 'none' : '';
            document.getElementById('filtrosDiaFinal').style.display = isMes ? 'none' : '';
            document.getElementById('btnToggleRango').style.display = isMes ? '' : 'none';
            document.getElementById('txtToggle').textContent = rangoActivo ? 'Quitar mes final' : 'Agregar mes final';
        }

        // ============================
        // Eventos
        // ============================
        document.getElementById('tipoGrafica').addEventListener('change', function() {
            updateUI(this.value);
        });

        document.getElementById('btnToggleRango').addEventListener('click', function() {
            rangoActivo = !rangoActivo;
            document.getElementById('txtToggle').textContent = rangoActivo ? 'Quitar mes final' :
                'Agregar mes final';
            const tipo = document.getElementById('tipoGrafica').value;
            if (tipo === 'mes') {
                document.getElementById('filtrosMesFinal').style.display = rangoActivo ? '' : 'none';
                if (!rangoActivo) document.getElementById('mesFinal').value = '';
            }
        });

        document.getElementById('btnBuscar').addEventListener('click', function() {
            saveToLocalStorage();
            const tipo = document.getElementById('tipoGrafica').value;
            if (tipo === 'mes') {
                const mesInicio = document.getElementById('mesInicio').value;
                if (!mesInicio) {
                    alert('Selecciona un mes de inicio.');
                    return;
                }
                let mesFinal = document.getElementById('mesFinal').value;
                if (!mesFinal) mesFinal = mesInicio;
                window.location.href =
                    `/reportes/graficos/compras?mesInicio=${mesInicio}&mesFinal=${mesFinal}`;
            } else {
                const diaInicio = document.getElementById('diaInicio').value;
                const diaFinal = document.getElementById('diaFinal').value;
                if (!diaInicio || !diaFinal) {
                    alert('Selecciona un rango de días válido.');
                    return;
                }
                window.location.href =
                    `/reportes/graficos/compras?diaInicio=${diaInicio}&diaFinal=${diaFinal}`;
            }
        });

        // Botón PDF
        document.getElementById('btnGenerarPdf').addEventListener('click', function() {
            const tipo = document.getElementById('tipoGrafica').value;
            const canvasId = tipo === 'mes' ? 'comprasGraficoMes' : 'comprasGraficoDia';
            const canvas = document.getElementById(canvasId);
            const chartImage = canvas.toDataURL('image/png');

            let diaInicio, diaFinal;
            if (tipo === 'mes') {
                diaInicio = document.getElementById('mesInicio').value + '-01';
                let mf = document.getElementById('mesFinal').value || document.getElementById('mesInicio')
                    .value;
                diaFinal = mf + '-31';
            } else {
                diaInicio = document.getElementById('diaInicio').value;
                diaFinal = document.getElementById('diaFinal').value;
            }

            sendImageToServer('/reportes/graficos/compras/pdf', {
                diaInicio,
                diaFinal,
                chartImage,
                tipo
            });
        });

        // ============================
        // Enviar imagen al servidor
        // ============================
        function sendImageToServer(url, data) {
            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                    },
                    body: JSON.stringify(data),
                })
                .then(response => {
                    if (!response.ok) throw new Error('Error al generar el PDF');
                    return response.blob();
                })
                .then(blob => {
                    window.open(window.URL.createObjectURL(blob), '_blank');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("Hubo un problema al generar el PDF. Intenta nuevamente.");
                });
        }

        // ============================
        // Generación de gráficos
        // ============================
        function generateChart(canvasId, labels, values, title) {
            const ctx = document.getElementById(canvasId).getContext('2d');
            return new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                            label: 'Monto (S/)',
                            data: values,
                            backgroundColor: 'rgba(230, 126, 34, 0.3)',
                            borderColor: '#e67e22',
                            borderWidth: 2,
                            borderRadius: 6,
                            type: 'bar',
                        },
                        {
                            label: 'Tendencia',
                            data: values,
                            borderColor: '#e74c3c',
                            borderWidth: 3,
                            pointBackgroundColor: '#e74c3c',
                            pointRadius: 5,
                            fill: false,
                            type: 'line',
                            tension: 0.3,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: title,
                            font: {
                                size: 18,
                                weight: 'bold'
                            },
                            color: '#2c2c2c'
                        },
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Periodo',
                                font: {
                                    size: 13
                                }
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Monto Total (S/)',
                                font: {
                                    size: 13
                                }
                            },
                            ticks: {
                                callback: value => 'S/ ' + value.toLocaleString()
                            }
                        },
                    },
                },
            });
        }

        // ============================
        // Inicialización
        // ============================
        window.onload = function() {
            loadFilters();

            const labelsMes = {!! json_encode($labelsMes) !!};
            const valuesMes = {!! json_encode($valuesMes) !!};
            const labelsDia = {!! json_encode($labelsDia) !!};
            const valuesDia = {!! json_encode($valuesDia) !!};

            const tipoGrafica = localStorage.getItem('comprasGraf_tipo') || 'mes';
            let hayDatos = false;

            if (tipoGrafica === 'mes' && labelsMes.length && valuesMes.length) {
                document.getElementById('comprasGraficoMes').style.display = 'block';
                document.getElementById('comprasGraficoDia').style.display = 'none';
                generateChart('comprasGraficoMes', labelsMes, valuesMes, 'Monto total de compras por mes');
                hayDatos = true;
            } else if (tipoGrafica === 'dia' && labelsDia.length && valuesDia.length) {
                document.getElementById('comprasGraficoDia').style.display = 'block';
                document.getElementById('comprasGraficoMes').style.display = 'none';
                generateChart('comprasGraficoDia', labelsDia, valuesDia, 'Monto total de compras por día');
                hayDatos = true;
            }

            document.getElementById('sinDatos').style.display = hayDatos ? 'none' : 'block';
            document.getElementById('btnGenerarPdf').style.display = hayDatos ? 'inline-block' : 'none';
        };
    </script>
@stop
