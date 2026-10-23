# 💳 COMPROBANTES DE PROVEEDOR Y DESEMBOLSO EN CAJA

> **Módulo:** Compras / Pagos / Caja Diaria  
> **Servicio:** `App\Services\PagoService::register()`  
> **Impacto Financiero:** Afecta directamente `cajas.egresoDiario`

---

## 🧾 1. COMPROBANTE DEL PROVEEDOR VS COMPROBANTE INTERNO

Un error conceptual recurrente en los sistemas es confundir el comprobante de venta emitido por Eli Boutique con el comprobante que entrega el proveedor:

* **Comprobante Interno (Venta):** Boleta o Factura electrónica generada por Eli Boutique con su propio correlativo (ej. `0000142`).
* **Comprobante del Proveedor (Compra):** Factura o Guía emitida por el taller o mayorista externo, con su propia serie y número legal (ej. `F002-0049102`), la cual sustenta el gasto contable.

### Campos de Trazabilidad en Compras:
* `numero_documento_proveedor`: Serie y correlativo de la factura del proveedor.
* `tipo_documento_proveedor`: Factura, Guía de Remisión, Boleta o Recibo.

---

## 💰 2. EL PAGO DE LA COMPRA EN CAJA

El desembolso financiero a favor del proveedor se procesa a través de `PagoService::register(..., 'compra')`:

### Protocolo de Pago:
1. **Validación de Recepción:** Solo las órdenes en estado `Recibida` pueden pagarse.
2. **Validación de Caja Abierta:** Se verifica que la caja del día actual esté abierta y no haya sido cerrada:
   ```php
   $cajaHoy = Caja::whereDate('fecha', now()->toDateString())->lockForUpdate()->first();
   if (!$cajaHoy || $cajaHoy->hora_cierre) {
       throw ValidationException::withMessages(['caja' => 'La caja del día debe estar abierta para pagar.']);
   }
   ```
3. **Registro en Pagos (`pagos`):**
   * Vincula `compra_id`.
   * Registra el `importe` cancelado y el método de pago empleado (`efectivo`, `transferencia`, etc.).
4. **Impacto en Caja Diaria:**
   * Se incrementa el acumulador de salidas:
     `$cajaHoy->increment('egresoDiario', $pago->importe);`
5. **Transición Final:**
   * La orden pasa oficialmente a estado `Pagada`.
