# 💸 EGRESOS Y REVERSIONES SIN CORROMPER CAJAS CERRADAS

> **Módulo:** Caja Diaria  
> **Concepto Crítico:** Inmutabilidad Contable Histórica  
> **Regla de Negocio:** Regla #7 - Sección 2

---

## 🚫 1. EL GRAVE ERROR DE MODIFICAR CAJAS CERRADAS

Considera este escenario común:
1. Una clienta compró un vestido el día **Viernes** por S/ 150.00.
2. La caja del Viernes se cerró esa misma noche con balance perfecto.
3. El día **Martes de la semana siguiente**, la clienta regresa y solicita la anulación o devolución del dinero.

### ❌ Práctica Prohibida (Corrupción de Datos):
Buscar la venta del Viernes y restar S/ 150.00 de la caja del Viernes.
* **Consecuencia:** Se altera una caja histórica ya cerrada y auditada. El informe de cierre que se firmó el Viernes ahora discrepa con la base de datos (**Fraude o corrupción contable**).

---

## ✅ 2. LA SOLUCIÓN PROFESIONAL: EGRESO EN LA CAJA ACTUAL

Para preservar la inmutabilidad histórica:
* **Venta del mismo día (Caja Abierta):** Se anula directamente la venta; la caja de hoy decrementa su `ingresoDiario` porque el dinero aún está en la gaveta.
* **Venta de días pasados (Caja Cerrada):**
  1. La caja del Viernes se mantiene **intacta al 100%**.
  2. La prenda se restituye al stock y al Kardex con el evento `REVERSION_VENTA`.
  3. La salida de dinero se procesa como un **Egreso Extraordinario por Devolución de Venta** en la **Caja Abierta de Hoy (Martes)**.
  4. De este modo, la caja del Martes refleja con exactitud por qué faltan S/ 150.00 al final del turno.
