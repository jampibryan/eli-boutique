# 📈 POLÍTICA DE COSTOS DE ADQUISICIÓN Y MARGEN BRUTO

> **Módulo:** Compras / Rentabilidad Comercial  
> **Conceptos:** Costo Unitario de Compra, Margen de Ganancia, Trazabilidad Financiera

---

## 🧮 1. LA ECUACIÓN DEL MARGEN BRUTO EN MODA

En Eli Boutique, cada venta debe reportar la rentabilidad real generada. Esto solo es posible si el costo de adquisición de las compras viaja a través del Kardex hasta el detalle de la venta:

$$\text{Margen Bruto Unitario} = \text{Precio Venta Unitario (sin IGV)} - \text{Costo Adquisición}$$

$$\text{Rentabilidad Porcentual} = \left( \frac{\text{Margen Bruto}}{\text{Precio Venta (sin IGV)}} \right) \times 100\%$$

---

## 🔗 2. LA CADENA DE PROPAGACIÓN DEL COSTO

```text
[1. Cotización Compra] ──► Se define precio unitario pactado (ej. S/ 25.00)
         │
         ▼
[2. Recepción Almacén]  ──► Se actualiza productos.costo_compra = S/ 25.00
         │
         ▼
[3. Asiento en Kardex]  ──► movimientos_inventario guarda costo_unitario = S/ 25.00
         │
         ▼
[4. Venta en POS]       ──► venta_detalles guarda:
                            precio_unitario = S/ 59.00
                            costo_unitario  = S/ 25.00
                            Ganancia Bruta  = S/ 34.00 (57.6% margen)
```

---

## 💡 3. BENEFICIOS PARA LA ADMINISTRACIÓN

1. **Identificación de Prendas Estrella:** Permite saber con certeza cuáles prendas dejan la mayor rentabilidad neta (no solo las que más volumen venden).
2. **Políticas de Descuento Seguras:** Al realizar liquidaciones de temporada o promociones 2x1, la administración conoce el "piso de costo" por debajo del cual no se puede vender sin generar pérdidas.
3. **Poder de Negociación:** Histórico de evolución de precios por proveedor para negociar mejores descuentos por volumen en campañas navideñas o de cambio de estación.
