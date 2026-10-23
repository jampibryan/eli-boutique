# 🧾 EMISIÓN DE COMPROBANTES PDF (BOLETAS Y FACTURAS)

> **Módulo:** Ventas  
> **Motor de Render:** DomPDF (`barryvdh/laravel-dompdf`)  
> **Formato:** Ticket térmico estándar continuo de 80mm  
> **Vista Blade:** `resources/views/Venta/comprobante_moderno.blade.php`

---

## 🖨️ 1. ESTÁNDAR TÉRMICO DE 80MM (ROLLO CONTINUO)

En el retail moderno de boutique, la emisión de comprobantes se realiza preferentemente en impresoras térmicas de punto de venta (Epson, Bixolon, Xprinter, etc.).

### Dimensiones y Geometría en DomPDF:
* **Ancho fijo:** $80\text{ mm} = 226.77\text{ pt}$ (puntos PostScript).
* **Alto dinámico:** Se calcula proporcionalmente al número de ítems vendidos para evitar cortes de papel innecesarios o desbordes:
  ```php
  $itemsCount = $venta->detalles->count();
  $altoPuntos = max(520, 420 + ($itemsCount * 32));
  $pdf->setPaper([0, 0, 226.77, $altoPuntos], 'portrait');
  ```

---

## 📋 2. TIPOS DE COMPROBANTES EMITIDOS

### A. Boleta de Venta
* Emitida a consumidores finales.
* Muestra el DNI o nombre del cliente.
* Detalle de prendas, tallas, precios unitarios y total con IGV incluido.

### B. Factura Comercial
* Emitida a personas jurídicas o con negocio (RUC 10 o RUC 20).
* Exige validación previa de:
  * `ruc_factura`: 11 dígitos numéricos exactos (`regex:/^\d{11}$/`).
  * `razon_social_factura`: Razón social legal registrada ante SUNAT.
* Desglosa en el ticket:
  * **Op. Gravada (SubTotal):** Base imponible sin impuesto.
  * **IGV (18%):** Impuesto desglosado.
  * **Importe Total:** Monto cancelado.

---

## 🔒 3. INMUTABILIDAD DEL COMPROBANTE (SNAPSHOT FISCAL)

Un principio cardinal de Eli Boutique es que el comprobante emitido **nunca cambia en el tiempo**:
1. Si el día de mañana el administrador sube el precio de un vestido de S/ 80.00 a S/ 120.00, las boletas y facturas emitidas en el pasado **conservan intacto el precio de S/ 80.00**.
2. Los datos fiscales se leen directamente de los campos inmutables de `venta_detalles` y `ventas`, garantizando coherencia tributaria y contable permanente.
