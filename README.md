# 🛍️ Eli Boutique - Sistema de Gestión Empresarial

<p align="center">
  <img src="public/img/logo_eli_boutique.png" alt="Eli Boutique Logo" width="120" style="border-radius: 50%;">
</p>

<p align="center">
  Sistema integral de gestión para boutique de ropa desarrollado con <strong>Laravel 10</strong>.<br>
  Control de ventas, compras, inventario, caja diaria, reportes gráficos, generación de PDFs profesionales<br>
  y <strong>API REST</strong> para consumo desde aplicación móvil Flutter y análisis predictivo con Streamlit.
</p>

---

## 📋 Tabla de Contenidos

- [Arquitectura del Sistema](#-arquitectura-del-sistema)
- [Requisitos del Sistema](#-requisitos-del-sistema)
- [Instalación](#-instalación)
- [Módulos del Sistema](#-módulos-del-sistema)
- [API REST](#-api-rest)
- [Sistema de Roles y Permisos](#-sistema-de-roles-y-permisos)
- [Reportes y PDFs](#-reportes-y-pdfs)
- [Tecnologías Utilizadas](#️-tecnologías-utilizadas)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Notas Importantes](#-notas-importantes)
- [Despliegue en Producción](#-despliegue-en-producción)

---

## 🏗️ Arquitectura del Sistema

El sistema opera con una arquitectura de tres capas conectadas por red local (LAN):

```
┌─────────────────────────────────────────────────────────────────┐
│                        RED LOCAL (LAN)                          │
│                                                                 │
│  ┌──────────────────┐   HTTP/JSON   ┌────────────────────────┐  │
│  │  📱 Flutter App  │ ◄───────────► │  🖥️ Laravel Backend   │  │
│  │  (Dart)          │    /api/*     │  PHP 8.2 + MySQL       │  │
│  │  App Móvil       │               │  192.168.0.102:8000    │  │
│  └──────────────────┘               └────────────┬───────────┘  │
│                                                  │              │
│  ┌──────────────────┐   HTTP/JSON                │              │
│  │  📊 Streamlit    │ ◄─────────────────────────►│              │
│  │  (Python)        │  /api/obtener-datos-ventas │              │
│  │  ML Predicción   │                            │              │
│  └──────────────────┘                                           │
└─────────────────────────────────────────────────────────────────┘
```

| Componente | Tecnología | Función |
|------------|------------|--------|
| **Backend** | Laravel 10 (PHP 8.2) | API REST + Aplicación web + Base de datos |
| **App Móvil** | Flutter (Dart) | Consulta de datos en tiempo real vía API |
| **ML/Predicción** | Streamlit (Python) | Análisis predictivo de ventas |

**Servidor de desarrollo:**
```bash
php artisan serve --host=0.0.0.0 --port=8000
```
Accesible en LAN desde cualquier dispositivo en `http://192.168.0.102:8000`

---

## 📌 Requisitos del Sistema

| Requisito | Versión mínima |
|-----------|---------------|
| PHP | >= 8.2 |
| Composer | >= 2.0 |
| MySQL / MariaDB | >= 5.7 / >= 10.3 |
| Node.js | >= 16.x |

### Extensiones PHP Requeridas

- `ext-gd` — Procesamiento de imágenes para PDFs
- `ext-pdo` — Conexión a base de datos
- `ext-mbstring` — Manejo de strings multibyte
- `ext-xml` — Procesamiento XML
- `ext-curl` — Peticiones HTTP
- `ext-zip` — Manejo de archivos comprimidos

---

## 🚀 Instalación

```bash
# 1. Clonar el repositorio
git clone https://github.com/tu-usuario/eli-boutique.git
cd eli-boutique

# 2. Instalar dependencias
composer install
npm install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar base de datos en .env
# DB_DATABASE=eli_boutique
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Configurar correo en .env (para envío de órdenes de compra)
# MAIL_MAILER=smtp
# MAIL_HOST=smtp.gmail.com
# MAIL_PORT=587
# MAIL_USERNAME=tu-correo@gmail.com
# MAIL_PASSWORD=tu-contraseña-de-aplicacion
# MAIL_ENCRYPTION=tls

# 6. Ejecutar migraciones y seeders
php artisan migrate:fresh --seed

# 7. Compilar assets
npm run dev

# 8. Iniciar servidor
php artisan serve
```

Accede a: `http://localhost:8000`

---

## 📦 Módulos del Sistema

### 👥 Gestión de Usuarios
- CRUD completo de usuarios del sistema
- Asignación de roles (Administrador, Gerente, Vendedor)
- Control de acceso basado en permisos

### 📊 Clientes
- Registro y administración de clientes
- Búsqueda rápida vía API
- Reporte PDF con listado ordenado alfabéticamente
- Eliminación lógica (SoftDeletes)

### 🤝 Colaboradores
- Gestión de personal con cargo asociado
- Relación con tipo de género y cargo
- Reporte PDF profesional

### 🏢 Proveedores
- Administración de proveedores (persona natural y jurídica)
- Búsqueda rápida vía API
- Reporte PDF generado con DomPDF
- Eliminación lógica (SoftDeletes)

### 📦 Productos e Inventario
- Catálogo con categorías: Polos & Camisetas, Jeans & Pantalones, Shorts & Bermudas, Abrigos & Chaquetas, Ropa Deportiva
- Gestión de tallas (S, M, L, XL, 28, 30, 32, 34) con stock individual por talla
- Género de producto (Unisex, Hombre, Mujer)
- Control automático de stock total calculado
- Validación de inventario antes de vender
- Reporte PDF del catálogo

### 🛒 Ventas
- Punto de venta con carrito de compras interactivo
- Agregar, duplicar, cambiar talla, actualizar cantidad de ítems
- Generación automática de código secuencial (0000001, 0000002...)
- Comprobante PDF por venta (Boleta / Factura)
- Anulación de ventas con devolución automática de stock por talla
- Sincronización automática con la caja del día (ingresos, productos vendidos, clientes atendidos)
- Exportación a CSV
- Reporte PDF general de ventas

### 💰 Compras
- Flujo profesional de estados: **Borrador → Enviada → Cotizada → Aprobada → Recibida/Pagada**
- Generación de orden de compra en PDF
- Envío automático de email al proveedor con PDF adjunto
- Cotización con condiciones de pago y días de crédito
- Anulación de compras
- Reporte PDF de compras

### 💵 Cajas
- Apertura y cierre de caja diaria
- Código de caja auto-generado
- Informe individual por caja con métricas financieras (ingresos, gastos, balance)
- Control de estados: pendiente, abierta, cerrada
- Reporte PDF de caja

### 📈 Reportes Gráficos
- **Ventas**: Gráfico interactivo con Chart.js (barras + línea de tendencia)
- **Compras**: Gráfico interactivo con esquema de colores diferenciado
- Filtros por **mes** (con rango opcional de meses) o por **día**
- Las etiquetas del gráfico usan formato dd/mm/yyyy y mm/yyyy
- Exportación a PDF agrupado:
  - Por mes: resumen mensual (cantidad de operaciones, productos, subtotal, IGV, total)
  - Por día: resumen diario con totales

### ⏱️ Reportes de Tiempo
- **Tiempo de Ventas**: Reporte PDF con tiempo estimado de atención por venta (rango 40-80 segundos)
- **Tiempo de Orden de Compras**: Reporte PDF con tiempo estimado de procesamiento por compra (rango 80-120 segundos)
- Fórmula determinista basada en cantidad de ítems, unidades y hash CRC32
- Incluye promedio, mínimo y máximo por período

### 🔮 Predicción de Ventas
- Módulo de análisis predictivo con datos históricos de ventas
- Endpoint API dedicado para alimentar modelo Streamlit (Python)

### 📖 Guía de Ventas
- Documentación HTML interactiva para el proceso de ventas

---

## 🌐 API REST

API de solo lectura (GET) para consumo desde Flutter y Streamlit. No requiere autenticación (uso en red local).

**Base URL:** `http://192.168.0.102:8000/api`

**Controlador:** `App\Http\Controllers\Api\ApiController`

| Endpoint | Descripción |
|----------|-------------|
| `GET /api/dashboard` | Resumen del día: caja activa, ventas, totales de clientes/productos/proveedores |
| `GET /api/clientes` | Listado de clientes con tipo de género |
| `GET /api/clientes/{id}` | Detalle de un cliente específico |
| `GET /api/productos` | Catálogo con categoría, género y stock desglosado por talla |
| `GET /api/productos/{id}` | Detalle de producto con stock por talla |
| `GET /api/categorias` | Catálogo de categorías de producto |
| `GET /api/tallas` | Catálogo de tallas disponibles |
| `GET /api/ventas` | Listado de ventas con cliente, estado, detalles y comprobante |
| `GET /api/ventas/{id}` | Detalle de venta con productos vendidos |
| `GET /api/compras` | Listado de compras con proveedor, detalles y estado |
| `GET /api/compras/{id}` | Detalle de compra |
| `GET /api/proveedores` | Listado de proveedores |
| `GET /api/proveedores/{id}` | Detalle de proveedor |
| `GET /api/cajas` | Historial de cajas ordenado por fecha |
| `GET /api/cajas/{id}` | Detalle de caja con ventas y balance diario |
| `GET /api/estados-transaccion` | Catálogo de estados (Pendiente, Pagado, Anulado, etc.) |
| `GET /api/obtener-datos-ventas` | Datos aplanados para ML/predicción (Streamlit) |

**Formato de respuesta estándar:**
```json
{
  "success": true,
  "data": [ ... ]
}
```

**Configuración CORS** (`config/cors.php`): Permite cualquier origen (`*`) para acceso desde dispositivos en la red local.

---

## 🔐 Sistema de Roles y Permisos

El sistema utiliza **Spatie Laravel Permission** con 3 roles y 14 permisos:

| Permiso | Administrador | Gerente | Vendedor |
|---------|:---:|:---:|:---:|
| Gestionar usuarios | ✅ | ❌ | ❌ |
| Gestionar clientes | ✅ | ✅ | ✅ |
| Ver clientes | ✅ | ✅ | ✅ |
| Gestionar colaboradores | ✅ | ✅ | ✅ |
| Gestionar proveedores | ✅ | ✅ | ❌ |
| Gestionar productos | ✅ | ✅ | ✅ |
| Ver productos | ✅ | ✅ | ✅ |
| Gestionar ventas | ✅ | ✅ | ✅ |
| Crear ventas | ✅ | ✅ | ✅ |
| Anular ventas | ✅ | ✅ | ✅ |
| Gestionar compras | ✅ | ✅ | ❌ |
| Ver cajas | ✅ | ✅ | ✅ |
| Gestionar cajas | ✅ | ✅ | ✅ |
| Ver reportes gráficos | ✅ | ✅ | ❌ |

---

## 📄 Reportes y PDFs

Todos los reportes utilizan **DomPDF** con diseño profesional (A4 landscape):

| Reporte | Descripción |
|---------|-------------|
| Reporte de Clientes | Listado ordenado alfabéticamente con datos de contacto |
| Reporte de Colaboradores | Personal con cargo y datos personales |
| Reporte de Proveedores | Empresas/personas proveedoras con RUC y contacto |
| Reporte de Productos | Catálogo completo con stock por tallas |
| Comprobante de Venta | Boleta/Factura individual por venta |
| Reporte de Ventas | Listado filtrado por rango de fechas |
| Orden de Compra | PDF profesional enviado por email al proveedor |
| Reporte de Compras | Listado filtrado con detalle de estados |
| Informe de Caja | Métricas financieras diarias |
| Gráfico de Ventas (PDF) | Resumen agrupado por mes o día + imagen del gráfico |
| Gráfico de Compras (PDF) | Resumen agrupado por mes o día + imagen del gráfico |
| Tiempo de Ventas | Tiempo estimado de atención por venta |
| Tiempo de O. Compras | Tiempo estimado de procesamiento por orden de compra |

Características comunes:
- Header con logo, datos de la empresa y fecha de emisión
- Diseño con gradientes y colores temáticos por módulo
- Tabla con fila de totales destacada
- Footer con paginación automática

---

## 🛠️ Tecnologías Utilizadas

### Backend
| Tecnología | Versión | Propósito |
|------------|---------|-----------|
| Laravel | ^10.10 | Framework PHP principal |
| Laravel Jetstream | ^4.3 | Autenticación y gestión de sesiones |
| Laravel Sanctum | ^3.3 | Autenticación SPA/API |
| Livewire | ^3.0 | Componentes reactivos |
| Spatie Permission | ^6.9 | Sistema de roles y permisos |
| DomPDF | ^3.0 | Generación de reportes PDF |
| Laravel AdminLTE | ^3.13 | Template de administración |
| GuzzleHTTP | ^7.2 | Cliente HTTP |

### Frontend
| Tecnología | Versión | Propósito |
|------------|---------|-----------|
| Bootstrap | ^5.2.3 | Framework CSS principal |
| Tailwind CSS | ^3.1.0 | Utilidades CSS complementarias |
| Chart.js | ^4.4.5 | Gráficos interactivos |
| Vite | ^5.0.0 | Build tool y HMR |
| Sass | ^1.56.1 | Preprocesador CSS |
| Axios | ^1.6.4 | Peticiones HTTP asíncronas |
| Font Awesome | — | Iconografía |

### Infraestructura
| Tecnología | Propósito |
|------------|-----------|
| MySQL | Base de datos relacional |
| SMTP (Gmail) | Envío de emails con órdenes de compra |
### Ecosistema Multi-plataforma
| Tecnología | Versión | Propósito |
|------------|---------|----------|
| Flutter | — | Aplicación móvil (consultas vía API REST) |
| Streamlit | — | Dashboard de predicción ML (Python) |
| CORS | — | Acceso entre plataformas en red local |
---

## 📁 Estructura del Proyecto

```
eli-boutique/
├── app/
│   ├── Http/Controllers/       # Controladores por módulo
│   │   └── Api/                # ApiController — endpoints REST
│   ├── Models/                 # 20 modelos Eloquent
│   ├── Mail/                   # Mailable para órdenes de compra
│   └── Providers/              # Service Providers
├── database/
│   ├── migrations/             # Esquema de base de datos
│   └── seeders/                # Datos iniciales (roles, permisos, catálogos)
├── resources/views/
│   ├── Caja/                   # Vistas de caja (index, informe, reporte)
│   ├── Carrito/                # Carrito de compras
│   ├── Cliente/                # CRUD de clientes
│   ├── Colaborador/            # CRUD de colaboradores
│   ├── Compra/                 # Compras (index, create, edit, cotizar, orden)
│   ├── Pago/                   # Formulario de pagos
│   ├── Predecir/               # Predicción de ventas
│   ├── Producto/               # CRUD de productos
│   ├── Proveedor/              # CRUD de proveedores
│   ├── Reporte/                # Gráficos y reportes PDF
│   ├── User/                   # Gestión de usuarios
│   ├── Venta/                  # Ventas (index, create, comprobante, reporte)
│   └── emails/                 # Templates de correo
├── public/
│   ├── img/                    # Imágenes del sistema y productos
│   ├── guiaventas/             # Guía HTML de ventas
│   └── help/                   # Documentación de ayuda
├── routes/
│   ├── web.php                 # Rutas web (vistas, PDFs, carrito)
│   └── api.php                 # API REST — 18 endpoints GET para Flutter/Streamlit
└── config/
    ├── adminlte.php            # Configuración del menú lateral
    └── permission.php          # Configuración de Spatie Permission
```

---

## 📝 Notas Importantes

### Extensión GD (requerida para PDFs con imágenes)

Si aparece el error `Function imagecreatefromwebp() not found`:

**XAMPP (Windows):**
1. Edita `C:\xampp\php\php.ini`
2. Busca `;extension=gd` y quita el `;`
3. Reinicia Apache

**Linux:**
```bash
sudo apt-get install php8.2-gd
sudo systemctl restart apache2
```

### Logo del Sistema
- Ubicación: `public/img/logo_eli_boutique.png`
- Formato: PNG (requerido por DomPDF)
- Tamaño recomendado: 512x512px

### Correo Electrónico
Para el envío de órdenes de compra por email, se necesita configurar Gmail con una **contraseña de aplicación** (no usar la contraseña regular de la cuenta). Se genera en: [Contraseñas de aplicación de Google](https://myaccount.google.com/apppasswords)

---

## 🌐 Despliegue en Producción

```bash
# Verificar requisitos
composer check-platform-reqs

# Instalar sin dependencias de desarrollo
composer install --optimize-autoloader --no-dev

# Compilar assets para producción
npm run build

# Cachear configuración
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Configuración del servidor:**
1. Document root apuntando a `/public`
2. Módulo `mod_rewrite` habilitado
3. Permisos correctos:
```bash
chmod -R 755 storage bootstrap/cache
```

---

## 📄 Licencia

Este proyecto es privado y de uso exclusivo para **Eli Boutique**.

---

<p align="center">
  Desarrollado por JampiBryan
</p>
