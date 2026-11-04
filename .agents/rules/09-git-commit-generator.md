# 📝 GENERADOR DE COMMITS CONVENTIONAL - REGLA #9

> **Prioridad:** ALTA  
> **Siempre Activa:** Sí  
> **Trigger:** Cuando el desarrollador escriba en el chat la palabra `"commit"`, `"dame el commit"`, `"hacer commit"`, `"pásame el commit"` o similar.  
> **Propósito:** Generar instantáneamente el comando `git commit` perfecto, profesional, estandarizado según Conventional Commits, sin pleonasmos y con ~30 palabras de alto valor técnico.

---

## 🎯 OBJETIVO

Garantizar que todo commit en el proyecto **Eli Boutique** siga las mejores prácticas internacionales de **Conventional Commits**, sea descriptivo, conciso (**entre 25 y 35 palabras**), técnicamente riguroso y esté 100% libre de redundancias léxicas y pleonasmos.

---

## ⚡ REGLA DEL DISPARADOR (TRIGGER "commit")

Cuando el desarrollador envíe un mensaje que contenga la palabra `"commit"` (ej. `"commit"`, `"dame el commit"`, `"pásame el commit"`, `"qué commit pongo"`, etc.):

1. La IA **DEBE** inspeccionar los archivos modificados o creados en la sesión/tarea actual mediante `git status -s`.
2. La IA **DEBE** responder de forma prioritaria entregando el comando listo para copiar y pegar:
   ```bash
   git add .
   git commit -m "tipo(alcance): descripción técnica densa..."
   ```
3. La IA **NUNCA ejecuta el commit por sí misma** (Regla #1 de Guardrails: El control de versiones y publicación de ramas pertenece exclusivamente al desarrollador).

---

## 🚫 PROHIBICIÓN ESTRICTA DE PLEONASMOS Y REDUNDANCIAS

El tipo del commit (`feat`, `fix`, `refactor`, etc.) **ya define la acción**. Queda terminantemente prohibido repetir la acción en la descripción:

| Tipo | ❌ PALABRAS PROHIBIDAS (Pleonasmos) | ¿Por qué? | ✅ EJEMPLO CORRECTO EN ELI BOUTIQUE |
| :--- | :--- | :--- | :--- |
| **`feat:`** | `implementar`, `agregar`, `añadir`, `crear`, `incorporar`, `desarrollar`, `nuevo`, `nueva` | `feat` ya significa que es una nueva funcionalidad. | `feat(pos): validación concurrente de stock por talla mediante lockForUpdate y desglose automático de base imponible e IGV en carrito` |
| **`fix:`** | `arreglar`, `reparar`, `corregir`, `solucionar`, `parchear`, `quitar bug`, `error en` | `fix` ya significa que se está reparando un fallo. | `fix(caja): reversión de ingresos solo para ventas pagadas y preservación de saldos ante cancelaciones en estado pendiente` |
| **`refactor:`** | `modificar`, `cambiar`, `refactorizar`, `reestructurar`, `actualizar`, `editar`, `ajustar` | `refactor` ya significa que se está reestructurando código. | `refactor(pago): persistencia atómica de RUC y razón social dentro de la transacción del servicio eliminando mutaciones externas` |
| **`perf:`** | `optimizar`, `mejorar rendimiento`, `acelerar`, `hacer más rápido` | `perf` ya significa mejora de desempeño. | `perf(dashboard): sincronización directa de métricas financieras con la caja diaria abierta y memoización de consultas en caché local` |
| **`chore:`** | `hacer tarea`, `mantenimiento de`, `limpieza de` | `chore` ya indica tareas accesorias de entorno o repo. | `chore(agents): reglas de SSOT, conciliación de arqueo y generador convencional de commits adaptado al dominio retail` |

---

## 📐 ESTRUCTURA Y LONGITUD (~30 PALABRAS)

- **Longitud esperada:** Entre **25 y 35 palabras** de forma fluida y coherente, sin palabras de relleno.
- **Formato canónico:**
  ```bash
  git commit -m "tipo(alcance): descripción técnica densa que detalle los módulos involucrados, las garantías de integridad alcanzadas y el impacto directo en la trazabilidad del sistema"
  ```
- **Tiempo verbal:** Modo indicativo presente o sustantivos de acción en español neutro (ej. *persistencia atómica de comprobantes*, *reversión segura de existencias*, *sincronización con la fuente única de verdad*).

---

## 🏷️ TABLA DE ALCANCES (SCOPES) EN ELI BOUTIQUE

| Alcance (Scope) | Módulo o Subsistema de Eli Boutique |
| :--- | :--- |
| `pos` / `ventas` | Punto de venta, carrito de sesión, reserva de prendas, cobro de ventas |
| `compras` | Ciclo de compras a proveedores (`Borrador` $\rightarrow$ `Enviada` $\rightarrow$ `Cotizada` $\rightarrow$ `Aprobada` $\rightarrow$ `Recibida` $\rightarrow$ `Pagada`) |
| `caja` | Apertura, cierre, arqueo, ingresos diarios, egresos diarios y conciliación de turnos |
| `inventario` | Matriz de tallas (`ProductoTallaStock`), control de quiebres, variantes de prendas |
| `excel` | Importación y exportación masiva OpenXML con logotipo corporativo |
| `comprobantes` | Renderizado y emisión de Boletas (DNI), Facturas (RUC) y reportes en DomPDF |
| `clientes` / `proveedores` | Catálogos maestros, validación regex de DNI (8 dígitos) y RUC (11 dígitos) |
| `dashboard` / `reportes` | Métricas ejecutivas, gráficas temporales con Chart.js, consolidados mensuales |
| `security` | Autenticación, roles Spatie (`administrador`, `gerente`, `vendedor`), Sanctum |
| `agents` | Manifiesto maestro, reglas activas y habilidades especializadas en `.agents` |
