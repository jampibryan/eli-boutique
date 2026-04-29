# Eli Boutique - Sistema de Gestion Empresarial

Sistema web desarrollado con Laravel 10 para la gestion operativa de una boutique de ropa. Incluye ventas, compras, inventario por tallas, caja diaria, reportes PDF, reportes graficos y una API REST protegida para integraciones externas.

## Resumen

- Backend en Laravel 10 con PHP 8.2.
- Autenticacion web con Jetstream Livewire.
- API protegida con Laravel Sanctum.
- Roles y permisos con Spatie Permission.
- Inventario por talla mediante `producto_talla_stock`.
- Flujos transaccionales para ventas, compras y pagos.
- Reportes PDF con DomPDF.
- Integracion prevista para Flutter y Streamlit.

## Modulos principales

- Usuarios, roles y permisos.
- Clientes, colaboradores y proveedores.
- Productos e inventario por tallas.
- Carrito y punto de venta.
- Compras con flujo de estados.
- Caja diaria.
- Reportes PDF y graficos.
- Prediccion de ventas via consumo de API.

## Stack tecnico

### Backend

- PHP 8.2
- Laravel 10
- Laravel Jetstream
- Laravel Sanctum
- Livewire 3
- Spatie Laravel Permission
- DomPDF
- MySQL o MariaDB

### Frontend

- Vite
- Bootstrap 5
- Tailwind CSS
- Sass
- Chart.js
- Axios

## Requisitos

- PHP >= 8.2
- Composer >= 2
- Node.js >= 16
- MySQL / MariaDB
- Extension PHP `gd`
- Extension PHP `pdo_mysql`
- Extension PHP `mbstring`
- Extension PHP `xml`
- Extension PHP `curl`
- Extension PHP `zip`

## Instalacion

```bash
git clone https://github.com/tu-usuario/eli-boutique.git
cd eli-boutique
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Configura la conexion a base de datos en `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=eli_boutique
DB_USERNAME=root
DB_PASSWORD=
```

Ejecuta migraciones y seeders:

```bash
php artisan migrate --seed
```

Levanta assets y aplicacion:

```bash
npm run dev
php artisan serve
```

La aplicacion quedara disponible en:

```text
http://localhost:8000
```

## Variables de entorno importantes

Ademas de la base de datos, revisa estas variables:

```env
APP_URL=http://localhost

SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,localhost:8000,127.0.0.1,127.0.0.1:3000,127.0.0.1:8000
CORS_ALLOWED_ORIGINS=http://localhost,http://localhost:3000,http://localhost:8000,http://127.0.0.1,http://127.0.0.1:3000,http://127.0.0.1:8000,http://localhost:8501,http://127.0.0.1:8501
CORS_SUPPORTS_CREDENTIALS=false

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-correo@example.com
MAIL_PASSWORD=tu-password-de-aplicacion
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu-correo@example.com
MAIL_FROM_NAME="Eli Boutique"
```

Notas:

- `SANCTUM_STATEFUL_DOMAINS` y `CORS_ALLOWED_ORIGINS` deben ajustarse si Flutter, Streamlit u otro cliente consume la API desde otra direccion.
- El registro publico de usuarios esta deshabilitado. Los usuarios deben ser creados por un administrador.
- Para envio de correos se recomienda usar password de aplicacion.

## Autenticacion y seguridad

### Web

- El acceso al backoffice requiere inicio de sesion.
- La aplicacion usa Jetstream con stack Livewire.
- Las sesiones autenticadas pasan por `auth` y `AuthenticateSession`.

### API

- La API sensible ya no es publica.
- Los endpoints de `api/*` requieren `auth:sanctum`.
- Puedes consumirla mediante sesion autenticada o Bearer Token.
- Jetstream tiene habilitada la gestion de API tokens desde el perfil del usuario.

Ejemplo de uso con token:

```http
GET /api/productos
Authorization: Bearer TU_TOKEN
Accept: application/json
```

## Endpoints API principales

Base URL local:

```text
http://localhost:8000/api
```

Endpoints disponibles:

- `GET /api/dashboard`
- `GET /api/clientes`
- `GET /api/clientes/{id}`
- `GET /api/productos`
- `GET /api/productos/{id}`
- `GET /api/categorias`
- `GET /api/tallas`
- `GET /api/ventas`
- `GET /api/ventas/{id}`
- `GET /api/compras`
- `GET /api/compras/{id}`
- `GET /api/proveedores`
- `GET /api/proveedores/{id}`
- `GET /api/cajas`
- `GET /api/cajas/{id}`
- `GET /api/estados-transaccion`
- `GET /api/obtener-datos-ventas`

Formato de respuesta usual:

```json
{
  "success": true,
  "data": []
}
```

## Flujos de negocio relevantes

### Ventas

- Carrito con seleccion de talla y cantidad.
- Validacion de stock por talla antes de confirmar.
- Registro transaccional de venta y detalles.
- Anulacion con reposicion de stock por talla.
- Comprobante PDF y exportacion CSV.

### Compras

Flujo general:

```text
Borrador -> Enviada -> Cotizada -> Aprobada -> Recibida -> Pagada
```

Incluye:

- Orden de compra PDF.
- Envio por correo al proveedor.
- Recepcion de mercaderia con actualizacion de stock por talla.
- Registro de pagos.

### Inventario

- El stock oficial se maneja por talla.
- El total de un producto se obtiene sumando su stock por talla.
- La relacion principal se encuentra en `producto_talla_stock`.

## Roles y permisos

El proyecto usa Spatie Permission para controlar acceso por modulo. Algunos permisos del sistema son:

- gestionar usuarios
- gestionar clientes
- ver clientes
- gestionar colaboradores
- gestionar proveedores
- gestionar productos
- ver productos
- gestionar ventas
- crear ventas
- anular ventas
- gestionar compras
- ver cajas
- gestionar cajas
- ver reportes graficos

## Reportes

El sistema genera reportes PDF para:

- clientes
- colaboradores
- proveedores
- productos
- ventas
- compras
- caja
- ordenes de compra
- reportes graficos
- tiempos de ventas y compras

Tambien incluye reportes graficos con Chart.js para ventas y compras.

## Pruebas

Para ejecutar la suite:

```bash
php artisan test
```

La base del proyecto ya incluye pruebas para:

- autenticacion y sesion
- proteccion de rutas web
- proteccion de API con Sanctum
- transacciones de ventas
- flujo de compras y pagos

Si compilas frontend en produccion:

```bash
npm run build
```

## Estructura del proyecto

```text
app/
  Http/Controllers/
    Api/
  Models/
  Services/
  Mail/
database/
  migrations/
  seeders/
resources/
  views/
routes/
  web.php
  api.php
config/
tests/
public/
```

Directorios clave:

- `app/Services`: logica de negocio para ventas, compras y pagos.
- `app/Http/Controllers/Api`: endpoints para consumo externo.
- `resources/views`: vistas Blade por modulo.
- `database/seeders`: datos base del sistema.
- `tests/Feature`: pruebas funcionales del negocio.

## Desarrollo local

Comandos utiles:

```bash
php artisan optimize:clear
php artisan route:list
php artisan migrate
php artisan db:seed
php artisan test
```

Para desarrollo frontend:

```bash
npm run dev
```

## Produccion

Pasos basicos recomendados:

```bash
composer install --no-dev --optimize-autoloader
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Ademas:

- Apunta el document root a `public/`.
- Configura correctamente `APP_URL`.
- Define `CORS_ALLOWED_ORIGINS` segun los clientes autorizados.
- Usa HTTPS si la API saldra de red local.
- Revisa permisos de `storage/` y `bootstrap/cache/`.

## Notas operativas

- La extension `gd` es necesaria para ciertos reportes con imagenes.
- El logo principal se ubica en `public/img/logo_eli_boutique.png`.
- La guia de ventas estatica se encuentra en `public/guiaventas/`.

## Licencia

Proyecto privado de uso interno para Eli Boutique.
