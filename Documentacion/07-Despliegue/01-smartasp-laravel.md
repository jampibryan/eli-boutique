# Publicación de Eli Boutique en SmartASP.NET

Esta guía corresponde al proyecto actual: Laravel 10, PHP 8.2 o compatible, SQL Server ya existente y servidor IIS de SmartASP.NET. El sitio creado es `EliBoutique`; inicialmente contiene solamente `default.asp`. Preparar el paquete no publica el sitio ni modifica registros.

## 1. Confirmar el alojamiento

En Sitios web → EliBoutique → Administrar/Más funciones, seleccionar PHP 8.2 si está disponible. Confirmar que ese mismo PHP de la web tiene `pdo_sqlsrv`, los controladores de Microsoft necesarios y `gd`, además de las extensiones habituales de Laravel (mbstring, XML/DOM, fileinfo, curl, OpenSSL, PDO, etc.). Tener una base SQL Server contratada no demuestra por sí solo que el PHP del sitio tenga el controlador correcto.

Si no aparece la opción, solicitar a soporte que confirme PHP 8.2 con `pdo_sqlsrv` y `gd` para ese sitio. El comando `composer check-platform-reqs` del preparador comprueba el PHP de la PC, no el del alojamiento.

## 2. Preparar el paquete en PowerShell

Desde la carpeta del proyecto:

```powershell
Set-Location C:\xampp\htdocs\eli-boutique
php -v
composer --version
node --version
npm.cmd --version
.\scripts\prepare_smartasp.ps1 -InstallFrontendDependencies
```

El preparador ejecuta `npm ci`, `npm run build` y crea una carpeta nueva en `storage/app/releases/`. Copia las fuentes actuales del espacio de trabajo, incluidos archivos aún no confirmados en Git. En esa copia instala Composer con `--no-dev --optimize-autoloader --no-scripts`; no modifica el `vendor` de desarrollo.

Produce `EliBoutique-smartasp.zip`, una carpeta `EliBoutique` descomprimida y `manifest.json` con tamaño y SHA-256. El ZIP tiene `app`, `public`, `vendor`, etc. directamente en su raíz: no contiene una carpeta contenedora adicional.

Incluye vistas, traducciones, imágenes de productos, recursos compilados, migraciones y adjuntos del disco público. Excluye el `.env` real, `.git`, `node_modules`, pruebas, scripts de carga, exports, respaldos, sesiones, logs, cachés locales, certificados y enlaces absolutos del equipo. Los planes de demostración y de reversión se quedan localmente. No ejecuta migraciones, seeders, entrenamiento ni envíos de correo.

Para regenerarlo después de cambios, ejecutar el mismo comando. Cada ejecución crea una carpeta distinta. No utilizar `git archive` como sustituto mientras haya archivos nuevos sin seguimiento: omitiría esos cambios.

No ejecutar `composer update` ni `npm audit fix --force` como parte automática de este despliegue. Las alertas de dependencias deben revisarse y sus actualizaciones probarse aparte.

## 3. Preparar el directorio público del sitio

En Archivos, crear `/EliBoutique/public` si aún no existe. En Sitios web → EliBoutique → Más funciones → Mapped Path, seleccionar esa carpeta como raíz pública **antes de subir o configurar archivos privados**. El sitio puede responder 403/404 mientras la carpeta esté vacía.

La estructura final debe ser:

```text
/EliBoutique/
    .env                       ← privado, creado en el servidor
    .env.production.example    ← plantilla sin contraseña ni APP_KEY
    artisan
    composer.json
    composer.lock
    app/
    bootstrap/cache/
    config/
    database/migrations/
    lang/
    resources/
    routes/
    storage/
    vendor/
    public/                    ← única raíz web
        index.php
        web.config
        build/manifest.json
        css/
        js/
        img/
```

El `public/web.config` incluido establece `index.php` como documento inicial y envía las rutas que no son archivos o carpetas al controlador de Laravel. No mover `index.php` a la raíz ni agregar `/public` a la URL pública. No combinar estas reglas con otras que vuelvan a anteponer `public/`.

## 4. Subir y extraer

1. En Archivos, abrir `/EliBoutique`, no `/EliBoutique/public`.
2. Subir `EliBoutique-smartasp.zip`.
3. Seleccionar el ZIP → Unzip/Extraer → destino `/EliBoutique`.
4. Revisar Cola de trabajo hasta finalizar.
5. Confirmar que `/EliBoutique/artisan`, `/EliBoutique/vendor/autoload.php`, `/EliBoutique/public/index.php`, `/EliBoutique/public/web.config` y `/EliBoutique/public/build/manifest.json` existen.

No subir solo `public/`: el archivo `index.php` necesita las carpetas privadas hermanas. No restaurar la base ni crear una nueva: se usa la ya existente. El `default.asp` original puede permanecer fuera de la raíz pública.

## 5. Configurar el .env del servidor

Copiar `/EliBoutique/.env.production.example` como `/EliBoutique/.env`, usando el editor del alojamiento. Mantenerlo fuera de `public`.

Completar:

- `APP_KEY`: copiar el valor exacto del `.env` actual del proyecto. No mostrarlo en capturas ni regenerarlo sobre la instalación existente.
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`: usar la conexión actual que ya funciona, con `DB_CONNECTION=sqlsrv`. Conservar las opciones de cifrado/certificado que correspondan a esa conexión.
- `APP_URL=https://agchavin-001-site1.jtempurl.com` mientras se pruebe la dirección temporal. Después cambiarlo a `https://eliboutique.org` cuando el dominio y su certificado estén operativos.
- `APP_ENV=production` y `APP_DEBUG=false`.
- `SESSION_DRIVER=file`, `CACHE_DRIVER=file`, `SESSION_DOMAIN=null` y `SESSION_SECURE_COOKIE=true` para la prueba inicial por HTTPS.

La plantilla usa `MAIL_MAILER=log`: durante la prueba los correos se registran, no se entregan. Configurar el SMTP real antes de usar envío de órdenes de compra en producción. Configurar también la clave de DeepSeek si se habilita el asistente. Estos secretos solo van en el servidor; no en Git ni en capturas.

El paquete no lleva configuración cacheada de la PC. No ejecutar `config:cache` localmente para luego subir ese archivo: contendría rutas y valores del entorno local.

## 6. Escritura y archivos públicos de storage

La identidad de IIS/PHP necesita escribir en `storage/`, `bootstrap/cache/` y `public/img/productos/`. Si hay errores de acceso y el panel no permite ajustar permisos, pedir al soporte de SmartASP.NET que los configure.

El paquete omite el enlace `public/storage` de Windows porque apunta a una ruta de esta PC. Para servir los adjuntos de `storage/app/public`:

- Si existe consola PHP en el servidor, ejecutar allí `php artisan storage:link` desde `/EliBoutique`.
- Si no hay consola o no se permite crear el enlace, usar un directorio virtual IIS con alias `storage` dentro del sitio, dirigido a la carpeta física `/EliBoutique/storage/app/public`. Confirmar la ruta física absoluta con el panel o soporte. No exponer `storage/app` completo.

Las imágenes de productos en `public/img/productos` ya están incluidas y no dependen de ese enlace.

## 7. Comandos opcionales exclusivamente en el servidor

El alojamiento compartido puede no ofrecer terminal. El ZIP ya incluye dependencias y recursos; Laravel puede generar los manifiestos y vistas al arrancar si los directorios tienen escritura. No hace falta una consola para el primer arranque.

Si el panel o soporte ofrece ejecución de PHP/Artisan, desde la carpeta física del proyecto en el servidor se pueden ejecutar:

```powershell
php artisan package:discover --ansi
php artisan migrate:status
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Después de modificar `.env` cuando exista configuración cacheada, repetir `php artisan config:cache` en el servidor. El estado de migraciones es una consulta; no ejecutar nuevas migraciones sin revisar cuáles faltan y respaldar antes.

No ejecutar para esta primera publicación `migrate:fresh`, `migrate:refresh`, `db:wipe`, seeders ni los cargadores de demostración. La base actual ya contiene las operaciones y los cambios de esquema realizados. `php artisan serve` es para desarrollo local: IIS atiende la web publicada.

No crear páginas públicas para ejecutar comandos Artisan o consultas SQL.

## 8. Verificación y pasos posteriores

1. Abrir la dirección temporal por HTTPS y comprobar la página de acceso.
2. Iniciar sesión y revisar los listados existentes: 4.057 ventas, 3.217 boletas y 840 facturas en el corte de la última carga, salvo operaciones posteriores.
3. Comprobar clientes empresa, detalle de ventas, PDF, imágenes, adjuntos, inventario por talla, compras y caja sin insertar nuevos registros solo para probar la instalación.
4. Comprobar que rutas como `/.env`, `/composer.json`, `/storage/logs/laravel.log` y `/scripts/demo_data.php` no entregan archivos privados. Deben responder 403/404, no contenido del proyecto.
5. Si aparece un error 500, consultar `storage/logs/laravel.log` desde Archivos, manteniendo `APP_DEBUG=false`. Si aparece `could not find driver`, confirmar `pdo_sqlsrv` en el PHP de la web. Si solo funcionan `/index.php` o la portada pero las demás rutas no, revisar `web.config` y URL Rewrite en IIS.
6. Enlazar `eliboutique.org` desde Dominios del sitio y DNS de Porkbun, activar SSL para ese dominio y actualizar `APP_URL`.
7. Configurar después Streamlit Cloud con el contrato `snapshot`, la URL HTTPS y un token dedicado de lectura. Flutter se verifica al final. SUNAT continúa pendiente.

## Referencias oficiales

- [Despliegue Laravel 10](https://laravel.com/docs/10.x/deployment).
- [Mapped Path en SmartASP.NET](https://www.smarterasp.net/support/kb/a2439/how-to-change-websites-root-path-from-control-panel.aspx).
- [Extraer ZIP en SmartASP.NET](https://www.smarterasp.net/support/kb/a2414/how-to-extract-a-zip-file-in-file-manager.aspx).
- [Directorio virtual en SmartASP.NET](https://www.smarterasp.net/support/kb/a2016/how-to-set-the-virtual-directory-from-your-hosting-account.aspx).
- [Permisos de archivos en SmartASP.NET](https://www.smarterasp.net/support/kb/a2438/how-to-change-file-permissions-from-the-control-panel.aspx).
