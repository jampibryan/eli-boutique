# 📏 MATRIZ DE TALLAS Y FUENTE ÚNICA DE VERDAD (SSOT)

> **Módulo:** Inventario  
> **Tabla Clave:** `producto_talla_stock`  
> **Relación:** `Producto` $\longleftrightarrow$ `ProductoTallaStock` $\longleftrightarrow$ `ProductoTalla`

---

## 👗 1. ARQUITECTURA DE LA MATRIZ DE PRENDAS

A diferencia de una ferretería o farmacia donde un producto tiene un stock único, en una boutique de moda cada prenda física existe dividida en **variantes de talla**:

```text
               ┌────────────────────────────────────────────────────────┐
               │                        PRODUCTO                        │
               │  Código: P-001 | Nombre: Vestido Gala Elegance         │
               │  Categoría: Vestidos | Precio: S/ 120.00               │
               └───────────────────────────┬────────────────────────────┘
                                           │
                           (Relación 1 a N con Tallas)
                                           ▼
          ┌──────────────────────────────────────────────────────────────────┐
          │                       producto_talla_stock                       │
          ├─────────────────┬──────────────────────┬─────────────────────────┤
          │   producto_id   │  producto_talla_id   │      stock actual       │
          ├─────────────────┼──────────────────────┼─────────────────────────┤
          │      P-001      │     1 (Talla XS)     │       2 unidades        │
          │      P-001      │     2 (Talla S)      │       5 unidades        │
          │      P-001      │     3 (Talla M)      │       8 unidades        │
          │      P-001      │     4 (Talla L)      │       3 unidades        │
          └─────────────────┴──────────────────────┴─────────────────────────┘
```

---

## 🏛️ 2. PRINCIPIO DE LA FUENTE ÚNICA DE VERDAD (SSOT)

1. **Prohibición de Redundancia:**
   * La tabla `productos` **no almacena una columna de stock total**.
   * Almacenar un stock total en `productos` provocaría que, ante una venta en talla M, el total se desincronice si no se actualiza simultáneamente.
2. **Cálculo Agregado en Tiempo de Ejecución:**
   * El stock total de un producto para la vitrina o catálogo es siempre una propiedad calculada dinámicamente (`getStockTotalAttribute`):
     $$\text{Stock Total Producto} = \sum_{\text{tallas}} \text{producto\_talla\_stock.stock}$$
3. **Restricción de Integridad Única:**
   * La tabla `producto_talla_stock` cuenta con un índice único compuesto:
     `$table->unique(['producto_id', 'producto_talla_id']);`
   * Garantiza que es físicamente imposible duplicar filas para una misma prenda y talla.
