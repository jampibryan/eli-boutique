# ⚖️ MATRIZ DE DECISIONES - REGLA #5

> **Prioridad:** MEDIA  
> **Siempre Activa:** Sí  
> **Propósito:** Resolver conflictos y priorizar soluciones técnicas en Eli Boutique

---

## 🧭 JERARQUÍA DE PRIORIDADES

Cuando te enfrentes a múltiples alternativas de solución, aplica estrictamente esta jerarquía:

```text
1️⃣ SEGURIDAD          ➔ Proteger datos de usuarios, credenciales, permisos y evitar SQL injection
2️⃣ CONSISTENCIA FINANCIERA ➔ Integridad estricta en Caja Diaria, stock por tallas y comprobantes PDF
3️⃣ SIMPLICIDAD        ➔ Código legible, directo y mantenible sin sobre-ingeniería (KISS)
4️⃣ MANTENIBILIDAD     ➔ Arquitectura modular desacoplada en Services, reutilización (DRY / SOLID)
5️⃣ RENDIMIENTO        ➔ Optimización de consultas con índices sin sacrificar claridad
```

---

## 💡 EJEMPLOS DE APLICACIÓN

### Caso 1: Actualización de stock e importes de caja
* ✅ **DECISIÓN CORRECTA (Consistencia > Simplicidad):** Ejecutar dentro de `DB::transaction()` con `lockForUpdate()`.
* ❌ **Opción rechazada:** Modificar modelos sueltos en el controlador sin transacción ni bloqueo.
* *Razón:* Evita inconsistencias ante concurrencia y garantiza la integridad financiera.

### Caso 2: Emisión de Comprobantes PDF
* ✅ **DECISIÓN CORRECTA (Simplicidad & Mantenibilidad):** Generar Boleta o Factura en PDF limpio bajo demanda a partir de los datos transaccionales inmutables.
* ❌ **Opción rechazada:** Depender de Web Services SOAP externos o servicios tributarios de terceros no requeridos.
* *Razón:* Mantiene la autonomía y disponibilidad 100% garantizada de la boutique sin depender de servicios externos.

### Caso 3: Maestro eliminado (Producto, Cliente o Proveedor)
* ✅ **DECISIÓN CORRECTA (Corrección > Simplicidad):** Usar `->withTrashed()` en las relaciones transaccionales.
* ❌ **Opción rechazada:** Dejar que retorne `null` o eliminar físicamente (`HARD DELETE`) de la base de datos.
* *Razón:* Los comprobantes y reportes pasados deben ser inmutables y preservarse legalmente.
