# 🔒 SEGURIDAD - REGLA #2

> **Prioridad:** ALTA  
> **Siempre Activa:** Sí  
> **Propósito:** Prevenir vulnerabilidades, proteger autenticación y validar identidades fiscales

---

## 🔑 CREDENCIALES Y VARIABLES DE ENTORNO

### ❌ NUNCA hardcodear en código:
```php
// ❌ INCORRECTO
$dbPassword = 'mi_clave_secreta';
$apiKey = '123456789abcdef';
```

### ✅ SIEMPRE usar configuración y variables de entorno:
```php
// ✅ CORRECTO
config('database.connections.mysql.password')
config('services.mail.password')
env('DB_PASSWORD')
```

---

## 🛡️ PREVENCIÓN DE INYECCIÓN SQL (SQL INJECTION)

### ❌ NUNCA concatenar valores en consultas crudas:
```php
// ❌ PROHIBIDO
DB::select("SELECT * FROM productos WHERE descripcionP = '$nombre'");
```

### ✅ SIEMPRE usar bindings o Eloquent:
```php
// ✅ CORRECTO
DB::select("SELECT * FROM productos WHERE descripcionP = ?", [$nombre]);

// ✅ CORRECTO: Eloquent parametriza automáticamente
Producto::where('descripcionP', $nombre)->get();
```

---

## 🪪 VALIDACIÓN ESTRICTA DE DOCUMENTOS PERUANOS (DNI Y RUC)

Para garantizar la integridad tributaria y evitar rechazos por parte de SUNAT:

1. **DNI (Personas Naturales):**
   - Exactamente 8 dígitos numéricos: `regex:/^[0-9]{8}$/`.
2. **RUC (Personas Jurídicas / Facturas):**
   - Exactamente 11 dígitos numéricos: `regex:/^[0-9]{11}$/`.
   - Si se emite Factura (`01`), el RUC y la Razón Social son **obligatorios** a nivel de la transacción.

---

## 🌐 PROTECCIÓN DE APIS (SANCTUM & CORS)

- Todos los endpoints en `routes/api.php` que devuelven o modifican información sensible deben exigir `auth:sanctum`.
- Las únicas excepciones autorizadas son endpoints de solo lectura para modelos de predicción autorizados (`/api/obtener-datos-ventas`).
- Configurar adecuadamente `CORS_ALLOWED_ORIGINS` y `SANCTUM_STATEFUL_DOMAINS` al conectar clientes móviles (Flutter) o dashboards (Streamlit).
