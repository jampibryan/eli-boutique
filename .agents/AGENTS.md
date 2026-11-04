# AGENTE ELI BOUTIQUE - MANIFIESTO MAESTRO

> **Versión:** 1.0.0  
> **Fecha:** 2026-09-19  
> **Motor IA:** Google Gemini 3.8 Flash (High)  
> **IDE:** Antigravity IDE  
> **Enfoque:** Master Orchestrator (Trazabilidad 360° Retail ERP - Cero Silos)

---

## 🎯 PROPÓSITO

Este manifiesto define el comportamiento, alcance, visión transversal y restricciones del agente de IA para el sistema de gestión empresarial **Eli Boutique**. Todas las directrices, reglas de negocio y habilidades están organizadas para maximizar la productividad, garantizar la **Fuente Única de Verdad (`Single Source of Truth`)**, asegurar la **consistencia financiera en Caja Diaria e Inventario por Tallas** y la **emisión ordenada de Comprobantes PDF internos**.

---

## 🚫 PROHIBICIONES Y PERMISOS DE EJECUCIÓN

| # | Acción | Regla | Razón / Condición |
| :---: | :--- | :---: | :--- |
| 1 | **Comandos Git destructivos** (`git push`, `git checkout .`, `git reset --hard`) | ❌ **PROHIBIDO** | Previene pérdida accidental de código. El desarrollador controla las ramas y commits principales. |
| 2 | **Lógica de negocio o consultas SQL complejas en controladores web** | ❌ **PROHIBIDO** | Violación estricta de arquitectura (Thin Controllers). Toda lógica va en `app/Services/`. |
| 3 | **Operaciones de inventario y caja fuera de transacciones de base de datos** | ❌ **PROHIBIDO** | Todo cambio de stock y movimiento de dinero DEBE ejecutarse dentro de `DB::transaction()`. |
| 4 | **Modificación destructiva de tablas de producción (`DROP TABLE`, `TRUNCATE`)** | ❌ **PROHIBIDO** | Protección contra pérdida irreversible de datos. |
| 5 | **Ignorar el huso horario peruano (PET UTC-5)** | ❌ **PROHIBIDO** | Todo registro de ventas, compras, caja y comprobantes debe sincronizarse en hora local de Perú. |
| 6 | **Archivos temporales o scripts de prueba sueltos en raíz** | ❌ **PROHIBIDO** | Mantener el repositorio limpio. Pruebas efímeras en memoria o scratch del IDE. |

---

## 🌐 VISIBILIDAD TRANSVERSAL GLOBAL (360° RETAIL ERP - CERO SILOS)

> **Regla de Oro:** Queda **terminantemente prohibido** auto-restringir el análisis a un archivo aislado sin validar sus impactos en los módulos dependientes.

1. **Visión 360° de la Trazabilidad:**
   - Todo cambio en un módulo debe auditarse en sus 4 ejes de propagación:
   ```text
   [1. Catálogo / Tallas] ➔ [2. Compras / Proveedores] ➔ [3. Inventario Stock Tallas]
                                                               ⬇
                                              [4. Carrito / Ventas POS / Caja Diaria / Reportes]
   ```
2. **Cruce de Fuentes de Verdad:**
   - Si se consulta una Venta, se debe verificar su stock descontado en `producto_talla_stock`, su impacto en `cajas.ingresoDiario` y su pago en `pagos`.
   - Si se consulta una Compra, se debe rastrear su ciclo de estados (`Borrador` ➔ `Enviada` ➔ `Cotizada` ➔ `Aprobada` ➔ `Recibida` ➔ `Pagada`), el incremento de stock por talla al recibir y el registro de egreso en caja al pagar.

---

## 📁 ALCANCE DEL PROYECTO

### Estructura de Directorios

```text
c:\Disco D\PROYECTOS\eli-boutique\
├── app/
│   ├── Http/Controllers/       # Thin Controllers (delegan a Services)
│   │   └── Api/                # Endpoints REST para clientes externos (Sanctum)
│   ├── Models/                 # Modelos Eloquent con relaciones y scopes
│   └── Services/               # Lógica de negocio (VentaService, CompraService, PagoService)
├── database/
│   ├── migrations/             # Migraciones DDL con llaves foráneas e índices
│   └── seeders/                # Datos semilla de catálogos y roles
├── resources/
│   └── views/                  # Vistas Blade con AdminLTE, Tailwind y Bootstrap
├── routes/
│   ├── web.php                 # Rutas de backoffice con middleware auth y permisos
│   └── api.php                 # API protegida con Sanctum
└── .agents/                    # Reglas, manifiestos y habilidades de IA
```

### Entorno y Tecnologías
* **Backend:** PHP 8.2 / 8.3 con Laravel 10
* **Frontend:** Blade, Livewire 3, Tailwind CSS, Bootstrap 5, Chart.js
* **Base de Datos:** MySQL / MariaDB (en Docker / Sail o local)
* **Comprobantes:** Emisión y descarga en PDF interno (Boletas y Facturas)
* **Autenticación:** Laravel Jetstream con Sanctum y Spatie Permission
* **Zona Horaria:** **PET (America/Lima - UTC-5)**

---

## 📋 REGLAS ACTIVAS

* [01-guardrails.md](file:///c:/Disco%20D/PROYECTOS/eli-boutique/.agents/rules/01-guardrails.md): Restricciones y límites de ejecución.
* [02-security.md](file:///c:/Disco%20D/PROYECTOS/eli-boutique/.agents/rules/02-security.md): Seguridad, autenticación y validación estricta de DNI/RUC.
* [03-architecture.md](file:///c:/Disco%20D/PROYECTOS/eli-boutique/.agents/rules/03-architecture.md): Arquitectura en capas en Laravel (Services, Thin Controllers).
* [04-database.md](file:///c:/Disco%20D/PROYECTOS/eli-boutique/.agents/rules/04-database.md): Base de datos MySQL, snapshots, soft-deletes y zona horaria.
* [05-decision-matrix.md](file:///c:/Disco%20D/PROYECTOS/eli-boutique/.agents/rules/05-decision-matrix.md): Jerarquía de toma de decisiones técnicas.
* [06-ssot-state-machine.md](file:///c:/Disco%20D/PROYECTOS/eli-boutique/.agents/rules/06-ssot-state-machine.md): Fuente Única de Verdad (SSOT) y máquina de estados transaccionales determinista.
* [07-financial-reconciliation.md](file:///c:/Disco%20D/PROYECTOS/eli-boutique/.agents/rules/07-financial-reconciliation.md): Conciliación financiera, cuadratura de caja diaria y reglas de reversión de ventas/compras.
* [08-error-resilience-audit.md](file:///c:/Disco%20D/PROYECTOS/eli-boutique/.agents/rules/08-error-resilience-audit.md): Cero silenciamiento de errores, rollback transaccional atómico y logging de auditoría.
* [09-git-commit-generator.md](file:///c:/Disco%20D/PROYECTOS/eli-boutique/.agents/rules/09-git-commit-generator.md): Generador de commits Conventional (~30 palabras, sin pleonasmos, disparador "commit").

---

## 🧠 HABILIDADES ESPECIALIZADAS

* [cross-module-traceability](file:///c:/Disco%20D/PROYECTOS/eli-boutique/.agents/skills/cross-module-traceability/SKILL.md): Protocolo de verificación y auditoría de la trazabilidad transversal de los 4 ejes de negocio.
* [db-design-audit](file:///c:/Disco%20D/PROYECTOS/eli-boutique/.agents/skills/db-design-audit/SKILL.md): Auditoría de relaciones, normalización e integridad referencial en base de datos.
* [project-readme](file:///c:/Disco%20D/PROYECTOS/eli-boutique/.agents/skills/project-readme/SKILL.md): Mantenimiento de documentación viva sin secretos.
* [responsive-ui](file:///c:/Disco%20D/PROYECTOS/eli-boutique/.agents/skills/responsive-ui/SKILL.md): Guía de diseño responsivo para punto de venta y administración.
* [pos-cash-reconciliation](file:///c:/Disco%20D/PROYECTOS/eli-boutique/.agents/skills/pos-cash-reconciliation/SKILL.md): Protocolo operativo para cuadratura de caja diaria, arqueo y reversiones seguras.
* [receipt-pdf-generation](file:///c:/Disco%20D/PROYECTOS/eli-boutique/.agents/skills/receipt-pdf-generation/SKILL.md): Renderizado y descarga de boletas, facturas y reportes ejecutivos en PDF.
* [inventory-size-matrix](file:///c:/Disco%20D/PROYECTOS/eli-boutique/.agents/skills/inventory-size-matrix/SKILL.md): Administración de matriz de tallas, variantes, stock físico e importaciones Excel.
