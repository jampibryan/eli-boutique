@php
    use Illuminate\Support\Facades\Auth;

    $isFactura = $tipoComprobante === 'Factura';
    $usuario = Auth::user();
    $clienteNombre = trim(($venta->cliente->nombreCliente ?? '') . ' ' . ($venta->cliente->apellidoCliente ?? ''));
    $baseImponible = $venta->detalles->sum(fn($d) => $d->cantidad * $d->base_imponible);
    $igvTotal = $venta->detalles->sum(fn($d) => $d->cantidad * $d->igv);
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
            background: linear-gradient(90deg, #2C2C2C 0%, #D4AF37 100%);
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
            border: 4px solid #D4AF37;
            background: #fdfcf7;
        }

        .brand-name {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #2C2C2C;
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
            border: 2px solid #2C2C2C;
            border-radius: 14px;
            overflow: hidden;
            text-align: center;
        }

        .doc-card .label {
            background: #2C2C2C;
            color: #ffffff;
            padding: 10px 12px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .doc-card .series {
            padding: 16px 12px;
            color: #D4AF37;
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
            color: #2C2C2C;
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
            background: #2C2C2C;
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
            color: #2C2C2C;
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
            background: #2C2C2C;
            color: #D4AF37;
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
                                         RUC: 20276544711 | Celular: 922 070 116<br>
                                         Calle Ayacucho 624, Pacanga
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="doc-box">
                        <div class="doc-card">
                            <div class="label">{{ $isFactura ? 'Factura Electrónica' : 'Boleta de Venta Electrónica' }}</div>
                            <div class="series">{{ $isFactura ? 'F001' : 'B001' }}-{{ str_pad($venta->codigoVenta, 8, '0', STR_PAD_LEFT) }}</div>
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
                                    <span class="field-value">{{ $venta->colaborador->nombreColab ?? ($usuario->name ?? 'Sistema') }}</span>
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
                                {{ $detalle->talla->descripcion ?? ($detalle->producto->tallas->first()->descripcion ?? '-') }}
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

            @if ($venta->documentoSunat && $venta->documentoSunat->signature_hash)
                <div style="margin-top: 15px; border: 1px dashed #d9dee8; padding: 10px; border-radius: 8px; font-size: 10px; color: #5b6472;">
                    <table style="width: 100%; border-collapse: collapse; border: none;">
                        <tr style="border: none;">
                            <td style="vertical-align: middle; width: 80%; border: none; padding: 0;">
                                <strong>Código Hash:</strong> {{ $venta->documentoSunat->signature_hash }}<br>
                                <span style="font-size: 9px; line-height: 1.3;">Representación impresa de la {{ $isFactura ? 'Factura Electrónica' : 'Boleta de Venta Electrónica' }}.<br>
                                Autorizado mediante resolución de SUNAT. Consulte su validez en el portal oficial.</span>
                            </td>
                            <td style="vertical-align: middle; text-align: right; width: 20%; border: none; padding: 0;">
                                <div style="font-weight: bold; border: 1px solid #D4AF37; color: #D4AF37; display: inline-block; padding: 5px 8px; border-radius: 4px; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px;">
                                    SUNAT ACEPTADO
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            @endif

            <div class="footer">
                Gracias por su preferencia.<br>
                Eli Boutique - comprobante emitido desde el sistema interno.
            </div>
        </div>
    </div>
</body>

</html>
