# 🔐 CONTROL DE ACCESO, ROLES Y PERMISOS (SPATIE)

> **Módulo:** Seguridad y Autenticación  
> **Paquete:** `spatie/laravel-permission`  
> **Middleware:** `permission:...`, `role:...`

---

## 👥 1. MATRIZ DE ROLES Y PERMISOS

Eli Boutique segmenta las responsabilidades del personal mediante una matriz de permisos granular:

| Permiso | Descripción | Rol Administrador | Rol Cajero / Vendedor | Rol Almacenero |
| :--- | :--- | :---: | :---: | :---: |
| `crear ventas` | Registrar pedidos y ventas en mostrador. | ✅ | ✅ | ❌ |
| `gestionar ventas` | Ver historial completo de ventas y comprobantes. | ✅ | ✅ | ❌ |
| `anular ventas` | Cancelar ventas pagadas y restituir stock. | ✅ | ❌ (Solo admin) | ❌ |
| `gestionar compras` | Crear órdenes de compra y pagar a proveedores. | ✅ | ❌ | ✅ |
| `recibir compras` | Recibir prendas físicas y dar de alta al Kardex. | ✅ | ❌ | ✅ |
| `ver cajas` | Consultar arqueos e informes de caja. | ✅ | ✅ | ❌ |
| `gestionar cajas` | Abrir y cerrar la caja diaria. | ✅ | ✅ | ❌ |
| `gestionar productos`| Crear, editar prendas y modificar precios. | ✅ | ❌ | ❌ |
| `gestionar usuarios` | Crear usuarios, asignar roles y contraseñas. | ✅ | ❌ | ❌ |

---

## 🛡️ 2. PROTECCIÓN EN CONTROLADORES (THIN CONTROLLERS)

Cada controlador protege sus acciones en el constructor mediante middleware:

```php
public function __construct()
{
    $this->middleware('permission:gestionar ventas|crear ventas', ['only' => ['index', 'show']]);
    $this->middleware('permission:crear ventas|gestionar ventas', ['only' => ['create', 'store', 'calcularTotales']]);
    $this->middleware('permission:anular ventas', ['only' => ['anularVenta']]);
    $this->middleware('permission:gestionar ventas', ['only' => ['edit', 'update', 'destroy']]);
}
```

Si un colaborador sin autorización intenta forzar una anulación vía URL o API, el sistema retorna automáticamente un código `403 Forbidden`.
