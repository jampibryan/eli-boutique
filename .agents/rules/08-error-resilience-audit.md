# 🛡️ RESILIENCIA, MANEJO DE EXCEPCIONES Y AUDITORÍA - REGLA #8

> **Prioridad:** ALTA  
> **Siempre Activa:** Sí  
> **Propósito:** Garantizar atomicidad de fallos, cero pérdida silenciosa de errores y trazabilidad forense de operaciones sensibles.

---

## 🚫 CERO SILENCIAMIENTO DE EXCEPCIONES

### ❌ Práctica Prohibida:
```php
// ❌ PROHIBIDO: Silenciar errores y ocultar fallos transaccionales
try {
    $tallaStock->decrement('stock', $cantidad);
} catch (\Exception $e) {
    // Bloque vacío o ignorar el error
}
```

### ✅ Práctica Obligatoria:
Cualquier anomalía debe propagarse para que `DB::transaction()` ejecute un **Rollback Atómico Completo** y retorne una excepción clara:
```php
// ✅ CORRECTO: Lanzar ValidationException o permitir que la transacción falle limpiamente
if ($tallaStock->stock < $cantidad) {
    throw ValidationException::withMessages([
        'stock' => "Stock insuficiente para {$producto->descripcionP} en la talla solicitada.",
    ]);
}
```

---

## 📋 REGISTRO DE AUDITORÍA FORENSE (LOGGING)

Toda operación que involucre:
1. **Anulación de Venta Pagada**
2. **Recepción o Cancelación de Compra Recibida**
3. **Cierre de Caja con Descuadre (Diferencia de Arqueo)**
4. **Ajuste Forzado de Stock por Talla**

DEBE registrar una entrada en el log diario (`Log::info` o `Log::warning`) conteniendo:
- **Usuario/Colaborador:** ID y nombre del operador responsable.
- **Fecha y Hora:** Timestamp en hora local peruana (`now()->toDateTimeString()`).
- **Entidad afectada:** ID de Venta, Compra o Caja.
- **Valores previos y nuevos:** Montos anteriores, montos nuevos y motivo documentado.
