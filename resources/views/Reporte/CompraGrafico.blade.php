@extends('adminlte::page')

@section('title', 'Reporte de Compras')

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

            <canvas id="comprasGraficoMes" class="mt-5" style="display: none;"></canvas>
            <canvas id="comprasGraficoDia" class="mt-5" style="display: none;"></canvas>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Asegúrate de incluir Chart.js -->

    <script>


        const btnMostrar





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
                document.getElementById('comprasGraficoMes').style.display = 'block';
                document.getElementById('comprasGraficoDia').style.display = 'none';
            } else {
                document.getElementById('formMes').style.display = 'none';
                document.getElementById('formDia').style.display = 'block';
                document.getElementById('comprasGraficoMes').style.display = 'none';
                document.getElementById('comprasGraficoDia').style.display = 'block';
            }
        }

        // Evento de cambio para el tipo de gráfica
        document.getElementById('tipoGrafica').addEventListener('change', function () {
            const tipo = this.value;
            saveToLocalStorage();
            updateFormAndChart(tipo);

            // Verificar si hay datos y graficar automáticamente si es necesario
            if (tipo === 'mes') {
                const labelsMes = {!! json_encode($labelsMes) !!};
                const valuesMes = {!! json_encode($valuesMes) !!};
                if (labelsMes.length > 0 && valuesMes.length > 0) {
                    graficarDatosMes(labelsMes, valuesMes);
                }
            } else {
                const labelsDia = {!! json_encode($labelsDia) !!};
                const valuesDia = {!! json_encode($valuesDia) !!};
                if (labelsDia.length > 0 && valuesDia.length > 0) {
                    graficarDatosDia(labelsDia, valuesDia);
                }
            }
        });

        // Al hacer clic en mostrar gráfica de mes
        document.getElementById('mostrarGraficaMes').addEventListener('click', function () {
            saveToLocalStorage();
            const mesInicio = document.getElementById('mesInicio').value;
            const mesFinal = document.getElementById('mesFinal').value;
            window.location.href = `/reportes/graficos/compras?mesInicio=${mesInicio}&mesFinal=${mesFinal}`;
        });

        // Al hacer clic en mostrar gráfica de día
        document.getElementById('mostrarGraficaDia').addEventListener('click', function () {
            saveToLocalStorage();
            const diaInicio = document.getElementById('diaInicio').value;
            const diaFinal = document.getElementById('diaFinal').value;
            window.location.href = `/reportes/graficos/compras?diaInicio=${diaInicio}&diaFinal=${diaFinal}`;
        });

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
                document.getElementById('comprasGraficoMes').style.display = 'none';
            }

            if (labelsDia.length > 0 && valuesDia.length > 0) {
                graficarDatosDia(labelsDia, valuesDia);
            } else {
                document.getElementById('comprasGraficoDia').style.display = 'none';
            }

            // Asegurarse de mostrar el gráfico correspondiente según el tipo
            const tipoGrafica = localStorage.getItem('tipoGrafica') || 'mes';
            updateFormAndChart(tipoGrafica);

            // Si el tipo es mes y hay datos para el gráfico, generarlo automáticamente
            if (tipoGrafica === 'mes' && labelsMes.length > 0 && valuesMes.length > 0) {
                graficarDatosMes(labelsMes, valuesMes);
            }
        };

        function graficarDatosMes(labels, values) {
            const ctx = document.getElementById('comprasGraficoMes').getContext('2d');
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
                            type: 'bar' // Este dataset se mostrará como barras
                        },
                        {
                            label: 'Líneas',
                            data: values,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)', // Color de fondo para la línea
                            borderColor: 'rgba(255, 99, 132, 1)', // Color del borde para la línea
                            borderWidth: 2,
                            fill: false, // No llenar el área debajo de la línea
                            type: 'line', // Este dataset se mostrará como línea
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Monto total de compras por mes', // Título del gráfico
                            font: {
                                size: 16 // Tamaño de fuente del título
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Rango de meses'  // Etiqueta del eje X
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Monto total (S/)'  // Etiqueta del eje Y
                            }
                        }
                    }
                }
            });
        }


        function graficarDatosDia(labels, values) {
            const ctx = document.getElementById('comprasGraficoDia').getContext('2d');
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
                            type: 'bar' // Este dataset se mostrará como barras
                        },
                        {
                            label: 'Líneas',
                            data: values,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)', // Color de fondo para la línea
                            borderColor: 'rgba(54, 162, 235, 1)', // Color del borde para la línea
                            borderWidth: 2,
                            fill: false, // No llenar el área debajo de la línea
                            type: 'line', // Este dataset se mostrará como línea
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Monto total de compras por día', // Título del gráfico
                            font: {
                                size: 16 // Tamaño de fuente del título
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Rango de días'  // Etiqueta del eje X
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Monto total (S/)'  // Etiqueta del eje Y
                            }
                        }
                    }
                }
            });
        }


    </script>
@stop
