---
name: inventory-size-matrix
description: Protocolo para la administración de la matriz de tallas, variantes de producto, stock físico, importaciones masivas Excel y prevención de quiebres de inventario en Eli Boutique.
---

# 📏 INVENTORY SIZE MATRIX - SKILL (ELI BOUTIQUE)

> **Activación:** `skill:inventory-size-matrix` o al modificar `ProductoController`, `ProductoTallaStock`, `ProductoExcelService` o lógica de variantes.  
> **Propósito:** Mantener la consistencia del catálogo de prendas de vestir, garantizando que el stock físico por talla sea la única fuente real de existencias y evitando sobreventas o quiebres no detectados.

---

## 🧵 1. ARQUITECTURA DE LA MATRIZ DE TALLAS

En una boutique de moda, un producto físico existe en múltiples variantes de talla:

```text
       ┌──────────────────────┐
       │      PRODUCTO        │ (Código, Descripción, Precio, Género, Categoría)
       └──────────┬───────────┘
                  │
        (1 a N con ProductoTallaStock)
                  ▼
 ┌───────────────────────────────────────────────┐
 │             producto_talla_stock              │
 ├─────────────────┬──────────────┬──────────────┤
 │ producto_id     │ talla_id     │ stock actual │
 ├─────────────────┼──────────────┼──────────────┤
 │ ID 10 (Vestido) │ 1 (Talla S)  │ 4 unidades   │
 │ ID 10 (Vestido) │ 2 (Talla M)  │ 8 unidades   │
 │ ID 10 (Vestido) │ 3 (Talla L)  │ 2 unidades   │
 └─────────────────┴──────────────┴──────────────┘
```

* **Regla Central:** El stock total del producto en vitrina es siempre una suma agregada calculada:
  $$\text{Stock Total Producto} = \sum \text{producto\_talla\_stock.stock}$$
  **Nunca** debe mantenerse un campo redundante en `productos` que se desincronice del stock por talla.

---

## 🔒 2. CONTROL DE CONCURRENCIA EN VENTAS Y CARRITO

Al interactuar con el punto de venta (POS) o carrito:

1. **Normalización de Líneas:** Si el usuario añade la misma prenda y la misma talla en dos momentos distintos, el servicio debe agruparlas antes de validar existencias.
2. **Bloqueo Pesimista Obligatorio:**
   ```php
   $tallaStock = ProductoTallaStock::where('producto_id', $productoId)
       ->where('producto_talla_id', $tallaId)
       ->lockForUpdate()
       ->first();
   ```
3. **Validación de Quiebre de Stock:**
   Si `$tallaStock->stock < $cantidadSolicitada`, abortar inmediatamente con `ValidationException` indicando el nombre de la prenda, la talla y el stock remanente exacto.

---

## 📊 3. IMPORTACIÓN MASIVA Y ACTUALIZACIÓN EXCEL

A través de `ProductoExcelService`:

1. **Atomicidad:** Toda la carga del archivo Excel debe ejecutarse dentro de `DB::transaction()`. Si una sola fila tiene datos corruptos (ej. talla inexistente o precio negativo), la importación completa debe revertirse informando el número de fila.
2. **Validación de Tallas:** Comprobar que cada columna o valor de talla exista en la tabla `producto_tallas`. Si no existe, crearla o rechazar según la política definida.
3. **Sincronización:** Insertar o actualizar la fila en `producto_talla_stock` preservando los identificadores existentes.
