---
name: db-design-audit
description: Auditoría de diseño relacional, normalización, detección de redundancias indebidas vs snapshots históricos e integridad de datos en MySQL y Laravel para Eli Boutique.
---

# 🗄️ DATABASE DESIGN & NORMALIZATION AUDIT - SKILL

> **Activación:** `skill:db-design-audit`  
> **Propósito:** Protocolo para auditar migraciones, verificar llaves foráneas, índices, consistencia de tipos de datos y preservación de snapshots históricos en Eli Boutique.

---

## 🎯 1. PRINCIPIOS DE DISEÑO RELACIONAL

### 1.1 Redundancia Indebida vs. Snapshot Histórico

| Tipo de Campo | ¿Es redundancia indebida? | ¿Cómo debe tratarse? | Ejemplo en Eli Boutique |
| :--- | :--- | :--- | :--- |
| **Atributos de Entidades Relacionadas**<br>*(Nombre de Categoría, Género, Stock actual de catálogo)* | ❌ **Sí, si la tabla ya tiene la FK.**<br>Duplica datos que mutan en el maestro. | **Obtener mediante relación Eloquent / JOIN.** | En `ventas`, no duplicar el nombre del cliente si ya existe `cliente_id`. |
| **Snapshots Históricos Transaccionales**<br>*(Precio unitario vendido, RUC/Razón Social en Factura, Base Imponible, IGV)* | ✅ **NO, es Snapshot Obligatorio.**<br>La venta es un documento legal/fiscal inmutable. | **Congelar copia del dato en la fila transaccional.** | `venta_detalles.precio_unitario` y `ventas.ruc_factura` deben congelarse al momento del cobro. |
| **Totales Consolidados**<br>*(Subtotal, IGV, Monto Total)* | ✅ **Permitido en cabeceras fiscales.** | **Calcular y guardar en la transacción.** | `ventas.subTotal`, `ventas.IGV` y `ventas.montoTotal`. |

---

## 📋 2. CHECKLIST DE AUDITORÍA DE MIGRACIONES Y MODELOS

Al revisar o crear cualquier migración o modelo Eloquent:

### 2.1 Tipos Numéricos y Monetarios
- [ ] Todo importe monetario (`precioP`, `subTotal`, `IGV`, `montoTotal`, `ingresoDiario`, `egresoDiario`, `importe`, `vuelto`) debe usar `decimal(10, 2)`.
- [ ] Cantidades de inventario deben usar `integer` o `unsignedInteger`.

### 2.2 Integridad Referencial e Índices
- [ ] Toda columna foránea (`cliente_id`, `producto_id`, `caja_id`, `comprobante_id`, etc.) debe tener su `foreignId()->constrained()` correspondiente.
- [ ] Crear índices para columnas de filtrado frecuente (`codigoVenta`, `codigoCompra`, `fecha`, `estado_transaccion_id`).

### 2.3 Preservación Histórica
- [ ] Las relaciones de entidades con `SoftDeletes` (`Producto`, `Cliente`, `Proveedor`) deben incluir `->withTrashed()` en las relaciones de `Venta`, `Compra`, `VentaDetalle` y `CompraDetalle`.
