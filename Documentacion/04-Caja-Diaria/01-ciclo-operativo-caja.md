# 💵 CAJA DIARIA - CICLO OPERATIVO Y CUADRATURA

> **Módulo:** Caja Diaria y Arqueos  
> **Controlador:** `App\Http\Controllers\CajaController`  
> **Modelo Principal:** `App\Models\Caja`  
> **Tabla de Persistencia:** `cajas`

---

## ⏰ 1. EL CICLO DIARIO OPERATIVO EN TIENDA

La caja diaria en Eli Boutique controla el flujo de caja en efectivo y digital durante la jornada de ventas:

```text
[1. APERTURA (Mañana)]  ──► Verificación de caja previa cerrada.
                            Registro de apertura con fecha de hoy.
         │
         ▼
[2. OPERACIÓN DIARIA]   ──► Cobros de Ventas (+) ──► Incrementa ingresoDiario
                            Pagos de Compras (-) ──► Incrementa egresoDiario
                            Contador de Clientes ──► Clientes únicos atendidos
         │
         ▼
[3. ARQUEO (Cierre)]    ──► Conteo físico de efectivo vs. Saldo del Sistema.
         │
         ▼
[4. CIERRE (Noche)]     ──► Sellado de la caja con hora_cierre.
                            La caja queda inmutable.
```

---

## 🔓 2. APERTURA DE CAJA (`abrirCaja`)

* **Validación de Unicidad Diaria:** Solo se permite tener **una única caja abierta por día calendario** (`$table->date('fecha')->unique()`).
* Si el cajero intenta registrar ventas o compras sin haber abierto la caja previamente, el sistema bloquea la acción con:
  > *"No se puede registrar ventas. Debes abrir la caja primero."*
* Al abrir la caja, se inicializan en cero:
  * `clientesHoy = 0`
  * `productosVendidos = 0`
  * `ingresoDiario = 0.00`
  * `egresoDiario = 0.00`

---

## 🔒 3. CIERRE DE CAJA (`cerrarCaja`)

* Al finalizar el horario de atención en tienda:
  1. El cajero ingresa a la vista de Cajas.
  2. Presiona "Cerrar Caja".
  3. El sistema registra `hora_cierre = now()->format('H:i:s')`.
  4. A partir de ese momento, **ningún usuario puede agregar más ventas ni compras a esa jornada**.
  5. Se emite el reporte consolidado de cierre en PDF (`informeCaja.blade.php`).
