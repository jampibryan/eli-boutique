# 📑 KARDEX MULTITALLA Y REGISTRO DE MOVIMIENTOS

> **Módulo:** Inventario / Auditoría  
> **Tabla:** `movimientos_inventario`  
> **Modelo:** `App\Models\MovimientoInventario`

---

## 🔍 1. ¿QUÉ ES EL KARDEX Y POR QUÉ ES INDISPENSABLE?

El Kardex es el **libro mayor de auditoría del almacén**. Registra cada una de las entradas, salidas y saldos físicos de cada producto en cada una de sus tallas, junto con el costo y documento de respaldo.

Sin Kardex, si una prenda tiene 3 unidades en vitrina, nadie sabe si ayer había 5 porque se vendieron 2, si se extraviaron o si entraron por una compra. **El Kardex responde con precisión quién, cuándo, cómo y por qué cambió el inventario.**

---

## 🗃️ 2. TIPOS DE MOVIMIENTOS RECONOCIDOS

| Tipo de Movimiento (`tipo_movimiento`) | Signo | Origen | Documento de Respaldo |
| :--- | :---: | :--- | :--- |
| `INGRESO_COMPRA` | **(+)** | Recepción de mercadería desde `CompraService::receive()` | Orden de Compra (ej. `0000045`) |
| `SALIDA_VENTA` | **(-)** | Venta confirmada en mostrador desde `VentaService::create()` | Venta (ej. `0000102`) |
| `REVERSION_VENTA` | **(+)** | Anulación de venta desde `VentaService::anular()` | Anulación Venta (ej. `0000102`) |
| `AJUSTE_INVENTARIO` | **(±)** | Regularización por conteo físico o merma de confección | Acta de Ajuste con motivo |
| `INVENTARIO_INICIAL` | **(+)** | Saldo de apertura al iniciar operaciones con el sistema | Balance de Inventario Inicial |

---

## 📊 3. ESQUEMA DE CAMPOS DEL KARDEX

```sql
CREATE TABLE movimientos_inventario (
    id BIGINT PRIMARY KEY,
    producto_id BIGINT FOREIGN KEY,
    producto_talla_id BIGINT FOREIGN KEY,
    tipo_movimiento VARCHAR(50),
    cantidad INT,                     -- +5 o -2
    costo_unitario DECIMAL(10,2),     -- Costo de compra
    precio_unitario DECIMAL(10,2),    -- Precio de venta si aplica
    stock_anterior INT,               -- Saldo antes del movimiento
    stock_nuevo INT,                  -- Saldo resultante después del movimiento
    referencia_documento VARCHAR(100),-- 'Venta 0000123'
    user_id BIGINT FOREIGN KEY,       -- Usuario que ejecutó la acción
    motivo TEXT,
    created_at TIMESTAMP
);
```

### Invariante de Saldo:
En todo momento, para cada registro se cumple:
$$\text{stock\_nuevo} = \text{stock\_anterior} + \text{cantidad}$$
