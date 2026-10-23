# 🛡️ AUDITORÍA FORENSE Y TRAZABILIDAD EN LOGS

> **Módulo:** Auditoría / Resiliencia  
> **Regla de Negocio:** Regla #8 de Eli Boutique ERP  
> **Destino:** `storage/logs/laravel.log` (Canal `stack`)

---

## 📋 1. ESTÁNDAR OBLIGATORIO DE LOGGING

En Eli Boutique, ninguna operación sensible que altere dinero o mercadería puede ejecutarse de forma silenciosa.

Toda acción que involucre:
1. **Cobro o creación de Venta**
2. **Anulación de Venta Pagada**
3. **Recepción de Compra física a Almacén**
4. **Pago de Compra a Proveedor**
5. **Apertura o Cierre de Caja**

DEBE generar una entrada de auditoría con la siguiente estructura JSON contextual:

```php
Log::info('Pago de venta registrado exitosamente (SSOT Pagado)', [
    'venta_id' => $venta->id,
    'codigo_venta' => $venta->codigoVenta,
    'caja_id' => $cajaHoy->id,
    'importe' => $validated['importe'],
    'vuelto' => $vuelto,
    'monto_total' => $venta->montoTotal,
    'es_factura' => !empty($validated['ruc_factura']),
    'usuario_id' => auth()->id(),
    'timestamp_pet' => now()->toDateTimeString(),
]);
```

---

## ⏰ 2. SINCRONIZACIÓN EN HUSO HORARIO PERUANO (PET)

* Todas las marcas de auditoría se registran en hora local del Perú (**America/Lima - UTC-5**).
* Esto garantiza que los logs del servidor coincidan al segundo con los tickets impresos, cámaras de seguridad de la tienda física y vouchers bancarios de Yape/POS.
