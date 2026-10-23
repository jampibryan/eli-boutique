# 📦 MÓDULO DE COMPRAS - VISIÓN GENERAL Y ABASTECIMIENTO

> **Módulo:** Compras y Proveedores  
> **Controlador Principal:** `App\Http\Controllers\CompraController`  
> **Servicio de Negocio:** `App\Services\CompraService`  
> **Modelos Involucrados:** `Compra`, `CompraDetalle`, `Proveedor`, `ProductoTallaStock`, `MovimientoInventario`, `Pago`

---

## 🎯 1. EL ROL ESTRATÉGICO DE LAS COMPRAS EN ELI BOUTIQUE

El módulo de compras a menudo es considerado un simple registro de gastos; sin embargo, en una boutique de moda, **las Compras constituyen la génesis absoluta del inventario y del negocio**:

1. **Origen del Stock Físico:** Ninguna prenda puede venderse sin haber ingresado formalmente a través de una orden de compra o balance inicial.
2. **Determinación del Margen de Ganancia:** El costo negociado con los talleres o proveedores mayoristas determina la rentabilidad real de cada prenda.
3. **Control de Abastecimiento por Tallas:** Permite identificar qué tallas (S, M, L, XL) tienen mayor rotación y cuáles requieren reposición inmediata.

---

## 🗺️ 2. EL CICLO DE VIDA DE LA COMPRA

A diferencia de una venta que suele ocurrir en segundos, una compra puede tomar días entre su requerimiento, negociación de precio, despacho del proveedor y verificación física en almacén:

```text
  [1. Borrador] ──► Requerimiento interno de prendas y tallas.
        │
        ▼
  [2. Enviada]  ──► Notificación por correo al proveedor solicitando cotización.
        │
        ▼
  [3. Cotizada] ──► Captura de precios unitarios pactados, descuentos y PDF del proveedor.
        │
        ▼
  [4. Aprobada] ──► Autorización gerencial para proceder con la orden.
        │
        ▼
  [5. Recibida] ──► Ingreso físico en almacén. Se incrementa el stock por talla y el Kardex.
        │
        ▼
  [6. Pagada]   ──► Desembolso financiero registrado en PagoService e imputado a Caja Diaria.
```

---

## 🤝 3. ENTIDADES PARTICIPANTES

* **Proveedor:** Fabricante o taller confeccionista (RUC, Razón Social, Teléfono, Correo, Tipo de Proveedor). Relación protegida con `SoftDeletes`.
* **Colaborador Comprador:** Encargado de compras que gestiona la cotización y aprueba la recepción de prendas.
* **Orden de Compra (`compras`):** Documento cabecera con código correlativo (ej. `0000045`), fechas de ciclo y totales.
* **Líneas de Compra (`compra_detalles`):** Desglose detallado de prendas, tallas solicitadas, precios cotizados y subtotales.
