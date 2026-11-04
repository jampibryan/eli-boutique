# 🏗️ ARQUITECTURA LIMPIA Y SERVICIOS - REGLA #3

> **Prioridad:** ALTA  
> **Siempre Activa:** Sí  
> **Propósito:** Mantener arquitectura en capas desacoplada, testeable y consistente en Laravel

---

## 🏛️ ESTRUCTURA DE CAPAS (Obligatoria)

```text
app/Models/                       # Capa de Datos / Dominio (Eloquent)
├── Entidades de Negocio          # Venta, Compra, Producto, Caja, Pago, etc.
└── Relaciones y Scopes           # withTrashed(), belongsTo, hasMany

app/Services/                     # Capa de Aplicación / Servicios
├── VentaService.php              # Creación, actualización y anulación de ventas
├── CompraService.php             # Creación, actualización y recepción de compras
└── PagoService.php               # Registro de pagos para ventas y compras

app/Http/Controllers/             # Capa de Presentación (THIN CONTROLLERS)
├── VentaController.php           # Valida Request -> llama a Service -> responde Vista/JSON
└── CompraController.php          # Control de flujo de compra
```

---

## 🎯 PRINCIPIOS DE DISEÑO

### 1. Controladores Delgados (`Thin Controllers`)
- Los controladores solo deben:
  1. Validar la petición (`$request->validate(...)` o `FormRequest`).
  2. Invocar al servicio especializado correspondiente (`$ventaService->create(...)`).
  3. Retornar la respuesta (redirección con mensaje flash, vista o JSON).

### 2. Servicios Transaccionales (`Services`)
- Toda mutación que involucre más de un modelo (ej: Venta + VentaDetalle + Stock + Caja) DEBE ir dentro de `DB::transaction()`.
- Usar bloqueo pesimista (`lockForUpdate()`) al leer stocks o registros de caja para evitar condiciones de carrera (*race conditions*).

### 3. Tareas en Segundo Plano (`Jobs`)
- Procesos externos lentos (como la conexión SOAP a SUNAT) nunca deben bloquear la respuesta HTTP al usuario.
- Deben despacharse mediante Jobs encolados (`ShouldQueue`) con control de reintentos (`$tries = 3`) y espera exponencial (`$backoff`).
