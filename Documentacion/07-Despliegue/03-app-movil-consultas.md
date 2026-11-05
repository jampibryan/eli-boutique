# App móvil de consultas y distribución directa

La aplicación Flutter está en `D:\TESIS\eli_boutique_mobile`. Su versión 1.1.0 usa
`https://eliboutique.org/api/mobile/v1`, cuentas existentes de Eli Boutique y permisos
de Spatie. No contiene credenciales de SQL Server ni el token de Streamlit.

## API

- `POST /auth/login`: correo, contraseña, identificador aleatorio de instalación y código
  de autenticador cuando la cuenta tiene segundo factor confirmado. Límite de seis intentos
  por minuto/IP. No existe registro de cuentas desde la app.
- El token dura 30 días y contiene `read` y `mobile:read`. Una nueva sesión de la misma
  instalación sustituye únicamente su token anterior; las sesiones de otros equipos y de
  Streamlit se conservan.
- `GET /auth/me` valida sesión y actualiza permisos; `POST /auth/logout` revoca el token actual.
- GET de dashboard, gráficos y listados/detalles: permisos según módulo. Ninguna ruta móvil
  registra, modifica o elimina ventas, compras, clientes, productos, stock o cajas.
- Los listados admiten `page`, `per_page` (máximo 50), `q`, `state`, `from` y `to`. Las relaciones
  de detalle se consultan al abrir una operación. No se descargan miles de detalles para listar.
- El contrato conserva DNI/RUC como texto y convierte identificadores BIGINT a enteros.
- La ruta de analítica usada por Streamlit continúa funcionando sin cambios de contrato.
  Las rutas antiguas por módulo ahora comprueban habilidad de lectura y permisos del usuario.

## Gráficos

El período inicial son los últimos 30 días hasta la última venta disponible. El usuario puede
elegir otro intervalo de hasta 366 días. Las ventas incluyen operaciones pagadas con pago;
los ingresos y egresos usan la fecha del pago y restan vuelto. El flujo de caja no es utilidad.
Stock mínimo (hasta 15 unidades) y existencias son actuales, aunque cambie el filtro histórico.
No se entrena un modelo ni se hacen predicciones nuevas en el teléfono.

## Publicación del backend en SmartASP

El paquete privado generado para esta entrega se encuentra en
`storage/app/mobile-release/actualizacion-laravel-mobile.zip`.

1. Subirlo en el gestor de archivos y extraerlo en `/EliBoutique`, reemplazando los archivos
   del paquete. No extraer dentro de `/EliBoutique/public`.
2. Conservar el `.env` del servidor. El paquete no lo incluye y no requiere migraciones,
   reinstalar vendor, compilar Vite ni ejecutar seeders.
3. Reciclar el sitio. Si se usó caché de rutas anteriormente, ejecutar `php artisan route:clear`
   desde una consola del hosting, o retirar únicamente el archivo de caché de rutas generado
   que exista en `bootstrap/cache`; no eliminar el directorio ni otros archivos sin revisar.
4. `GET https://eliboutique.org/api/mobile/v1/auth/me` sin token debe devolver 401.
   Un 404 indica que el nuevo contrato todavía no está activo.
5. Instalar el APK e ingresar con una cuenta existente. Los permisos se administran en Laravel.
   Comprobar consultas por WiFi y por datos móviles, cierre de sesión y vuelta al primer plano.

El paquete también contiene la regla HTTPS en `public/web.config` y el catálogo de fotografías
vigente en el workspace, sin modificar registros. La raíz pública debe continuar siendo
`/EliBoutique/public`. Verificar que HTTP redirija a HTTPS y que las nuevas fotos respondan 200.

## Firma e instalación

Identificador Android: `org.eliboutique.consulta`. Se genera APK universal release para
instalación directa; no necesita Play Store ni App Bundle. La firma propia se conserva en
`android/keystore/eliboutique-release.jks` y se configura en `android/key.properties`, ambos
ignorados por Git. Guardar una copia privada de ambos fuera del equipo: se necesitan para
que una actualización se instale sobre la misma app. Nunca enviarlos con el APK ni publicarlos.

En Windows, los scripts de compilación desactivan las plataformas de escritorio mediante
variables de entorno del proceso para evitar requerir enlaces simbólicos de plugins Windows.
No modifican el modo desarrollador ni la configuración global de Flutter.

## Verificación

Las pruebas de backend se ejecutan con `phpunit.analytics.xml`, que fuerza SQLite en memoria
y tiene guardas antes de cualquier RefreshDatabase. Nunca usar la base remota en esas pruebas.
Las lecturas de integración con SQL Server se verifican aparte sin mutaciones comerciales.
Los tests Flutter usan respuestas simuladas, sin correos, contraseñas ni tokens reales.

La comprobación final con el servidor público y un teléfono requiere haber extraído primero
la actualización de Laravel. Generar el APK por sí solo no publica las rutas del servidor.
