# ⚠️ CONTROL DE MERMAS, DEFECTOS Y AJUSTES DE INVENTARIO

> **Módulo:** Inventario  
> **Conceptos:** Conteo Ciego, Mermas Textiles, Regularizaciones

---

## 🧵 1. MERMAS EN EL RETAIL TEXTIL

En una boutique de moda, las discrepancias entre el stock de sistema y el stock físico real suelen ocurrir por:
* **Prendas con fallas de confección:** Botones desprendidos, costuras abiertas o telas rasgadas.
* **Manchas durante el probador:** Clientas que prueban prendas con maquillaje.
* **Pérdidas o diferencias de conteo:** Errores humanos de despacho.

---

## 🛠️ 2. PROTOCOLO DE AJUSTE CON AUDITORÍA

Cuando se realiza un inventario físico en tienda y se detecta una diferencia:

1. **Identificación de la Talla Específica:**
   * El ajuste jamás se hace "al producto en general". Se debe identificar qué talla específica está descuadrada (ej. Talla M tiene 3 unidades en sistema, pero físicamente solo hay 2).
2. **Registro de Movimiento en Kardex:**
   * Tipo: `AJUSTE_INVENTARIO`.
   * Cantidad: `-1` (salida por merma/ajuste).
   * Motivo obligatorio: *"Prenda manchada en probador - dada de baja según Acta 014"*.
3. **Usuario Responsable:**
   * El Kardex guarda el `user_id` del administrador que autorizó la baja, evitando ajustes anónimos.
