---
name: cross-module-traceability
description: Protocolo de verificación y auditoría de la trazabilidad transversal de los 4 ejes de Eli Boutique (Ventas, Compras, Stock Tallas y Caja Diaria).
---

# 🔗 CROSS-MODULE TRACEABILITY - SKILL (ELI BOUTIQUE)

> **Activación:** `skill:cross-module-traceability`  
> **Propósito:** Protocolo para auditar, sincronizar y verificar la trazabilidad transversal entre Ventas, Compras, Inventario por Tallas y Caja Diaria.

---

## 🗺️ 1. MAPA TRANSVERSAL DEL CICLO DE VIDA DEL DATO

```text
┌─────────────────────────┐     (Punto de Venta / Carrito)
│ 1. PRODUCTOS Y TALLAS   │ ──────────────────────────────────────────┐
│   producto_talla_stock  │                                           │
└─────────────────────────┘                                           ▼
            ▲                                             ┌───────────────────────┐
            │ (Recepción de Mercadería)                   │ 2. VENTA (Pendiente)  │
            │                                             │   ventas / detalles   │
┌─────────────────────────┐                               └───────────────────────┘
│ 3. ORDEN DE COMPRA      │                                           │
│   Borrador ➔ Enviada ➔  │                                           │ (Cobro / Pago)
│   Cotizada ➔ Aprobada ➔ │                                           ▼
│   Recibida ➔ Pagada     │                               ┌───────────────────────┐
└─────────────────────────┘                               │ 3. PAGO Y CAJA DIARIA │
            │ (Egreso Diario al Pagar)                    │   cajas.ingresoDiario │
            ▼                                             └───────────────────────┘
┌─────────────────────────┐                                           │
│ 4. CAJA DIARIA          │ ◄─────────────────────────────────────────┘
│   Apertura / Cierre     │
└─────────────────────────┘
            │
            ▼ (Emisión de Comprobante PDF Interno)
┌─────────────────────────────────┐
│ 4. COMPROBANTES DE VENTA        │
│   Boleta / Factura en PDF       │
└─────────────────────────────────┘
```

---

## ⚡ 2. LOS 4 EJES DE PROPAGACIÓN TRANSVERSAL

### Eje A: Ventas, Stock por Talla y Carrito
* **Entidades:** `Carrito` (sesión) ➔ `Venta` ➔ `VentaDetalle` ➔ `ProductoTallaStock`.
* **Reglas:**
  1. Al confirmar la venta (inicia en `Pendiente`), el stock de la talla se decrementa inmediatamente usando `lockForUpdate()` para reservar la prenda.
  2. Si la venta se cancela o anula (`anular`), el stock reservado DEBE restituirse a la talla correspondiente en `producto_talla_stock`.
  3. Si la venta se edita en estado `Pendiente`, se calcula la diferencia de stock por talla bloqueando concurrentemente las líneas antiguas y nuevas.

### Eje B: Caja Diaria y Movimientos de Efectivo
* **Entidades:** `Caja` ➔ `Venta` ➔ `Compra` ➔ `Pago`.
* **Reglas:**
  1. No se puede crear una venta ni una orden de compra si la caja del día no está abierta o si ya fue cerrada.
  2. **Venta Pendiente:** NO afecta `ingresoDiario` ni `clientesHoy` de la caja.
  3. **Venta Pagada:** Al registrar el pago en `PagoService`, se marca la venta como `Pagado` y se incrementa `cajas.ingresoDiario` y `clientesHoy`.
  4. **Anulación de Venta:** Solo se resta `ingresoDiario` y `clientesHoy` de la caja SI y solo SI la venta estaba previamente en estado `Pagado`.
  5. **Pago de Compra:** Al pagar una orden de compra recibida, se valida que la caja esté abierta y se incrementa `cajas.egresoDiario`.

### Eje C: Compras y Recepción de Mercadería
* **Entidades:** `Proveedor` ➔ `Compra` ➔ `CompraDetalle` ➔ `ProductoTallaStock` ➔ `Pago`.
* **Flujo Canónico:**
  `Borrador` ➔ `Enviada` ➔ `Cotizada` ➔ `Aprobada` ➔ `Recibida` ➔ `Pagada`.
* **Reglas:**
  1. Solo una orden en estado `Aprobada` puede pasar a `Recibida`.
  2. Al marcar `Recibida`, se incrementa automáticamente el stock por talla en `producto_talla_stock`.
  3. Solo una orden en estado `Recibida` puede ser pagada en `PagoService`.

### Eje D: Preservación Histórica, Comprobantes e Integridad de Maestros
* **Entidades:** `Producto`, `Cliente`, `Proveedor` (con `SoftDeletes`), `Venta`, `Comprobante`.
* **Reglas:**
  1. Toda relación de detalle histórico (`VentaDetalle->producto()`, `CompraDetalle->producto()`, `Venta->cliente()`, `Compra->proveedor()`) DEBE utilizar `->withTrashed()`.
  2. Eliminar lógicamente un producto o cliente nunca debe generar errores `500` ni hacer desaparecer registros en reportes históricos ni en el arqueo de caja.
  3. Comprobantes PDF (Boleta o Factura) deben generarse a partir de los snapshots congelados de venta y venta_detalles.

---

## 🛠️ 3. PROTOCOLO DE REVISIÓN PRE-FLIGHT (PRE-CHANGE CHECK)

Antes de modificar cualquier controlador, modelo o servicio en Eli Boutique:

1. **Identificar Ejes Impactados:** ¿Afecta stock por talla, caja, compras o comprobantes?
2. **Revisar Aguas Arriba y Aguas Abajo:** Si cambia un estado en `ventas` o `compras`, verificar si `cajas` o `producto_talla_stock` requieren actualización simultánea.
3. **Garantizar Transaccionalidad:** Incluir todas las mutaciones dentro de `DB::transaction()` con control de bloqueos (`lockForUpdate()`).
4. **Verificar Ausencia de Huérfanos:** No permitir `destroy()` que deje pagos o detalles huérfanos.
