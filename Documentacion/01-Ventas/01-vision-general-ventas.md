# 🏷️ MÓDULO DE VENTAS - VISIÓN GENERAL Y CICLO DE VIDA

> **Módulo:** Ventas y Facturación  
> **Controlador Principal:** `App\Http\Controllers\VentaController`  
> **Servicio de Negocio:** `App\Services\VentaService`  
> **Modelos Involucrados:** `Venta`, `VentaDetalle`, `Cliente`, `Caja`, `Pago`, `MovimientoInventario`

---

## 🎯 1. PROPÓSITO DEL MÓDULO

El módulo de Ventas gestiona las operaciones comerciales en mostrador y punto de venta de Eli Boutique. Está diseñado para garantizar:
1. **Atención rápida en caja (POS Express):** Registro y cobro de una venta en menos de 5 segundos.
2. **Cero sobreventa (Over-selling):** Reserva concurrente de prendas por talla bloqueadas en base de datos.
3. **Trazabilidad 360°:** Vínculo directo entre el cliente, el colaborador vendedor, la caja abierta, los medios de pago y la salida en Kardex.

---

## 🔄 2. MÁQUINA DE ESTADOS DE LA VENTA

Toda venta transita de manera determinista y unidireccional por los siguientes estados (`estado_transacciones`):

```text
       ┌──────────────────────┐
       │   Carrito / POS      │
       └──────────┬───────────┘
                  │ Confirmar Venta (Reserva de stock talla con lockForUpdate)
                  ▼
       ┌──────────────────────┐
       │     1. PENDIENTE     │  ───► Stock reservado / Caja intacta / No emite ticket fiscal
       └──────────┬───────────┘
                  │ Cobro en Caja (PagoService)
                  ▼
       ┌──────────────────────┐
       │      2. PAGADO       │  ───► Caja incrementada / Emisión de Boleta o Factura PDF
       └──────────┬───────────┘
                  │ Solicitud de anulación oficial (VentaService::anular)
                  ▼
       ┌──────────────────────┐
       │      3. ANULADO      │  ───► Stock devuelto al Kardex / Caja decrementada si era del día
       └──────────────────────┘
```

### Reglas Inquebrantables de los Estados:
* **Pendiente:** Funciona como sistema de apartado/separación. La prenda física queda reservada en tienda para el cliente, por lo que su stock se descuenta para evitar que otro cajero la venda. Sin embargo, la caja **no recibe un solo centavo** hasta que se ejecute el cobro.
* **Pagado:** Valida que el importe entregado cubra el total. Congela el número correlativo y habilita la impresión del comprobante legal.
* **Anulado:** Un estado final irreversible. Una venta anulada jamás puede regresar a estado `Pagado` ni `Pendiente`.

---

## 📊 3. ESTRUCTURA DE DATOS DE LA VENTA

### Cabecera (`ventas`)
* `codigoVenta`: Identificador numérico formateado a 7 dígitos (ej. `0000142`).
* `cliente_id`: Cliente registrado o cliente genérico de mostrador.
* `colaborador_id`: Colaborador que atendió la venta para control de comisiones.
* `caja_id`: Caja diaria a la que pertenece la transacción.
* `subTotal`: Base imponible gravada (Total / 1.18).
* `IGV`: Impuesto General a las Ventas correspondiente al 18% peruano.
* `montoTotal`: Total final a pagar por el cliente.
* `ruc_factura` / `razon_social_factura`: Snapshot inmutable si el cliente solicitó Factura.

### Detalles de la Venta (`venta_detalles`)
* `producto_id`: Prenda vendida (relación `withTrashed()` para no romper históricos).
* `producto_talla_id`: Talla física específica (S, M, L, etc.).
* `cantidad`: Unidades vendidas.
* `precio_unitario`: Precio de venta unitario vigente al momento de cobrar.
* `costo_unitario`: Costo de adquisición congelado para cálculo de margen bruto.
* `subtotal`: Importe de la línea (`cantidad * precio_unitario`).
