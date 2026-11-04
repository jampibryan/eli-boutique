# 🔒 RESERVA CONCURRENTE Y CONTROL DE STOCK POR TALLAS

> **Módulo:** Ventas / Inventario  
> **Patrón Técnico:** Bloqueo Pesimista en Base de Datos (`SELECT ... FOR UPDATE`)  
> **Ubicación:** `App\Services\VentaService::create()` y `update()`

---

## 🛑 1. EL PROBLEMA DE LA CONCURRENCIA EN TIENDA

Imagina que dos colaboradoras en Eli Boutique atienden a dos clientas distintas al mismo segundo:
* En mostrador A, una clienta quiere llevar el último vestido rojo en **Talla S** (Stock = 1).
* En mostrador B, otra clienta también solicita el mismo vestido rojo en **Talla S**.
* Si el sistema no tuviera control concurrente, ambas colaboradoras pulsarían "Cobrar" y el sistema vendería 2 vestidos cuando físicamente solo existe 1 en vitrina (**Quiebre de inventario por condición de carrera**).

---

## 🛡️ 2. SOLUCIÓN TÉCNICA: BLOQUEO PESIMISTA (`lockForUpdate`)

Eli Boutique resuelve este problema serializando las peticiones a nivel de fila en la base de datos:

```php
$tallaStock = ProductoTallaStock::where('producto_id', $productoData['id'])
    ->where('producto_talla_id', $productoData['talla_id'])
    ->lockForUpdate()
    ->first();
```

### ¿Cómo opera paso a paso?
1. Cuando la colaboradora A presiona "Cobrar", el motor de base de datos coloca un candado exclusivo sobre el registro exacto de esa prenda y esa talla.
2. Si la colaboradora B intenta acceder a ese mismo registro en ese instante, su solicitud se encola en milisegundos a la espera de que termine la transacción A.
3. Al confirmarse la venta A, el stock pasa de `1` a `0`.
4. Cuando la solicitud B se ejecuta, lee el nuevo stock (`0`), detecta que es inferior a la cantidad solicitada (`1`) y aborta de inmediato con una `ValidationException`:
   > *"Stock insuficiente para Vestido Fiesta Rojo talla S. Stock disponible: 0, solicitado: 1."*
5. Se previene la sobreventa con 100% de fiabilidad matemática y transaccional.

---

## 📐 3. NORMALIZACIÓN DE LÍNEAS DE CARRITO

Si un usuario agrega al carrito la misma prenda en la misma talla en dos momentos distintos (por ejemplo, 1 blusa talla M y luego otra blusa talla M):
* `VentaService` cuenta con la rutina `normalizarLineas()`.
* Agrupa las cantidades antes de consultar a la base de datos:
  $$\text{Cantidad Total Solicitada} = \sum \text{Líneas Idénticas}$$
* De este modo, solo se ejecuta un único bloqueo por combinación `producto_id:producto_talla_id`.
