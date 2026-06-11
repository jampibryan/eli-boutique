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
- Extension PHP `openssl` (requerida por Greenter para firmar digitalmente XMLs)
- Extension PHP `soap` (requerida por Greenter para transmitir por SOAP a la SUNAT)

## Instalacion

```bash
git clone https://github.com/tu-usuario/eli-boutique.git
cd eli-boutique
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Configura la conexión a la base de datos en el archivo `.env` recién creado.

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

Además de la base de datos, asegúrate de configurar las siguientes secciones en el archivo `.env`:

- **Conexión API (Sanctum y CORS):** Define `SANCTUM_STATEFUL_DOMAINS` y `CORS_ALLOWED_ORIGINS` si planeas consumir la API desde clientes externos (como Flutter o Streamlit).
- **Envío de Correos (SMTP):** Configura las variables `MAIL_*` con las credenciales de tu servidor de correo (se recomienda usar contraseñas de aplicación si utilizas Gmail).
- **Facturación Electrónica (SUNAT / Greenter):**
  - `SUNAT_MODE`: Indica el ambiente (`beta` para desarrollo/pruebas o `production` para producción).
  - `SUNAT_RUC`: RUC real de la empresa (solo necesario en modo producción).
  - `SUNAT_USUARIO`: Usuario SOL real (solo necesario en modo producción).
  - `SUNAT_CLAVE`: Clave SOL real (solo necesario en modo producción).
  - `SUNAT_CERTIFICATE_PATH`: Ruta relativa al certificado digital `.crt` en `storage/` (por defecto: `app/certificates/certificate.crt`).
  - `SUNAT_PRIVATE_KEY_PATH`: Ruta relativa a la llave privada `.key` en `storage/` (por defecto: `app/certificates/private.key`).

Notas:

- `SANCTUM_STATEFUL_DOMAINS` y `CORS_ALLOWED_ORIGINS` deben ajustarse si Flutter, Streamlit u otro cliente consume la API desde otra direccion.
- El registro publico de usuarios esta deshabilitado. Los usuarios deben ser creados por un administrador.
- Para envio de correos se recomienda usar password de aplicacion.
- En el ambiente `beta` no es necesario especificar RUC, usuario ni clave en el `.env`, ya que por defecto utiliza las credenciales de prueba oficiales de la SUNAT (`20000000001` / `MODDATOS` / `moddatos`).

## Autenticacion y seguridad

### Web

- El acceso al backoffice requiere inicio de sesion.
- La aplicacion usa Jetstream con stack Livewire.
- Las sesiones autenticadas pasan por `auth` y `AuthenticateSession`.
- **Validación de Identidad:** El registro y edición de clientes valida de forma estricta que el identificador sea un DNI (8 dígitos) o un RUC (11 dígitos).

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

- Carrito con selección de talla y cantidad.
- Validación de stock por talla antes de confirmar.
- Registro transaccional de venta y detalles.
- Anulación con reposición de stock por talla.
- **Comprobantes Inteligentes:** Generación de PDF adaptado al tipo de cliente: botón "Generar Boleta" para clientes con DNI (8 dígitos) y "Generar Factura" para clientes con RUC (11 dígitos).
- **Diseño Premium:** Formato moderno en PDF con colores institucionales (Carbón y Dorado), incluyendo cálculos automáticos de subtotal, IGV y total multiplicados por cantidad.
- **Soporte de Ventas Históricas:** Resuelve de forma segura el reporte de tallas en ventas antiguas que carecen del identificador mediante un fallback automático al primer stock registrado del producto.
- Exportación de ventas a CSV.

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

### Caja

- Apertura y cierre de caja diaria con control de saldos y transacciones.
- **Seguridad en Cierre de Caja:** El cálculo de saldos y el estado final se procesa de forma interna en el servidor para evitar que el usuario altere los totales de efectivo en el flujo frontend.
- Reporte detallado de movimientos e informes generales en PDF.

### Facturación Electrónica (SUNAT)

- **Integración con Greenter SDK:** Generación del XML firmado digitalmente en formato UBL 2.1 para Facturas (tipo `01`) y Boletas (tipo `03`).
- **Transmisión Asíncrona SOAP:** El envío se despacha de forma asíncrona mediante un Laravel Job (`SendVentaToSunatJob`) inmediatamente tras confirmar y pagar un comprobante.
- **Trazabilidad y CDR:** Guarda los XMLs firmados localmente en `storage/app/invoices/` y las constancias de recepción de SUNAT (CDR zip) en `storage/app/cdrs/`.
- **Representación Física:** Muestra en el PDF de venta la numeración oficial (ej: `B001-00000001`), el sello digital de aceptación y el código hash de la firma digital (DigestValue).
- **Monitoreo desde Dashboard:** Estado de transmisión en tiempo real visible con badges en el listado de ventas (`Pendiente`, `Enviando`, `Aceptado`, `Rechazado`, `Error`).

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
