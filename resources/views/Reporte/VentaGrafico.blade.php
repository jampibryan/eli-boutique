

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
            <div id="generarPdfDia" style="display: none;" class="mt-3">
                <button class="btn btn-danger" id="btnGenerarPdfDia">Generar PDF</button>
            </div>
            
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Asegúrate de incluir Chart.js -->

    {{-- Bloque 1: Scripts generales --}}

    <script>
        // Guardar valores en localStorage
        function saveToLocalStorage() {
            const tipo = document.getElementById('tipoGrafica').value;
            localStorage.setItem('tipoGrafica', tipo);
            if (tipo === 'mes') {
                const mesInicio = document.getElementById('mesInicio').value;
                const mesFinal = document.getElementById('mesFinal').value;
                localStorage.setItem('mesInicio', mesInicio);
                localStorage.setItem('mesFinal', mesFinal);
            } else {
                const diaInicio = document.getElementById('diaInicio').value;
                const diaFinal = document.getElementById('diaFinal').value;
                localStorage.setItem('diaInicio', diaInicio);
                localStorage.setItem('diaFinal', diaFinal);
            }
        }
    
        // Cargar valores desde localStorage y mostrar el gráfico adecuado
        function loadFromLocalStorage() {
            const tipo = localStorage.getItem('tipoGrafica') || 'mes';
            document.getElementById('tipoGrafica').value = tipo;
    
            const mesInicio = localStorage.getItem('mesInicio');
            const mesFinal = localStorage.getItem('mesFinal');
            if (mesInicio) document.getElementById('mesInicio').value = mesInicio;
            if (mesFinal) document.getElementById('mesFinal').value = mesFinal;
    
            const diaInicio = localStorage.getItem('diaInicio');
            const diaFinal = localStorage.getItem('diaFinal');
            if (diaInicio) document.getElementById('diaInicio').value = diaInicio;
            if (diaFinal) document.getElementById('diaFinal').value = diaFinal;
    
            updateFormAndChart(tipo);
        }
    
        // Actualizar formularios y gráficos según el tipo seleccionado
        function updateFormAndChart(tipo) {
            if (tipo === 'mes') {
                document.getElementById('formMes').style.display = 'block';
                document.getElementById('formDia').style.display = 'none';
                document.getElementById('ventasGraficoMes').style.display = 'block';
                document.getElementById('ventasGraficoDia').style.display = 'none';
            } else {
                document.getElementById('formMes').style.display = 'none';
                document.getElementById('formDia').style.display = 'block';
                document.getElementById('ventasGraficoMes').style.display = 'none';
                document.getElementById('ventasGraficoDia').style.display = 'block';
            }
        }
    </script>

    {{-- Bloque 2: Eventos de botones --}}
    <script>
        // Evento para el botón "Mostrar gráfica de mes"
        document.getElementById('mostrarGraficaMes').addEventListener('click', function () {
            saveToLocalStorage();
            const mesInicio = document.getElementById('mesInicio').value;
            const mesFinal = document.getElementById('mesFinal').value;
            window.location.href = `/reportes/graficos/ventas?mesInicio=${mesInicio}&mesFinal=${mesFinal}`;
        });
    
        // Evento para el botón "Mostrar gráfica de día"
        document.getElementById('mostrarGraficaDia').addEventListener('click', function () {
            saveToLocalStorage();
            const diaInicio = document.getElementById('diaInicio').value;
            const diaFinal = document.getElementById('diaFinal').value;
            window.location.href = `/reportes/graficos/ventas?diaInicio=${diaInicio}&diaFinal=${diaFinal}`;
        });
    
        // Evento para el botón "Generar PDF de día"
        document.getElementById('btnGenerarPdfDia').addEventListener('click', function () {
            const diaInicio = document.getElementById('diaInicio').value;
            const diaFinal = document.getElementById('diaFinal').value;
    
            // Validar que los campos de fecha no estén vacíos
            if (!diaInicio || !diaFinal) {
                alert("Por favor selecciona un rango de fechas válido.");
                return;
            }
    
            // Redirigir a la URL para generar el PDF
            window.open(`/reportes/graficos/ventas/pdf?diaInicio=${diaInicio}&diaFinal=${diaFinal}`);
        });
    </script>
    

    {{-- Bloque 3: Generación avanzada de PDF --}}
    <script>
        // Evento para generar PDF con el gráfico capturado
        document.getElementById('btnGenerarPdfDia').addEventListener('click', function () {
            const diaInicio = document.getElementById('diaInicio').value;
            const diaFinal = document.getElementById('diaFinal').value;

            // Validar si las fechas están llenas
            if (!diaInicio || !diaFinal) {
                alert("Por favor selecciona un rango de fechas válido.");
                return;
            }

            // Capturar el gráfico como una imagen en formato base64
            const canvas = document.getElementById('ventasGraficoDia');
            const chartImage = canvas.toDataURL('image/png'); // Convertir el gráfico en base64

            // Enviar la solicitud POST al servidor con las fechas y el gráfico
            fetch(`/reportes/graficos/ventas/pdf`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({
                    diaInicio: diaInicio,
                    diaFinal: diaFinal,
                    chartImage: chartImage, // Incluir el gráfico como imagen base64
                }),
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Error al generar el PDF');
                    }
                    return response.blob();
                })
                .then((blob) => {
                    // Descargar el PDF generado
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'Reporte_Ventas.pdf';
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                })
                .catch((error) => {
                    // Manejo silencioso del error
                    console.error('Error:', error); // Solo registro en la consola
                });
        });

    </script>


    {{-- Bloque 4: Generación de gráficos --}}
    <script>
        // Función para generar gráfico de meses
        function graficarDatosMes(labels, values) {
            const ctx = document.getElementById('ventasGraficoMes').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Barras',
                            data: values,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1,
                            type: 'bar'
                        },
                        {
                            label: 'Líneas',
                            data: values,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 2,
                            fill: false,
                            type: 'line'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Monto total de ventas por mes',
                            font: {
                                size: 16
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Rango de meses'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Monto total (S/)'  
                            }
                        }
                    }
                }
            });
        }
    
        // Función para generar gráfico de días
        function graficarDatosDia(labels, values) {
            const ctx = document.getElementById('ventasGraficoDia').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Barras',
                            data: values,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1,
                            type: 'bar'
                        },
                        {
                            label: 'Líneas',
                            data: values,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 2,
                            fill: false,
                            type: 'line'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Monto total de ventas por día',
                            font: {
                                size: 16
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Rango de días'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Monto total (S/)'  
                            }
                        }
                    }
                }
            });
            // Mostrar el botón "Generar PDF"
            document.getElementById('generarPdfDia').style.display = 'block';
        }
    </script>
    
    {{-- Bloque 5: Inicialización --}}
    <script>
        // Al cargar la página, inicializa los gráficos y carga los valores
        window.onload = function () {
            loadFromLocalStorage();
            const labelsMes = {!! json_encode($labelsMes) !!};
            const valuesMes = {!! json_encode($valuesMes) !!};
            const labelsDia = {!! json_encode($labelsDia) !!};
            const valuesDia = {!! json_encode($valuesDia) !!};
    
            // Renderizar gráficos solo si hay datos
            if (labelsMes.length > 0 && valuesMes.length > 0) {
                graficarDatosMes(labelsMes, valuesMes);
            } else {
                document.getElementById('ventasGraficoMes').style.display = 'none';
            }
    
            if (labelsDia.length > 0 && valuesDia.length > 0) {
                graficarDatosDia(labelsDia, valuesDia);
            } else {
                document.getElementById('ventasGraficoDia').style.display = 'none';
            }
    
            // Asegurarse de mostrar el gráfico correspondiente según el tipo
            const tipoGrafica = localStorage.getItem('tipoGrafica') || 'mes';
            updateFormAndChart(tipoGrafica);
        };
    </script>
    

@stop
