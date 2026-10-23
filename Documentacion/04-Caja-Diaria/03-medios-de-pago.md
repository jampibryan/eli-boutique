# 📱 GESTIÓN DE MEDIOS DE PAGO MULTICANAL

> **Módulo:** Caja Diaria / Pagos  
> **Tabla:** `pagos`  
> **Campos:** `metodo_pago`, `referencia_pago`, `importe`, `vuelto`

---

## 💳 1. EL RETO DE LOS PAGOS DIGITALES EN TIENDA

En el Perú actual, más del 60% de las ventas en tiendas de moda se cobran mediante:
* **Billeteras Digitales:** Yape, Plin.
* **Tarjetas:** Tarjetas de Débito/Crédito (Visa, Mastercard a través de POS Izipay, Niubiz o Culqi).
* **Transferencias Bancarias:** BCP, BBVA, Interbank.
* **Efectivo Físico:** Billetes y monedas en la gaveta.

Si un sistema suma todos estos medios en una sola bolsa de dinero en efectivo, el cajero **nunca podrá cuadrar físicamente su gaveta** al final del día.

---

## 🏷️ 2. DISCRIMINACIÓN DE MEDIOS EN ELI BOUTIQUE

La tabla `pagos` clasifica cada transacción mediante:

| Medio de Pago (`metodo_pago`) | ¿Afecta Gaveta Física? | Validación / Respaldo |
| :--- | :---: | :--- |
| `efectivo` | **SÍ** | Se cuenta físicamente billete por billete. Permite cálculo de `vuelto`. |
| `yape` | **NO** | Se verifica en la app móvil. Puede registrar número de operación en `referencia_pago`. |
| `plin` | **NO** | Se verifica en la app móvil del banco. |
| `tarjeta` | **NO** | Se verifica contra el voucher impreso del terminal POS inalámbrico. |
| `transferencia` | **NO** | Se verifica con la confirmación de abono en cuenta empresarial. |

---

## 🗂️ 3. CUADRATURA SEPARADA EN EL ARQUEO

Al momento del cierre:
$$\text{Efectivo en Gaveta} = \text{Apertura} + \text{Ventas en Efectivo} - \text{Egresos en Efectivo}$$

$$\text{Total en Cuentas Digitales} = \text{Ventas Yape} + \text{Ventas Plin} + \text{Ventas Tarjetas}$$

$$\text{Ingreso Total de la Jornada} = \text{Efectivo} + \text{Digital}$$

De esta manera, el cajero solo responde por los billetes reales que tiene frente a él, eliminando confusiones entre dinero bancarizado y dinero físico.
