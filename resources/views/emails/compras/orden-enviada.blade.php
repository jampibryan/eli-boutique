<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border: 1px solid #dee2e6;
        }
        .info-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .info-row {
            margin: 10px 0;
        }
        .info-label {
            font-weight: bold;
            color: #28a745;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #dee2e6;
            color: #6c757d;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">🛍️ Eli Boutique</h1>
        <p style="margin: 10px 0 0 0; font-size: 18px;">Nueva Orden de Compra</p>
    </div>

    <div class="content">
        <h2 style="color: #28a745;">Estimado Proveedor,</h2>
        
        <p>Le enviamos la orden de compra con el código <strong>#{{ $compra->codigoCompra }}</strong> para su revisión y cotización.</p>

        <div class="info-box">
            <div class="info-row">
                <span class="info-label">📋 Código de Orden:</span>
                <span>{{ $compra->codigoCompra }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">📅 Fecha de Envío:</span>
                <span>{{ \Carbon\Carbon::parse($compra->fecha_envio)->format('d/m/Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">📄 Tipo de Comprobante:</span>
                <span>{{ $compra->comprobante->descripcionCOM }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">📦 Productos:</span>
                <span>{{ $compra->detalles->count() }} artículo(s)</span>
            </div>
        </div>

        <h3 style="color: #28a745;">Próximos Pasos:</h3>
        <ol>
            <li>Revisar el PDF adjunto con el detalle de productos solicitados</li>
            <li>Preparar su cotización con los precios actualizados</li>
            <li>Responder a este correo con la cotización en PDF</li>
        </ol>

        <p><strong>Nota:</strong> Por favor, responda con su cotización a la brevedad posible para proceder con la orden.</p>

        <div style="text-align: center;">
            <p style="margin: 10px 0;">El PDF de la orden de compra se encuentra adjunto a este correo.</p>
        </div>
    </div>

    <div class="footer">
        <p><strong>Eli Boutique</strong></p>
        <p>Este es un correo automático, por favor no responder directamente.<br>
        Para consultas, contacte con nuestro departamento de compras.</p>
    </div>
</body>
</html>
