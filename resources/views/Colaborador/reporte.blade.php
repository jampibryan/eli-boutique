<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Reporte de Colaboradores - Eli Boutique</title>
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
            padding: 15px;
        }

        .header {
            margin-top: 15px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
        }

        .header-content {
            display: table;
            width: 100%;
        }

        .logo-section {
            display: table-cell;
            width: 20%;
            vertical-align: middle;
            padding-left: 10px;
        }

        .logo-section img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #667eea;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }

        .company-info {
            display: table-cell;
            width: 60%;
            text-align: center;
            vertical-align: middle;
            padding: 0 15px;
        }

        .company-info h1 {
            font-size: 24px;
            color: #667eea;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .company-info p {
            font-size: 10px;
            color: #666;
            margin: 2px 0;
        }

        .report-info {
            display: table-cell;
            width: 20%;
            text-align: right;
            vertical-align: middle;
            font-size: 9px;
            color: #666;
            padding-right: 10px;
        }

        .report-info strong {
            color: #333;
        }

        .report-title {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 12px;
            border-radius: 5px;
        }

        .summary-section {
            background: #f8f9fa;
            padding: 10px 15px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
            border-radius: 3px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .summary-section strong {
            color: #667eea;
            font-size: 12px;
        }

        .stats-container {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .stat-box {
            display: table-cell;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 10px;
            text-align: center;
            border-radius: 8px;
            margin: 0 5px;
            border: 2px solid #dee2e6;
        }

        .stat-box .stat-number {
            font-size: 20px;
            font-weight: bold;
            color: #667eea;
            display: block;
            margin-bottom: 3px;
        }

        .stat-box .stat-label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
        }

        .cargo-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
        }

        .cargo-gerente {
            background: #e3f2fd;
            color: #1976d2;
            border: 1px solid #1976d2;
        }

        .cargo-vendedor {
            background: #f3e5f5;
            color: #7b1fa2;
            border: 1px solid #7b1fa2;
        }

        .cargo-cajero {
            background: #fff3e0;
            color: #ef6c00;
            border: 1px solid #ef6c00;
        }

        .cargo-almacenero {
            background: #e8f5e9;
            color: #388e3c;
            border: 1px solid #388e3c;
        }

        .cargo-default {
            background: #f5f5f5;
            color: #616161;
            border: 1px solid #616161;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead {
            background: #667eea;
            color: white;
        }

        thead th {
            padding: 10px 8px;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            border: 1px solid #5568d3;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tbody tr:hover {
            background-color: #e9ecef;
        }

        tbody td {
            padding: 8px 6px;
            text-align: center;
            border: 1px solid #dee2e6;
            font-size: 10px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #666;
            padding-top: 10px;
            border-top: 1px solid #dee2e6;
        }

        .footer strong {
            color: #667eea;
        }

        .page-number:before {
            content: "Página " counter(page);
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <div class="logo-section">
                <img src="{{ public_path('img/logo_eli_boutique.png') }}" alt="Logo">
            </div>
            <div class="company-info">
                <h1>ELI BOUTIQUE</h1>
                <p>Sistema de Gestión Empresarial</p>
                <p>RUC: 20123456789 | Teléfono: 987 654 321</p>
            </div>
            <div class="report-info">
                <p><strong>Fecha:</strong> {{ date('d/m/Y') }}</p>
                <p><strong>Hora:</strong> {{ date('h:i A') }}</p>
                <p><strong>Usuario:</strong> {{ Auth::user()->name ?? 'Sistema' }}</p>
            </div>
        </div>
    </div>

    <!-- Report Title -->
    <div class="report-title">
        👥 REPORTE DE COLABORADORES
    </div>

    <!-- Table -->
    <table>
        <thead>
            <tr>
                <th width="8%">N°</th>
                <th width="18%">CARGO</th>
                <th width="20%">NOMBRE COMPLETO</th>
                <th width="12%">DNI</th>
                <th width="22%">EMAIL</th>
                <th width="12%">TELÉFONO</th>
                <th width="8%">EDAD</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($colaboradores as $index => $colaborador)
                <tr>
                    <td style="font-weight: bold; color: #667eea;">{{ $index + 1 }}</td>
                    <td>
                        @php
                            $cargoNombre = strtolower($colaborador->cargo->descripcionCargo);
                            $badgeClass = 'cargo-default';
                            if (str_contains($cargoNombre, 'gerente')) {
                                $badgeClass = 'cargo-gerente';
                            } elseif (str_contains($cargoNombre, 'vendedor')) {
                                $badgeClass = 'cargo-vendedor';
                            } elseif (str_contains($cargoNombre, 'cajero')) {
                                $badgeClass = 'cargo-cajero';
                            } elseif (str_contains($cargoNombre, 'almacen')) {
                                $badgeClass = 'cargo-almacenero';
                            }
                        @endphp
                        <span class="cargo-badge {{ $badgeClass }}">
                            {{ strtoupper($colaborador->cargo->descripcionCargo) }}
                        </span>
                    </td>
                    <td style="text-align: left; padding-left: 10px;">
                        {{ strtoupper($colaborador->nombreColab) }} {{ strtoupper($colaborador->apellidosColab) }}
                    </td>
                    <td>{{ $colaborador->dniColab }}</td>
                    <td>{{ $colaborador->correoColab }}</td>
                    <td>{{ $colaborador->telefonoColab }}</td>
                    <td>{{ $colaborador->edadColab }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p><strong>Eli Boutique</strong> - Sistema de Gestión | Generado automáticamente el {{ date('d/m/Y h:i A') }}
        </p>
        <p class="page-number"></p>
    </div>
</body>

</html>
