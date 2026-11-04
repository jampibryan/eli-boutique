# Demostración hasta el 15 de octubre de 2026 y evaluación del modelo

La [corrección posterior de comprobantes y lista de pendientes](Estado-y-Pendientes-2026-11-04.md)
documenta el segundo lote reversible. Para retirar ambos lotes, primero se revierte el de facturas.

La carga autorizada se aplicó a SQL Server el 4 de noviembre de 2026. Los registros se identifican como demostración y no acreditan operaciones comerciales reales ni mediciones de la tesis.

## Registros añadidos

El lote `demo-hasta-2026-10-15-s20261015` cubre del 2026-06-12 al 2026-10-15. Se inició después de la caja ya existente del 11 de junio, sin sobrescribirla. No se borraron registros operativos anteriores.

| Entidad | Añadidos | Total después de la carga |
| --- | ---: | ---: |
| Ventas | 1538 | 4057 |
| Detalles de ventas | 2385 | 7257 |
| Compras | 70 | 414 |
| Detalles de compras | 860 | 2436 |
| Pagos | 1608 | 4471 |
| Cajas | 108 | 317 |
| Movimientos de inventario | 3245 | 3321 |

Se consolidaron las reposiciones por proveedor con una cobertura orientativa de diez días de operación. Las cantidades, fechas, clientes, colaboradores y medios de pago varían dentro del escenario reproducible con semilla 20261015. Los tiempos de cobro son sintéticos y no son mediciones de desempeño.

## Inventario final

Hay 480 unidades en 19 productos. Todas las 76 variantes activas terminan con stock positivo: mínimo 2 y máximo 11 unidades por talla. Los productos fuera del umbral bajo terminan entre 28 y 37 unidades.

| Producto con stock mínimo | Unidades finales |
| --- | ---: |
| Polo manga larga | 11 |
| Jeans boyfriend | 9 |
| Bermudas de lino | 10 |
| Chaqueta denim | 9 |
| Abrigo trench | 10 |
| Leggings deportivos | 11 |

Se completó `costo_compra` de los 19 productos que lo tenían en cero con su referencia de compras existente, sin cambiar precios de venta ni costos de detalles históricos anteriores. Los valores previos de catálogo y stock se conservaron para reversión.

## Top cinco acumulado

| Producto | Unidades vendidas |
| --- | ---: |
| Polo clásico cuello pique | 761 |
| Camiseta básica algodón | 736 |
| Sudadera con capucha | 698 |
| Polo manga larga | 638 |
| Chaqueta bomber | 616 |

## Conciliación y reversión

El inventario se simuló cronológicamente en una copia SQLite antes de cargar. Las verificaciones transaccionales y posteriores en SQL Server encontraron cero tallas activas no positivas, cero diferencias de cabecera contra detalles y cero diferencias de pagos contra caja. Los cobros se conciliaron netos de vuelto.

`demo_data_batches` identifica el lote y `demo_data_records` registra cada fila añadida y cada cambio de valores anteriores. La prueba de reversión verificó 9909 registros sin borrar ni revertir nada. Repetir la carga devuelve `already_applied` y no duplica datos.

Para verificar o revertir exclusivamente este lote, desde la raíz de Laravel:

```powershell
php scripts/demo_data.php verify storage/app/demo-2026/plan.json
php scripts/demo_data.php rollback-check demo-hasta-2026-10-15-s20261015
php scripts/demo_data.php rollback demo-hasta-2026-10-15-s20261015
```

La reversión real no se ejecutó. El comando se niega a borrar si las filas controladas tuvieron modificaciones posteriores; las restricciones de la base también impiden romper referencias. Conserva las tablas de control y su historial. No usar borrados generales ni reiniciar identidades.

La primera carga detectó la caja del 11 de junio y fue revertida por completo antes de aplicar el plan ajustado. Las identidades pueden tener saltos normales por esa reversión; no se resecuenciaron.

## Alcance del Kardex

El inventario anterior incluye 76 registros `INVENTARIO_INICIAL` capturados el 20 de octubre de 2026. Se conservaron sin modificar. El nuevo lote utiliza la foto de existencias como saldo de apertura de su escenario y registra su propia cadena de entradas y salidas. Esto no constituye una reconstrucción cronológica del Kardex real previo: no sumar o interpretar ambas fechas de captura como evidencia de operación histórica observada.

## Entrenamiento y evaluación

La red se entrenó una sola vez durante 9 épocas, con parada temprana, y se guardó separada del modelo antiguo. La normalización se ajustó solo al entrenamiento.

| Partición | Fechas | Filas diarias |
| --- | --- | ---: |
| Entrenamiento | hasta 2026-06-23 | 4314 |
| Validación | 2026-06-24 a 2026-08-19 | 931 |
| Prueba | 2026-08-20 a 2026-10-15 | 931 |

| Métrica de prueba | Valor |
| --- | ---: |
| MAE de red, unidades por producto y día | 1.17098 |
| MAE de referencia histórica | 1.13064 |
| RMSE del método activo | 1.51529 |
| WAPE diario del método activo | 73.56% |
| WAPE agregado por producto/mes dentro de la ventana de prueba | 23.95% |

La validación favoreció ligeramente a la red, pero la prueba final favoreció a la referencia. Por esa razón el panel utiliza la referencia reciente por producto y día de semana; la red queda guardada para comparación. No se volvió a entrenar para mejorar artificialmente las métricas. La prueba intervino como decisión de habilitación, no para ajustar pesos.

El error diario es elevado respecto a cantidades pequeñas: no se afirma precisión alta ni se interpreta `1 - WAPE` como exactitud. Estas métricas incluyen registros de demostración y no validan demanda futura real.

Los modelos están en `D:/TESIS/eli_boutique_streamlit/modelos/20261104-corte20261015`. `active_model.json` selecciona esa versión. El modelo y los escaladores antiguos de la raíz se conservaron intactos.

## Streamlit local

El panel dispone de ventas, inventario, compras, pagos, caja y proyección financiera. Se actualiza mientras está abierto, sin entrenar. La fecha de corte es explícita; con corte al 15/10 se pronostica noviembre. Los meses incompletos no inflan promedios y los productos sin historial no reciben una demanda mínima inventada. El margen usa ventas sin IGV y costo base, separado de cobros con IGV y de beneficio neto.

El modo local consulta el lector privado de Laravel, que mantiene las credenciales en su propio `.env`. Publicación, URL HTTPS, token de Cloud, commit y push siguen pendientes. No se enviaron documentos a SUNAT ni correos a proveedores.

Los planes, respaldos de referencia y resultados de verificación están en `storage/app/demo-2026/`, ignorado por Git. Los comandos de control deben conservar esa carpeta para revisar o repetir la verificación del lote.
