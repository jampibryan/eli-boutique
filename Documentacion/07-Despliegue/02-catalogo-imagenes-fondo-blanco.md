# Actualización de las imágenes de productos

Se prepararon 19 imágenes ilustrativas generadas con la herramienta integrada ImageGen: una por código de producto, fondo blanco y prendas sin personas, maniquíes ni perchas. No son fotografías verificadas de la mercadería real. Los prompts exactos y sus archivos originales están registrados localmente en `storage/app/catalogue-images/20261104/generation.json`.

## Cambios incluidos

- `app/Models/Producto.php`: resuelve la nueva imagen por código para productos sin foto o con una de las rutas retiradas. Una foto subida posteriormente desde el formulario tiene prioridad.
- `config/product_images.php`: correspondencia de los 19 códigos con las nuevas imágenes JPG. No cambia registros de la base.
- `resources/views/components/producto-imagen.blade.php`: imagen de respaldo SVG integrada en el HTML y desactivación del manejador de errores antes de sustituir una imagen fallida; no genera solicitudes repetidas.
- `resources/views/Producto/index.blade.php`: usa el componente en tarjetas y detalles, fondo blanco y ajuste de imagen sin recortar la prenda.
- `resources/views/Producto/edit.blade.php`: muestra la imagen resuelta, incluye el género obligatorio que faltaba y muestra los errores de validación de la foto.
- `app/Http/Controllers/ProductoController.php`: entrega los géneros al formulario de edición.
- `public/img/productos/catalogo-blanco/`: las 19 fotos JPG, con nombres ASCII por código.
- `public/img/productos/web.config`: habilita el tipo MIME `image/webp` únicamente en la carpeta de productos, para futuras cargas WebP. Las nuevas imágenes del catálogo son JPG y no dependen de esta configuración.

Los cinco WebP anteriores se retiraron del directorio público local y se conservaron en `storage/app/product-image-backups/20261104-catalogo-blanco/`. El ZIP de actualización no contiene las imágenes anteriores ni borra archivos del servidor al extraerse.

## Aplicar en SmartASP

Paquete: `storage/app/releases/20261104-productos-fondo-blanco/actualizacion-productos.zip`.

1. Guardar una copia de los archivos de aplicación indicados arriba, el archivo `config/product_images.php` si ya existe y cualquier `web.config` existente en `public/img/productos`. Conservar las fotos anteriores fuera de la raíz pública si se desea retirarlas físicamente del servidor.
2. Subir el ZIP a `/EliBoutique` y extraerlo **en `/EliBoutique`**, reemplazando los archivos incluidos. La raíz del ZIP contiene `app`, `config`, `resources` y `public`; no extraer en `/EliBoutique/public`.
3. Limpiar la configuración y las vistas compiladas en el servidor: `php artisan config:clear` y `php artisan view:clear`. Si se usa configuración cacheada, regenerarla allí con `php artisan config:cache`. Sin consola, retirar únicamente `/EliBoutique/bootstrap/cache/config.php`, si existe, y los `.php` compilados de `/EliBoutique/storage/framework/views`, conservando las carpetas y `.gitignore`. No subir cachés generadas en esta PC.
4. Comprobar `https://eliboutique.org/img/productos/catalogo-blanco/cam-002.jpg`: debe responder 200 con contenido de imagen. Abrir `/productos`, revisar ambas páginas del catálogo, detalles y edición, y comprobar que los errores no crecen continuamente en la consola.
5. Las antiguas rutas se dejan de usar automáticamente. Para retirarlas físicamente del alojamiento después de comprobar el nuevo catálogo, trasladar fuera de `public` solo los cinco archivos enumerados en `config/product_images.php` bajo `replaced_paths`; no borrar la carpeta completa, que también recibe nuevas cargas.

Esta actualización no requiere migraciones, seeders, reinstalación de dependencias ni cambios en `.env`. No modifica ventas, precios, compras, stock ni caja. No fue publicada desde esta sesión: Computer Use detuvo el acceso al navegador al no poder verificar la URL de Brave.

Si solo la carpeta de fotos devuelve un error 500.19 tras la carga, revisar si el alojamiento bloquea la sección `staticContent`. Retirar únicamente el nuevo `public/img/productos/web.config` en ese caso permite seguir usando los JPG; solicitar el mapeo WebP al soporte para futuras cargas.

## Verificación local

Las pruebas aisladas de productos usan exclusivamente SQLite en memoria y un directorio temporal para las cargas:

```powershell
php vendor/phpunit/phpunit/phpunit -c phpunit.analytics.xml tests/Feature/ProductoImagesTest.php
```

Resultado: 3 pruebas, 82 aserciones. Se verifican las 19 imágenes JPEG, sus asociaciones, el respaldo sin bucle, el catálogo, el formulario y el guardado de imágenes.

También pasaron las 10 pruebas aisladas existentes de reportes y detalles de ventas (63 aserciones).

Referencia oficial del mapeo MIME de IIS: https://learn.microsoft.com/en-us/iis/configuration/system.webServer/staticContent/mimeMap
