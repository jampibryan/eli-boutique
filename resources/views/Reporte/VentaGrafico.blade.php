@extends('adminlte::page')

@section('title', 'Reporte de Ventas')

@section('content_header')

@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="text-start mb-3">Generar gráfico por:</h5>
            <div class="mb-3">
                <select id="tipoGrafica" class="form-select" aria-label="Seleccionar tipo de gráfica">
                    <option value="mes">Mes</option>
                    <option value="dia">Día</option>
                </select>
            </div>

            <!-- Formulario para seleccionar rango de meses -->
            <div id="formMes" class="mt-3">
                <div class="row">
                    <div class="col-md-5">
                        <label for="mesInicio">Mes de Inicio:</label>
                        <input type="month" id="mesInicio" class="form-control" />
                    </div>
                    <div class="col-md-5">
                        <label for="mesFinal">Mes Final:</label>
                        <input type="month" id="mesFinal" class="form-control" />
                    </div>
                    <div class="col-md-2 d-flex align-items-end justify-content-center">
                        <button id="mostrarGraficaMes" class="btn btn-primary">Mostrar Gráfica</button>
                    </div>
                </div>
            </div>

            <!-- Formulario para seleccionar rango de días -->
            <div id="formDia" class="mt-3" style="display: none;">
                <div class="row">
                    <div class="col-md-5">
                        <label for="diaInicio">Día de Inicio:</label>
                        <input type="date" id="diaInicio" class="form-control" />
                    </div>
                    <div class="col-md-5">
                        <label for="diaFinal">Día Final:</label>
                        <input type="date" id="diaFinal" class="form-control" />
                    </div>
                    <div class="col-md-2 d-flex align-items-end justify-content-center">
                        <button id="mostrarGraficaDia" class="btn btn-primary">Mostrar Gráfica</button>
                    </div>
                </div>
            </div>

            <canvas id="ventasGraficoMes" class="mt-5" style="display: none;"></canvas>
            <canvas id="ventasGraficoDia" class="mt-5" style="display: none;"></canvas>

            <!-- Botón para Generar PDF -->
            <div id="generarPdfDia" class="mt-3">
                <button class="btn btn-danger" id="btnGenerarPdfDia">Generar PDF</button>
            </div>

        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Asegúrate de incluir Chart.js -->

    <script>
        // -------------------------
        // BLOQUE 1: Gestión de LocalStorage
        // -------------------------

        // Guardar valores en LocalStorage

        function saveToLocalStorage() {
            const tipo = document.getElementById('tipoGrafica').value;
            localStorage.setItem('tipoGrafica', tipo);

            const isMes = tipo === 'mes';
            const inicio = document.getElementById(isMes ? 'mesInicio' : 'diaInicio').value;
            const final = document.getElementById(isMes ? 'mesFinal' : 'diaFinal').value;

            localStorage.setItem(isMes ? 'mesInicio' : 'diaInicio', inicio);
            localStorage.setItem(isMes ? 'mesFinal' : 'diaFinal', final);
        }

        // Cargar valores desde LocalStorage
        function loadFromLocalStorage() {
            const tipo = localStorage.getItem('tipoGrafica') || 'mes';
            document.getElementById('tipoGrafica').value = tipo;

            ['mesInicio', 'mesFinal', 'diaInicio', 'diaFinal'].forEach(key => {
                const value = localStorage.getItem(key);
                if (value) document.getElementById(key).value = value;
            });

            updateFormAndChart(tipo);
        }

        // Actualizar formularios y gráficos según el tipo seleccionado
        function updateFormAndChart(tipo) {
            const isMes = tipo === 'mes';

            document.getElementById('formMes').style.display = isMes ? 'block' : 'none';
            document.getElementById('formDia').style.display = isMes ? 'none' : 'block';
            document.getElementById('ventasGraficoMes').style.display = isMes ? 'block' : 'none';
            document.getElementById('ventasGraficoDia').style.display = isMes ? 'none' : 'block';
        }

        // -------------------------
        // BLOQUE 2: Gestión de Eventos
        // -------------------------

        document.getElementById('mostrarGraficaMes').addEventListener('click', function() {
            saveToLocalStorage();
            const mesInicio = document.getElementById('mesInicio').value;
            const mesFinal = document.getElementById('mesFinal').value;
            window.location.href = `/reportes/graficos/ventas?mesInicio=${mesInicio}&mesFinal=${mesFinal}`;
        });

        document.getElementById('mostrarGraficaDia').addEventListener('click', function() {
            saveToLocalStorage();
            const diaInicio = document.getElementById('diaInicio').value;
            const diaFinal = document.getElementById('diaFinal').value;
            window.location.href = `/reportes/graficos/ventas?diaInicio=${diaInicio}&diaFinal=${diaFinal}`;
        });

        document.getElementById('btnGenerarPdfDia').addEventListener('click', function() {
            console.log('Generar PDF');
            const diaInicio = document.getElementById('diaInicio').value;
            const diaFinal = document.getElementById('diaFinal').value;

            // Validar fechas
            if (!diaInicio || !diaFinal) {
                alert("Por favor selecciona un rango de fechas válido.");
                return;
            }

            // Capturar el gráfico en formato base64
            const canvas = document.getElementById('ventasGraficoDia'); // Asegúrate de que sea el ID correcto
            const chartImage = canvas.toDataURL('image/png'); // Convertir gráfico a base64

            // Preparar datos para el servidor
            const data = {
                diaInicio: diaInicio,
                diaFinal: diaFinal,
                chartImage: chartImage, // Incluir la imagen del gráfico
            };

            // Enviar datos al servidor sin esperar la respuesta
            sendImageToServer('/reportes/graficos/ventas/pdf', data);
        });



        // -------------------------
        // BLOQUE 3: Envío de Imagen al Servidor
        // -------------------------

        function sendImageToServer(url, data) {
            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify(data),
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al generar el PDF');
                    }
                    return response.blob();
                })
                .then(blob => {
                    const pdfUrl = window.URL.createObjectURL(blob);

                    // Abrir el PDF en una nueva pestaña
                    window.open(pdfUrl, '_blank');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("Hubo un problema al generar el PDF. Intenta nuevamente.");
                });
        }


        // -------------------------
        // BLOQUE 4: Generación de Gráficas
        // -------------------------

        function generateChart(canvasId, labels, values, title) {
            const ctx = document.getElementById(canvasId).getContext('2d');
            return new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                            label: 'Barras',
                            data: values,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1,
                            type: 'bar',
                        },
                        {
                            label: 'Líneas',
                            data: values,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 2,
                            fill: false,
                            type: 'line',
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
                                size: 16
                            },
                        },
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Rango'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Monto total (S/)'
                            }
                        },
                    },
                },
            });
        }

        // -------------------------
        // BLOQUE 5: Inicialización
        // -------------------------

        window.onload = function() {
            loadFromLocalStorage();

            const labelsMes = {!! json_encode($labelsMes) !!};
            const valuesMes = {!! json_encode($valuesMes) !!};
            const labelsDia = {!! json_encode($labelsDia) !!};
            const valuesDia = {!! json_encode($valuesDia) !!};

            if (labelsMes.length && valuesMes.length) {
                generateChart('ventasGraficoMes', labelsMes, valuesMes, 'Monto total de ventas por mes');
            } else {
                document.getElementById('ventasGraficoMes').style.display = 'none';
            }

            if (labelsDia.length && valuesDia.length) {
                generateChart('ventasGraficoDia', labelsDia, valuesDia, 'Monto total de ventas por día');
            } else {
                document.getElementById('ventasGraficoDia').style.display = 'none';
            }

            const tipoGrafica = localStorage.getItem('tipoGrafica') || 'mes';
            updateFormAndChart(tipoGrafica);
        };
    </script>

@stop
