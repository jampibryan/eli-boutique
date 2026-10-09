---
name: receipt-pdf-generation
description: Protocolo para renderizado y descarga de comprobantes de venta (Boletas y Facturas) y reportes ejecutivos en PDF mediante DomPDF en Eli Boutique.
---

# 📄 RECEIPT PDF GENERATION - SKILL (ELI BOUTIQUE)

> **Activación:** `skill:receipt-pdf-generation` o al modificar controladores, vistas Blade o plantillas PDF de comprobantes (`VentaController@pdfComprobante`, `ReporteGraficoController`).  
> **Propósito:** Garantizar que todo comprobante fiscal (Boleta/Factura) o reporte PDF emitido sea fiel reflejo inmutable de la transacción, visualmente profesional y sin errores de renderizado.

---

## 🧾 1. TIPOS DE COMPROBANTES Y REQUISITOS TRIBUTARIOS

| Tipo de Comprobante | Código / Prefijo | Documento del Cliente | Requisitos Obligatorios en el PDF |
| :--- | :---: | :---: | :--- |
| **Boleta de Venta** | `Boleta_` | DNI (8 dígitos) o Sin Documento | Código de venta, fecha/hora PET, desglose de prendas, talla, precio unitario y total en Soles (`S/.`). |
| **Factura Comercial** | `Factura_` | RUC (11 dígitos) | RUC del cliente, Razón Social, Dirección, Base Imponible (subtotal), IGV (18%), Monto Total. |
| **Orden de Compra** | `Orden-Compra-` | RUC del Proveedor | Código de compra, condiciones de pago, detalle de prendas por talla y precio cotizado. |

---

## ❄️ 2. REGLA DE SNAPSHOT INMUTABLE EN COMPROBANTES

- **NUNCA** recalcular importes o precios unitarios leyendo el catálogo activo de productos al generar el PDF de una venta ya realizada.
- El PDF debe renderizarse **exclusivamente** con los datos almacenados en `ventas` y `venta_detalles`:
  - `venta_detalles.precio_unitario`
  - `venta_detalles.base_imponible`
  - `venta_detalles.igv`
  - `venta_detalles.subtotal`
- **Tallas y Nombres:** Si un producto o talla fue eliminado lógicamente (`SoftDeletes`), la relación debe resolverse con `->withTrashed()` para no romper la vista Blade ni arrojar valores nulos.

---

## ⚙️ 3. CONFIGURACIÓN Y ESTILOS TÉCNICOS PARA DomPDF

1. **Codificación de Moneda y Caracteres Especiales:**
   - La plantilla Blade DEBE incluir `<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>`.
   - Para el símbolo de moneda peruana, utilizar `S/.` explícito o entidad HTML para evitar caracteres corruptos.
2. **Dimensiones y Orientación:**
   - Reportes ejecutivos y facturas A4: `$pdf->setPaper('A4', 'portrait')`.
   - Formato Ticket POS (si aplica): Tamaño de papel personalizado con ancho fijo de 80mm.
3. **Estilos CSS Compatibles con DomPDF:**
   - ❌ Evitar Flexbox o CSS Grid modernos (DomPDF no los interpreta fielmente).
   - ✅ Utilizar tablas HTML puras (`<table>`, `<tr>`, `<td>`) con anchos porcentuales fijos.
   - Usar `page-break-inside: avoid;` en filas de tabla para evitar cortes antiestéticos entre páginas.
   - Imágenes institucionales (logos): Cargar rutas absolutas locales con `public_path('...')` o cadenas en base64 para evitar llamadas HTTP externas que bloqueen el proceso.
