@php
    use Illuminate\Support\Facades\Auth;

    $isFactura = $tipoComprobante === 'Factura';
    $usuario = Auth::user();
    $clienteNombre = trim(($venta->cliente->nombreCliente ?? '') . ' ' . ($venta->cliente->apellidoCliente ?? ''));
    $baseImponible = $venta->detalles->sum('base_imponible');
    $igvTotal = $venta->detalles->sum('igv');
@endphp

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isFactura ? 'Factura' : 'Boleta' }} {{ $venta->codigoVenta }}</title>
    <style>
        @page {
            margin: 24px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #1f2937;
            font-size: 12px;
            margin: 0;
            background: #ffffff;
        }

        .page {
            border: 1px solid #d9dee8;
            border-radius: 16px;
            overflow: hidden;
        }

        .topbar {
            height: 10px;
            background: #183153;
        }

        .shell {
            padding: 24px 26px 18px 26px;
        }

        .company-header {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 22px;
        }

        .company-header td {
            vertical-align: top;
        }

        .brand {
            width: 60%;
        }

        .brand table {
            width: 100%;
            border-collapse: collapse;
        }

        .brand td {
            vertical-align: middle;
        }

        .logo-box {
            width: 86px;
        }

        .logo {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            border: 4px solid #f3d4df;
            background: #fff5f8;
        }

        .brand-name {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #183153;
            margin: 0 0 4px 0;
        }

        .brand-copy {
            color: #5b6472;
            line-height: 1.5;
            font-size: 11px;
            margin: 0;
        }

        .doc-box {
            width: 40%;
            text-align: right;
        }

        .doc-card {
            display: inline-block;
            width: 215px;
            border: 2px solid #183153;
            border-radius: 14px;
            overflow: hidden;
            text-align: center;
        }

        .doc-card .label {
            background: #183153;
            color: #ffffff;
            padding: 10px 12px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .doc-card .series {
            padding: 16px 12px;
            color: #183153;
            font-size: 22px;
            font-weight: 800;
        }

        .meta-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 12px 0;
            margin: 0 -12px 18px -12px;
        }

        .meta-table td {
            width: 50%;
            vertical-align: top;
        }

        .panel {
            border: 1px solid #d9dee8;
            border-radius: 14px;
            overflow: hidden;
            min-height: 118px;
        }

        .panel-title {
            background: #f7f9fc;
            color: #183153;
            padding: 10px 14px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border-bottom: 1px solid #d9dee8;
        }

        .panel-body {
            padding: 12px 14px;
        }

        .field {
            margin-bottom: 8px;
        }

        .field:last-child {
            margin-bottom: 0;
        }

        .field-label {
            display: block;
            color: #6b7280;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 2px;
        }

        .field-value {
            color: #111827;
            font-size: 12px;
            font-weight: 600;
        }

        .items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        .items thead th {
            background: #183153;
            color: #ffffff;
            padding: 10px 8px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            border: 0;
        }

        .items tbody td {
            border-bottom: 1px solid #e6eaf0;
            padding: 10px 8px;
            vertical-align: top;
            font-size: 11px;
        }

        .items tbody tr:nth-child(even) {
            background: #fbfcfe;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .product-name {
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 2px;
        }

        .product-meta {
            color: #6b7280;
            font-size: 10px;
        }

        .summary-wrap {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-wrap td {
            vertical-align: top;
        }

        .summary-note {
            width: 56%;
            padding-right: 16px;
        }

        .summary-note .note-card {
            border: 1px solid #d9dee8;
            border-radius: 14px;
            padding: 14px 16px;
            background: #fbfcfe;
        }

        .note-title {
            color: #183153;
            font-weight: 700;
            margin: 0 0 8px 0;
            font-size: 12px;
        }

        .note-copy {
            color: #5b6472;
            font-size: 11px;
            line-height: 1.6;
            margin: 0;
        }

        .summary-box {
            width: 44%;
        }

        .totals {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #d9dee8;
            border-radius: 14px;
            overflow: hidden;
        }

        .totals td {
            padding: 10px 14px;
            border-bottom: 1px solid #e6eaf0;
            font-size: 11px;
        }

        .totals tr:last-child td {
            border-bottom: 0;
        }

        .totals .label {
            color: #5b6472;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .totals .value {
            text-align: right;
            font-weight: 700;
            color: #111827;
        }

        .totals .grand td {
            background: #183153;
            color: #ffffff;
            font-size: 13px;
            font-weight: 800;
        }

        .footer {
            margin-top: 18px;
            padding-top: 12px;
            border-top: 1px solid #d9dee8;
            color: #6b7280;
            font-size: 10px;
            text-align: center;
            line-height: 1.6;
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="topbar"></div>
        <div class="shell">
            <table class="company-header">
                <tr>
                    <td class="brand">
                        <table>
                            <tr>
                                <td class="logo-box">
                                    <img class="logo" src="{{ public_path('img/logo_eli_boutique.png') }}" alt="Logo Eli Boutique">
                                </td>
                                <td>
                                    <div class="brand-name">Eli Boutique</div>
                                    <p class="brand-copy">
                                        Boutique de ropa y accesorios<br>
                                        Sistema de gestion comercial e inventario<br>
                                        RUC: 20123456789 | Telefono: 987 654 321
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="doc-box">
                        <div class="doc-card">
                            <div class="label">{{ $isFactura ? 'Factura de venta' : 'Boleta de venta' }}</div>
                            <div class="series">{{ $venta->codigoVenta }}</div>
                        </div>
                    </td>
                </tr>
            </table>

            <table class="meta-table">
                <tr>
                    <td>
                        <div class="panel">
                            <div class="panel-title">Datos del cliente</div>
                            <div class="panel-body">
                                <div class="field">
                                    <span class="field-label">{{ $isFactura ? 'Razon social / cliente' : 'Cliente' }}</span>
                                    <span class="field-value">{{ $clienteNombre !== '' ? $clienteNombre : 'Cliente no disponible' }}</span>
                                </div>
                                <div class="field">
                                    <span class="field-label">Documento</span>
                                    <span class="field-value">{{ $venta->cliente->dniCliente ?? 'No registrado' }}</span>
                                </div>
                                <div class="field">
                                    <span class="field-label">Estado</span>
                                    <span class="field-value">{{ $venta->estadoTransaccion->descripcionET ?? 'No definido' }}</span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="panel">
                            <div class="panel-title">Datos de emision</div>
                            <div class="panel-body">
                                <div class="field">
                                    <span class="field-label">Fecha</span>
                                    <span class="field-value">{{ $venta->created_at->format('d/m/Y') }}</span>
                                </div>
                                <div class="field">
                                    <span class="field-label">Hora</span>
                                    <span class="field-value">{{ $venta->created_at->format('h:i A') }}</span>
                                </div>
                                <div class="field">
                                    <span class="field-label">Generado por</span>
                                    <span class="field-value">{{ $usuario->name ?? 'Sistema' }}</span>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>

            <table class="items">
                <thead>
                    <tr>
                        <th style="width: 7%;">#</th>
                        <th style="width: 39%;" class="text-left">Descripcion</th>
                        <th style="width: 12%;">Talla</th>
                        <th style="width: 10%;">Cant.</th>
                        <th style="width: 16%;">P. Unit.</th>
                        <th style="width: 16%;">Importe</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($venta->detalles as $index => $detalle)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-left">
                                <div class="product-name">
                                    {{ $detalle->producto->descripcionP ?? 'Producto eliminado' }}
                                </div>
                                <div class="product-meta">
                                    Codigo interno: {{ $detalle->producto_id }}
                                </div>
                            </td>
                            <td class="text-center">
                                {{ $detalle->talla->talla ?? 'No registrada' }}
                            </td>
                            <td class="text-center">{{ $detalle->cantidad }}</td>
                            <td class="text-right">S/. {{ number_format($detalle->precio_unitario, 2) }}</td>
                            <td class="text-right">S/. {{ number_format($detalle->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <table class="summary-wrap">
                <tr>
                    <td class="summary-note">
                        <div class="note-card">
                            <p class="note-title">Observaciones</p>
                            <p class="note-copy">
                                Este comprobante resume los productos entregados y los importes cobrados en la operacion.
                                @if ($isFactura)
                                    Los valores mostrados consideran base imponible e IGV segun el detalle registrado.
                                @else
                                    Los importes consignados corresponden al precio final cobrado al cliente.
                                @endif
                            </p>
                        </div>
                    </td>
                    <td class="summary-box">
                        <table class="totals">
                            @if ($isFactura)
                                <tr>
                                    <td class="label">Base imponible</td>
                                    <td class="value">S/. {{ number_format($baseImponible, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="label">IGV</td>
                                    <td class="value">S/. {{ number_format($igvTotal, 2) }}</td>
                                </tr>
                            @endif
                            <tr class="grand">
                                <td>Total</td>
                                <td class="text-right">S/. {{ number_format($venta->montoTotal, 2) }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <div class="footer">
                Gracias por su preferencia.<br>
                Eli Boutique - comprobante emitido desde el sistema interno.
            </div>
        </div>
    </div>
</body>

</html>
