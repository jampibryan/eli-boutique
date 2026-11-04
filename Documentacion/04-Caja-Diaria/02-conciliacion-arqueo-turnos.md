# 🧮 CONCILIACIÓN FINANCIERA Y ARQUEO DE CAJA

> **Módulo:** Caja Diaria  
> **Conceptos:** Ecuación Invariante, Saldo Esperado, Faltantes y Sobrantes  
> **Regla de Negocio:** Regla #7 de Eli Boutique ERP

---

## ⚖️ 1. LA ECUACIÓN INVARIANTE DE CAJA

En cualquier instante del día, el saldo contable que debe existir en tienda satisface de manera estricta la siguiente fórmula:

$$\text{Saldo Esperado en Caja} = \text{Monto Apertura} + \text{Ingresos Diarios} - \text{Egresos Diarios}$$

Donde:
* **Ingresos Diarios (`cajas.ingresoDiario`):** Sumatoria de todas las ventas cobradas exitosamente en estado `Pagado`.
* **Egresos Diarios (`cajas.egresoDiario`):** Sumatoria de todos los pagos realizados a proveedores por órdenes de compra en estado `Pagada` y retiros autorizados.

---

## 🔍 2. PROTOCOLO DE CONCILIACIÓN CON LA TABLA PAGOS

Para auditar que el acumulador de caja no sufra desincronización con las boletas y facturas emitidas, se ejecuta el cruce canónico de datos:

### A. Conciliación de Ventas (Ingresos):
$$\sum_{\text{ventas hoy}} \text{pagos.importe} \equiv \text{cajas.ingresoDiario}$$

### B. Conciliación de Compras (Egresos):
$$\sum_{\text{compras hoy}} \text{pagos.importe} \equiv \text{cajas.egresoDiario}$$

---

## 💰 3. GESTIÓN DE DESCUADRES (ARQUEO FÍSICO)

Durante el arqueo vespertino, el cajero cuenta físicamente el dinero en billetes y monedas ($\text{Monto Físico}$):

$$\Delta = \text{Monto Físico Contado} - \text{Saldo Esperado en Sistema}$$

* **Si $\Delta = 0$:** **Caja Cuadrada**. Conteo perfecto.
* **Si $\Delta > 0$:** **Sobrante de Caja**. Dinero en exceso (ej. propinas o cobros no registrados). Queda registrado en observaciones.
* **Si $\Delta < 0$:** **Faltante de Caja**. Fuga o entrega errónea de vuelto al cliente. El colaborador a cargo debe justificar la diferencia ante la administración.
