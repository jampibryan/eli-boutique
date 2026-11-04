# Integración de Laravel y Streamlit

El [estado actualizado y los puntos 11 y 12](Estado-y-Pendientes-2026-11-04.md) incluyen
la corrección posterior del detalle de ventas, clientes empresa y distribución de comprobantes.

Laravel es la fuente de los registros operativos. Streamlit consulta una API de lectura
y ejecuta inferencias con un modelo guardado. Registrar operaciones, consultar datos y
entrenar un modelo son procesos independientes.

La ampliación posterior de los puntos 4 a 10 está documentada en
[carga de demostración y evaluación del modelo](Demostracion-y-Modelo-2026-10-15.md).
Los recuentos siguientes corresponden al estado anterior a esa carga.

## Estado revisado el 4 de noviembre de 2026

- Revisión local: `1c84c05`, sin cambios locales previos en Laravel.
- La auditoría SELECT en SQL Server encontró cero migraciones locales pendientes.
- La base conserva `2026_06_11_140000_create_venta_documentos_sunat_table`, ausente del
  código actual. Se conserva sin cambios porque SUNAT está en espera.
- Se contaron 2519 ventas, 344 compras, 19 productos y 2863 pagos.
- Las ventas abarcan del 1 de octubre de 2025 al 30 de mayo de 2026.
- No se encontraron stocks negativos ni diferencias de cabecera contra suma de detalles.
- Los 19 productos activos tienen `costo_compra <= 0`. No es evidencia de adquisición gratuita:
  la proyección de margen queda pendiente de completar costos verificables.
- Esta revisión no certifica la concurrencia del POS ni corrige todos sus flujos. El servicio
  de creación de ventas todavía recibe el total del cliente; esa revisión sigue pendiente.
- No se ejecutaron migraciones, seeders, ventas, pagos, entrenamiento ni envíos SUNAT.
- El contrato se comprobó con SELECT contra la base real: 4872 detalles de venta,
  19 productos, 1576 detalles de compra, 2863 pagos y 209 cajas; cantidades serializadas como enteros.

La auditoría se puede repetir con `php scripts/audit_streamlit_database.php`.
`--check-snapshot` consulta además el servicio analítico real, sin emitir tokens ni crear sesiones.
Nunca muestra contraseñas o datos personales. Los resultados corresponden al momento de consulta.

## Contrato de lectura versión 1.0

`GET /api/analitica/v1/snapshot`

Requiere `Authorization: Bearer ...`, habilidad Sanctum `read` y el permiso de usuario
`ver reportes gráficos`. Utilizar un token de un usuario dedicado con ese permiso cuando
se prepare el despliegue. El token se configura en Secrets de Streamlit; nunca se incluye en Git.

La respuesta contiene `success`, `schema_version`, `generated_at`, `revision` y `data`.
La revisión es un hash del contenido; cambia con las operaciones, correcciones o eliminaciones,
sin cambiar únicamente por consultar a otra hora. No es una revisión transaccional de SQL Server:
las tablas se leen mediante varias consultas y podría haber una operación concurrente entre ellas.

| Colección | Granularidad y propósito | Regla |
| --- | --- | --- |
| `ventas` | Una fila por detalle: IDs, producto/talla, fecha de venta, unidades, precio y costo históricos, subtotal | Solo estado `Pagado` con pago registrado. Excluye pendientes y anuladas; conserva productos eliminados lógicamente. Fuente del entrenamiento de demanda. |
| `productos` | Un producto, precio y costo actuales, estado activo, stock total y matriz por talla | Stock derivado de `producto_talla_stock`. El costo actual nunca reemplaza el costo histórico vendido. |
| `compras` | Una fila por detalle: orden, producto/talla, fecha de orden, estado, cantidad y precio final | Incluye estados para distinguir lo solicitado de lo recibido. La fecha de orden no es una fecha de recepción. |
| `pagos` | Un pago: tipo, transacción, fecha, método, importe, vuelto y monto neto | `monto_neto = importe - vuelto`. `vigente` distingue pagos de operaciones actualmente pagadas; no trata órdenes impagas como egresos. |
| `cajas` | Una caja diaria: fecha, cierre, ingresos y egresos | Totales registrados por el sistema; no se presentan como un conteo físico de efectivo. |

Los IDs y cantidades son números enteros JSON; los importes son números JSON, nunca texto
dependiente del controlador SQL Server. Las fechas son ISO `YYYY-MM-DD` y pueden ser nulas
si faltan en el origen; el cliente rechaza ventas sin fecha válida. No se incluyen clientes,
correos, DNI, RUC, contactos ni referencias bancarias. Los importes se exponen en PEN.
La API no modifica stock, caja, pagos ni estados. No reemplaza las rutas de la app móvil.

## Compatibilidad con la integración anterior

`GET /api/obtener-datos-ventas` permanece disponible para no interrumpir el despliegue
existente antes de configurar el token. Es el contrato `legacy`: solo ventas, sin compras,
inventario, costos ni garantías de filtrado por estado. No se utiliza para un entrenamiento nuevo.
El cierre de este acceso público debe coordinarse después de migrar el consumidor.

Streamlit acepta `LARAVEL_API_BASE_URL`, `LARAVEL_DATA_CONTRACT`, `LARAVEL_API_TOKEN` y
`MODEL_DIR`, mediante variables de entorno o Secrets. El modo predeterminado sigue siendo
`legacy` para conservar el comportamiento de conexión existente. Para el contrato completo
hay que seleccionar explícitamente `snapshot`. Un error de autenticación nunca provoca una
degradación silenciosa al endpoint público.

## Separación de responsabilidades en Streamlit

- `settings.py`: URL, modo, token y directorio del modelo. Solo permite enviar tokens
  por HTTPS, salvo localhost para desarrollo.
- `data_sources.py`: consultas GET, validación del contrato, ventas normalizadas y fuentes operativas.
- `model_artifacts.py`: carga del modelo y escaladores; admite el `.h5` existente sin entrenamiento.
- `prediction.py`: inferencia. Conserva la estimación acotada existente, con productos únicos,
  días reales del mes y promedio separado por año y mes. Su precisión requiere evaluación posterior.
- `app.py`: presentación. Reutiliza el modelo y recalcula al cambiar datos, modelo o mes objetivo.
- `training.py` / `training_pipeline.py`: comando explícito, jamás importado por el dashboard.
  Guarda en un directorio nuevo el modelo, escaladores, métricas y huellas de integridad.

Las fuentes de compras, pagos y caja ya tienen contrato y cliente; sus paneles específicos
se implementarán en el siguiente paso. La predicción sigue teniendo ventas como objetivo.
No se ha ejecutado el nuevo entrenamiento ni sustituido el modelo existente.

La caché de datos dura hasta cinco minutos cuando se vuelve a ejecutar la consulta, con
botón de sincronización manual. El TTL no es un proceso de actualización en segundo plano.
La actualización periódica de una pantalla abierta corresponde al siguiente paso.

## Verificación aislada

```powershell
php vendor/phpunit/phpunit/phpunit -c phpunit.analytics.xml --no-logging --do-not-cache-result
```

La configuración fuerza SQLite `:memory:` y el test bloquea las migraciones antes de
ejecutarse si la conexión no es SQLite en memoria. No usar la configuración general
de pruebas contra la base remota. En Streamlit, `python -B -m unittest discover -s tests -v`
verifica consulta, errores de autenticación, tipos, meses, carga del panel y sincronización
sin entrenamiento. `python training.py --help` tampoco carga TensorFlow ni consulta Laravel.

## Pendiente para funcionar por internet

Publicar Laravel con HTTPS, configurar el token y URL pública en Streamlit Cloud y publicar
los cambios del repositorio Streamlit. No se han creado credenciales, hecho commits/push,
publicado Laravel ni cambiado la configuración de Cloud durante este trabajo.
