# 🛡️ GUARDIANES GENERALES - REGLA #1

> **Prioridad:** MÁXIMA  
> **Siempre Activa:** Sí  
> **Propósito:** Definir límites de ejecución, seguridad y no regresión en Eli Boutique

---

## 🎯 ALCANCE DEL PROYECTO

### Backend Oficial
* **Framework:** Laravel 10 / PHP 8.2+
* **Estructura:** Modelos en `app/Models/`, Lógica de negocio en `app/Services/`, Controladores delgados en `app/Http/Controllers/`.

### Persistencia y Migraciones
* **Motor:** MySQL / MariaDB.
* **Migraciones oficiales:** `database/migrations/`.
* **Seeders oficiales:** `database/seeders/`.

---

## 🚫 PROHIBICIONES ABSOLUTAS DE EJECUCIÓN

La IA NUNCA debe ejecutar:

### 1. Comandos Git Destructivos o Invasivos
```bash
❌ git push
❌ git reset --hard
❌ git clean -fd
❌ git checkout .
```
*El control de versiones principal y publicación de ramas pertenece al desarrollador.*

### 2. Operaciones Financieras e Inventario sin Transacción
```php
// ❌ PROHIBIDO: Operaciones críticas sin transacción
$tallaStock->decrement('stock', $cantidad);
$caja->increment('ingresoDiario', $total);

// ✅ OBLIGATORIO: Siempre encapsulado en transacción
DB::transaction(function () use (...) {
    $tallaStock = ProductoTallaStock::where(...)->lockForUpdate()->first();
    $tallaStock->decrement('stock', $cantidad);
    ...
});
```

### 3. Modificaciones Destructivas de Base de Datos
* ❌ Prohibido ejecutar `php artisan migrate:fresh` o `migrate:reset` en entornos compartidos o producción sin instrucción explícita.
* ❌ Prohibido emitir sentencias `DROP TABLE` o `DROP COLUMN` directamente.

### 4. Thin Controllers (Controladores Delgados)
* ❌ Prohibido escribir lógica transaccional de ventas, compras o cálculos de stock directamente dentro de `VentaController` o `CompraController`.
* ✅ Toda lógica orquestada debe delegarse a `VentaService`, `CompraService` o `PagoService`.
