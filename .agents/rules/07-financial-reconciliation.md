# 💰 CONCILIACIÓN FINANCIERA, ARQUEO DE CAJA Y REVERSIONES - REGLA #7

> **Prioridad:** MÁXIMA  
> **Siempre Activa:** Sí  
> **Propósito:** Blindar la cuadratura contable de caja diaria, evitar pérdidas ficticias y regular las anulaciones y reversiones en Eli Boutique.

---

## 🧮 1. ECUACIÓN INVARIANTE DE CAJA DIARIA

En cualquier instante del día, el balance de la caja abierta debe satisfacer estrictamente:

$$\text{Saldo Esperado en Caja} = \text{Monto Apertura} + \text{Ingresos Diarios} - \text{Egresos Diarios}$$

* **Ingreso Diario:** Suma acumulada de ventas cobradas (`Pagado`) del día.
* **Egreso Diario:** Suma acumulada de compras pagadas (`Pagada`) y retiros justificados del día.
* **Arqueo de Cierre:** Toda diferencia entre el dinero físico contado y el saldo esperado constituye un **Sobrante** o **Faltante** que debe quedar registrado y justificado.

---

## ↩️ 2. REGLAS ESTRICTAS DE REVERSIÓN Y ANULACIÓN DE VENTAS

Cuando se anula o elimina una venta, se debe seguir la siguiente bifurcación según su estado:

### Escenario A: Venta en estado `Pendiente`
1. **Stock por Talla:** Se restituyen las unidades a `ProductoTallaStock` bloqueando con `lockForUpdate()`.
2. **Caja Diaria:** ⚠️ **NUNCA modificar `cajas.ingresoDiario` ni `clientesHoy`**. La venta pendiente jamás ingresó dinero a la caja. Restar dinero en este caso generaría un desfalco ficticio en caja.
3. **Persistencia:** Si se invoca `destroy()`, solo se permite si la venta está en `Pendiente` y dentro de una transacción que devuelva el stock.

### Escenario B: Venta en estado `Pagado`
1. **Prohibición de Borrado:** ❌ **Queda terminantemente prohibido hacer `DELETE` físico (`destroy`) de una venta pagada**.
2. **Flujo Obligatorio de Anulación:** Debe transicionar al estado `Anulado` mediante `VentaService::anular()`.
3. **Stock por Talla:** Se restituyen las unidades a cada talla correspondiente.
4. **Caja Diaria:** Se descuenta el `montoTotal` de `cajas.ingresoDiario`, se decrementa `productosVendidos`, y si el cliente no tiene otra venta pagada en el día, se descuenta de `clientesHoy`.
5. **Caja Cerrada:** Si la venta pertenece a una caja de días anteriores ya cerrada, **no se puede alterar la caja histórica cerrada**. Se debe registrar un egreso/devolución en la caja abierta del día actual con motivo de auditoría.

---

## 📦 3. REGLAS DE REVERSIÓN EN COMPRAS A PROVEEDORES

1. Si una compra no ha sido recibida (`Borrador`, `Enviada`, `Cotizada`, `Aprobada`), su cancelación no altera stock ni caja.
2. Si una compra fue marcada como `Recibida`: Anularla exige descontar las cantidades previamente ingresadas a `ProductoTallaStock` (validando que exista stock suficiente para no dejar saldos negativos).
3. Si una compra ya fue `Pagada`: Requiere registrar la devolución del importe de egreso en caja y auditar la nota de crédito del proveedor.
