# 🛍️ Eli Boutique - Sistema de Gestión Empresarial

<p align="center">
  <img src="public/img/logo_eli_boutique.png" alt="Eli Boutique Logo" width="120" style="border-radius: 50%;">
</p>

<p align="center">
  Sistema integral de gestión para boutique de ropa desarrollado con <strong>Laravel 10</strong>.<br>
  Control de ventas, compras, inventario, caja diaria, reportes gráficos y generación de PDFs profesionales.
</p>

---

## 📋 Tabla de Contenidos

- [Requisitos del Sistema](#-requisitos-del-sistema)
- [Instalación](#-instalación)
- [Módulos del Sistema](#-módulos-del-sistema)
- [Sistema de Roles y Permisos](#-sistema-de-roles-y-permisos)
- [Reportes y PDFs](#-reportes-y-pdfs)
- [Tecnologías Utilizadas](#️-tecnologías-utilizadas)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Notas Importantes](#-notas-importantes)
- [Despliegue en Producción](#-despliegue-en-producción)

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

### 🔮 Predicción de Ventas
- Módulo de análisis predictivo con datos históricos de ventas

### 📖 Guía de Ventas
- Documentación HTML interactiva para el proceso de ventas

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

---

## 📁 Estructura del Proyecto

```
eli-boutique/
├── app/
│   ├── Http/Controllers/       # Controladores por módulo
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
│   ├── web.php                 # Rutas principales
│   └── api.php                 # API (clientes, proveedores, ventas)
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
