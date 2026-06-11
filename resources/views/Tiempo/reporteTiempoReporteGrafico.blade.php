<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Reporte de Tiempo - Reportes Gráficos - Eli Boutique</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
            padding: 20px 25px;
        }

        /* ===== HEADER ===== */
        .header {
            border-bottom: 3px solid #5c76e4;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        .header-content {
            display: table;
            width: 100%;
        }

        .logo-section {
            display: table-cell;
            width: 15%;
            vertical-align: middle;
        }

        .logo-section img {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            border: 2px solid #5c76e4;
        }

        .company-info {
            display: table-cell;
            width: 55%;
            text-align: center;
            vertical-align: middle;
        }

        .company-info h1 {
            font-size: 22px;
            color: #5c76e4;
            margin-bottom: 3px;
        }

        .company-info p {
            font-size: 9px;
            color: #777;
            margin: 1px 0;
        }

        .report-info {
            display: table-cell;
            width: 30%;
            text-align: right;
            vertical-align: middle;
            font-size: 9px;
            color: #555;
        }

        .report-info p {
            margin: 2px 0;
        }

        .report-info strong {
            color: #333;
        }

        /* ===== TÍTULO ===== */
        .report-title {
            background-color: #5c76e4;
            color: white;
            padding: 8px 0;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        /* ===== RESUMEN ===== */
        .finance-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .finance-table td {
            text-align: center;
            padding: 10px 5px;
            border: 1px solid #e0e0e0;
        }

        .finance-label {
            font-size: 8px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .finance-value {
            font-size: 14px;
            font-weight: bold;
            margin-top: 2px;
        }

        /* ===== TABLA ===== */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        table.data-table thead th {
            background-color: #5c76e4;
            color: white;
            padding: 7px 5px;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
            border: 1px solid #4e73df;
            text-transform: uppercase;
        }

        table.data-table tbody td {
            padding: 5px 4px;
            text-align: center;
            border: 1px solid #dee2e6;
            font-size: 9.5px;
        }

        table.data-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        /* ===== DURACIÓN BADGES ===== */
        .duracion-rapida, .duracion-normal, .duracion-lenta {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 12px;
            font-size: 8.5px;
            font-weight: bold;
            color: white;
            text-align: center;
        }

        .duracion-rapida {
            background-color: #28a745;
        }

        .duracion-normal {
            background-color: #ff9800;
        }

        .duracion-lenta {
            background-color: #dc3545;
        }

        /* ===== PAGINACIÓN ===== */
        thead { display: table-header-group; }
        .finance-table { page-break-inside: avoid; }

        /* ===== FOOTER ===== */
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #999;
            padding-top: 8px;
            border-top: 1px solid #e0e0e0;
        }

        .footer strong {
            color: #5c76e4;
        }

        .page-number:before {
            content: "Página " counter(page);
        }
    </style>
</head>

<body>
    <!-- HEADER -->
    <div class="header">
        <div class="header-content">
            <div class="logo-section">
                <img src="{{ public_path('img/logo_eli_boutique.png') }}" alt="Logo">
            </div>
            <div class="company-info">
                <h1>Eli Boutique</h1>
                <p>RUC: 20276544711 | Calle Ayacucho 624, Pacanga</p>
                <p>Cel: 922 070 116 | ventas@eliboutique.pe</p>
            </div>
            <div class="report-info">
                <p><strong>Reporte generado:</strong></p>
                <p>{{ \Carbon\Carbon::now()->format('d/m/Y h:i A') }}</p>
                <p><strong>Total registros:</strong> {{ $totalRegistros }}</p>
            </div>
        </div>
    </div>

    <!-- TÍTULO -->
    <div class="report-title">
        REPORTE DE TIEMPO DE REPORTES GRÁFICOS
    </div>

    <!-- RESUMEN ESTADÍSTICO -->
    <table class="finance-table">
        <tr>
            <td style="background-color: #f0f2ff;">
                <div class="finance-label">TOTAL REPORTES</div>
                <div class="finance-value" style="color: #5c76e4;">{{ $totalRegistros }}</div>
            </td>
            <td style="background-color: #e8f5e9;">
                <div class="finance-label">TIEMPO PROMEDIO</div>
                <div class="finance-value" style="color: #28a745;">{{ $promedioDuracion }} seg</div>
            </td>
            <td style="background-color: #fff3e0;">
                <div class="finance-label">TIEMPO MÍNIMO</div>
                <div class="finance-value" style="color: #ff9800;">{{ $minDuracion }} seg</div>
            </td>
            <td style="background-color: #fce4ec;">
                <div class="finance-label">TIEMPO MÁXIMO</div>
                <div class="finance-value" style="color: #dc3545;">{{ $maxDuracion }} seg</div>
            </td>
        </tr>
    </table>

    <!-- TABLA DE TIEMPOS -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 15%;">TIEMPO N°</th>
                <th style="width: 15%;">FECHA</th>
                <th style="width: 20%;">TIEMPO INICIAL</th>
                <th style="width: 20%;">TIEMPO FINAL</th>
                <th style="width: 30%;">DURACIÓN (SEGUNDOS)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reportesConTiempo as $rep)
                <tr>
                    <td style="font-weight: bold; color: #5c76e4;">{{ $rep->tiempo_num }}</td>
                    <td>{{ $rep->fecha }}</td>
                    <td>{{ $rep->tiempoInicial }}</td>
                    <td>{{ $rep->tiempoFinal }}</td>
                    <td>
                        @if($rep->duracionSegundos <= 45)
                            <span class="duracion-rapida">{{ $rep->duracionSegundos }} segundos</span>
                        @elseif($rep->duracionSegundos <= 55)
                            <span class="duracion-normal">{{ $rep->duracionSegundos }} segundos</span>
                        @else
                            <span class="duracion-lenta">{{ $rep->duracionSegundos }} segundos</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- FOOTER -->
    <div class="footer">
        <strong>Eli Boutique</strong> — Reporte de Tiempo de Reportes Gráficos generado el {{ \Carbon\Carbon::now()->format('d/m/Y') }}
        | <span class="page-number"></span>
    </div>
</body>

</html>
