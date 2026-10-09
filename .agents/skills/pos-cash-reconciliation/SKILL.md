---
name: pos-cash-reconciliation
description: Protocolo operativo para cuadratura de caja diaria, arqueo de turno, gestión de sobrantes/faltantes y ejecución segura de reversiones de ventas y compras en Eli Boutique.
---

# 💵 POS CASH RECONCILIATION - SKILL (ELI BOUTIQUE)

> **Activación:** `skill:pos-cash-reconciliation` o al auditar/modificar flujos de `CajaController`, `PagoService` y arqueo de turnos.  
> **Propósito:** Estandarizar el protocolo de cuadratura financiera diaria, prevención de fugas de capital y ejecución segura de reversiones sin descuadrar la caja física.

---

## ⏰ 1. CICLO DIARIO OPERATIVO DE CAJA

```text
[1. APERTURA (Mañana)]  ➔ Conteo físico inicial ➔ Registro de monto_apertura en hora PET
           ⬇
[2. OPERACIÓN CONTINUA] ➔ Cobros de ventas (+) ➔ Pagos a proveedores (-) ➔ Bloqueos con lockForUpdate()
           ⬇
[3. ARQUEO (Fin Turno)]  ➔ Conteo ciego de billetes/monedas vs. Saldo del Sistema
           ⬇
[4. CIERRE (Vespertino)] ➔ Registro de hora_cierre, monto_cierre y justificación de diferencias
```

---

## 🔍 2. PROTOCOLO DE CONCILIACIÓN ENTRE PAGOS Y CAJA

Para verificar que la caja no tiene inconsistencias con los comprobantes emitidos:

1. **Cruce de Ingresos:**
   $$\sum \text{pagos.importe (de ventas del día)} \equiv \text{cajas.ingresoDiario}$$
2. **Cruce de Egresos:**
   $$\sum \text{pagos.importe (de compras pagadas del día)} \equiv \text{cajas.egresoDiario}$$
3. **Cálculo de Descuadre:**
   $$\Delta = \text{Monto Físico Contado} - (\text{monto\_apertura} + \text{ingresoDiario} - \text{egresoDiario})$$
   - Si $\Delta > 0$: Sobrante registrado en observaciones de cierre.
   - Si $\Delta < 0$: Faltante justificado por el colaborador responsable.

---

## 🔄 3. PROCEDIMIENTO PASO A PASO PARA REVERSIÓN DE VENTAS

Cuando se solicita anular o revertir una venta:

1. **Paso 1: Validación de Estado Inicial**
   - Verificar si la venta está en `Pendiente`, `Pagado` o `Anulado`.
   - Si ya está `Anulado`, rechazar la solicitud de inmediato.
2. **Paso 2: Bloqueo Transaccional**
   - Abrir `DB::transaction()`.
   - Bloquear las filas de `ProductoTallaStock` de todos los detalles de la venta mediante `lockForUpdate()`.
   - Si estaba `Pagado`, bloquear el registro de la `Caja` asociada con `lockForUpdate()`.
3. **Paso 3: Restitución de Mercadería**
   - Por cada detalle con `producto_talla_id`, ejecutar:
     `$stockTalla->increment('stock', $detalle->cantidad);`
4. **Paso 4: Ajuste de Saldos de Caja**
   - Si la venta estaba en `Pagado`:
     - Disminuir `ingresoDiario` por el `montoTotal`.
     - Disminuir `productosVendidos` por la suma de prendas.
     - Validar si el cliente tiene otras ventas pagadas en el día antes de decrementar `clientesHoy`.
   - Si la venta estaba en `Pendiente`:
     - **NO tocar ningún campo numérico de la caja**.
5. **Paso 5: Actualización de Estado y Log**
   - Cambiar `estado_transaccion_id` al ID de `Anulado`.
   - Registrar la trazabilidad en logs con usuario y motivo.
