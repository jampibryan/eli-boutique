# 🗄️ BASE DE DATOS Y PERSISTENCIA - REGLA #4

> **Prioridad:** ALTA  
> **Siempre Activa:** Sí  
> **Propósito:** Garantizar integridad referencial, consistencia de caja y preservación histórica

---

## 🇵🇪 ZONA HORARIA PERUANA (PET - UTC-5)

### REGLA OBLIGATORIA:
**TODA fecha y hora registrada en el sistema DEBE estar en huso horario peruano (PET - UTC-5).**
- Configuración en `config/app.php`: `'timezone' => 'America/Lima'`.
- En código: Utilizar `now()` o `today()`.

---

## 📜 PRESERVACIÓN HISTÓRICA Y SOFTDELETES

### 1. `withTrashed()` Obligatorio en Transacciones Pasadas
Los modelos maestros (`Producto`, `Cliente`, `Proveedor`) utilizan eliminación lógica (`SoftDeletes`).
- **Regla:** En todas las relaciones hijas de transacciones (`VentaDetalle`, `CompraDetalle`, `Venta`, `Compra`), la relación hacia el maestro DEBE incluir `->withTrashed()`.
```php
// ✅ CORRECTO en VentaDetalle.php
public function producto()
{
    return $this->belongsTo(Producto::class)->withTrashed();
}
```
*Razón:* Si un producto o proveedor se desactiva hoy, los reportes contables y los comprobantes fiscales del pasado no deben romper ni mostrar valores nulos.

### 2. Snapshots Transaccionales
Toda transacción cerrada congela copias de sus valores clave (precio unitario histórico, subtotal, IGV, RUC, Razón Social) en las tablas de detalles y ventas, independientemente de que el catálogo de productos cambie de precio mañana.

---

## 💰 CONSISTENCIA EN CAJA DIARIA

1. **Venta Pendiente:** Al crearse una venta, esta inicia en estado `Pendiente`. NO se incrementa `cajas.ingresoDiario` ni `clientesHoy`.
2. **Venta Pagada:** Al pagarse, pasa a `Pagado`, sumando `montoTotal` a `ingresoDiario` de la caja abierta correspondiente.
3. **Anulación de Venta:**
   - Si la venta estaba `Pagada`: Se devuelve el stock por talla y se resta su importe de `cajas.ingresoDiario`.
   - Si la venta estaba `Pendiente`: Se devuelve el stock por talla, pero **NUNCA** se resta de `ingresoDiario` de la caja (porque nunca ingresó).
4. **Pago de Compras:**
   - Pagar a un proveedor requiere obligatoriamente que la caja diaria esté abierta para registrar el `egresoDiario`.
