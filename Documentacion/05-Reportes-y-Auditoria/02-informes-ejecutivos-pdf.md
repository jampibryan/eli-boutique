# 📄 INFORMES EJECUTIVOS Y DOCUMENTOS PDF

> **Módulo:** Reportes / Facturación  
> **Librería:** DomPDF (`barryvdh/laravel-dompdf`)  
> **Controladores:** `VentaController`, `CompraController`, `CajaController`

---

## 🖨️ 1. CATÁLOGO DE REPORTES EN PDF GENERADOS

Eli Boutique genera múltiples documentos ejecutivos listos para imprimir o exportar:

### A. Informe de Caja Diaria (`informeCaja.blade.php`)
* **Disparador:** Desde la vista de detalle de cada caja en `CajaController::informe()`.
* **Contenido:**
  * Datos de apertura y cierre (Código de caja, fecha, hora de cierre).
  * Lista detallada de todas las ventas cobradas en el día con cliente y monto.
  * Consolidado de productos y cantidades físicas despachadas.
  * Lista de compras a proveedores pagadas en la jornada.
  * Balance neto final ($\text{Ingresos} - \text{Egresos}$).

### B. Reporte General de Cajas (`reporteGeneralCaja.blade.php`)
* **Disparador:** `CajaController::pdfCajas()`.
* **Propósito:** Arqueo consolidado entre rangos de fechas (ej. mes completo).

### C. Reporte Consolidado de Compras (`Compra/reporte.blade.php`)
* **Disparador:** `CompraController::pdfCompras()`.
* **Propósito:** Agrupación de compras por proveedor y categoría de producto para control de abastecimiento.

### D. Orden de Compra para Proveedor (`Compra/orden.blade.php`)
* **Disparador:** `CompraController::pdfOrdenCompra()`.
* **Propósito:** Documento formal con membrete de Eli Boutique, código de compra y requerimiento de tallas que se envía al proveedor para cotización.

### E. Comprobante de Venta Térmico 80mm (`Venta/comprobante_moderno.blade.php`)
* **Disparador:** `VentaController::pdfComprobante()`.
* **Propósito:** Boleta o Factura para el consumidor final en formato ticket de punto de venta.
