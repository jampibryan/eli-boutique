# 🎯 FUENTE ÚNICA DE VERDAD (SSOT) Y MÁQUINA DE ESTADOS - REGLA #6

> **Prioridad:** MÁXIMA  
> **Siempre Activa:** Sí  
> **Concepto Central:** *Single Source of Truth (SSOT)* en español: **"Fuente Única de Verdad"** (o *"Única Fuente de Verdad"*).  
> **Propósito:** Eliminar silos de datos, ambigüedad transaccional y discrepancias de estado entre módulos de Eli Boutique.

---

## 📌 ¿QUÉ ES SSOT (FUENTE ÚNICA DE VERDAD)?

En Eli Boutique, **SSOT** significa que **cada dato o estado del negocio tiene una y solo una entidad oficial responsable de su existencia, cálculo y persistencia**. 
Ningún otro módulo, controlador o vista tiene permitido recalcular, adivinar o almacenar copias mutables de dicho dato sin sincronización transaccional estricta.

---

## 🏛️ MATRIZ CANÓNICA DE PROPIEDAD DEL DATO (SSOT OWNERSHIP)

| Dimensión de Negocio | Entidad Dueña (SSOT) | Campo / Mecanismo Canónico | Regla de No Redundancia |
| :--- | :--- | :--- | :--- |
| **Stock Real Disponible** | `ProductoTallaStock` | `producto_talla_stock.stock` | ❌ Prohibido descontar de `productos.stock` como si fuera inventario físico; el stock físico real vive **siempre por talla**. |
| **Liquidez en Caja** | `Caja` | `ingresoDiario`, `egresoDiario`, `monto_apertura`, `monto_cierre` | ❌ Prohibido alterar saldos de caja desde controladores; solo eventos transaccionales auditados modifican la caja abierta del día. |
| **Registro de Pago** | `Pago` | `pagos.importe`, `pagos.vuelto`, `comprobante_id` | ❌ El comprobante fiscal y el medio de cobro pertenecen a `Pago`, no a la cabecera genérica de la venta. |
| **Estado Transaccional** | `EstadoTransaccion` | `estado_transaccion_id` vinculado a `ventas` y `compras` | ❌ Prohibido usar flags booleanos sueltos (`is_paid`, `is_cancelled`); el estado se rige por su catálogo oficial. |
| **Snapshot Fiscal/Legal** | `VentaDetalle` / `Venta` | `precio_unitario`, `subtotal`, `base_imponible`, `igv`, `ruc_factura` | ✅ Copia inmutable congelada al facturar; no se recalcula si el catálogo de productos cambia de precio. |

---

## 🔄 MÁQUINA DE ESTADOS DETERMINISTA

Toda orden o comprobante transita únicamente por flujos predefinidos y unidireccionales:

### 1. Ciclo de Ventas
```text
[1. Carrito en Sesión] 
         ⬇ (Confirmar venta con lockForUpdate en stock talla)
[2. Pendiente] (Stock reservado / Caja intacta)
         ⬇ (Cobro en PagoService)
[3. Pagado]    (Caja incrementada / Comprobante emitible)
         ⬇ (Flujo oficial de anulación)
[4. Anulado]   (Stock devuelto a talla / Caja decrementada si estaba Pagado)
```
* **Regla Inquebrantable:** Una venta en estado `Anulado` jamás puede transicionar de regreso a `Pagado` ni `Pendiente`.

### 2. Ciclo de Compras a Proveedores
```text
[1. Borrador] ➔ [2. Enviada] ➔ [3. Cotizada] ➔ [4. Aprobada] ➔ [5. Recibida] ➔ [6. Pagada]
                                                                      ⬇              ⬇
                                                              (Incrementa stock) (Egreso en Caja)
```
* **Regla Inquebrantable:** Prohibido pagar una compra que no haya alcanzado el estado `Recibida` (inspección de mercadería física obligatoria antes del desembolso).
