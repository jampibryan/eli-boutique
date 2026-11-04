# Estado de Eli Boutique y trabajo pendiente

## Plan original de doce puntos

| Punto | Trabajo | Estado |
| --- | --- | --- |
| 1 | Revisar Laravel tras el pull y su correspondencia con SQL Server | Realizado; contrastar nuevamente migraciones al desplegar. |
| 2 | Separar entrenamiento, consulta e inferencia de Streamlit | Realizado. El panel no entrena. |
| 3 | Definir contrato de ventas, productos/tallas, compras, pagos y cajas | Realizado; API autenticada y lector local disponibles. |
| 4 | Corregir cálculos, fechas, promedios y actualización del panel | Realizado; actualización cada 60 segundos con el panel abierto. |
| 5 | Completar demostración hasta el 15/10/2026 | Realizado: 1.538 ventas, 70 compras y 108 cajas añadidas. |
| 6 | Variar demanda y consolidar reposiciones | Realizado en la simulación reproducible. |
| 7 | Simular stock y conciliar pagos/caja antes de cargar | Realizado y comprobado después de cargar. |
| 8 | Conservar stock positivo y mostrar stock mínimo/top cinco | Realizado: 480 unidades, seis productos bajos y 76 variantes positivas. |
| 9 | Carga identificada, idempotente y reversible | Realizado; el lote de facturas posterior se revierte primero si se desea retirar ambos lotes. |
| 10 | Entrenar una versión, evaluar por fechas y guardar artefactos | Realizado: red guardada; referencia histórica activa por su menor error de prueba. No equivale a alta precisión sobre demanda real. |
| 11 | Publicar Laravel y conectar Streamlit Cloud | Pendiente. Ver lista siguiente. |
| 12 | Verificar recorrido completo y finalizar Flutter | Pendiente después del despliegue. Ver lista siguiente. |

## Punto 11: publicación y conexión por internet

1. Revisar y guardar los cambios locales de los dos repositorios, excluyendo `.env`, secretos, respaldos y exportaciones privadas. No se ha hecho commit ni push.
2. Preparar Laravel para el alojamiento contratado: versión/extensiones PHP, SQL Server, dependencias Composer y recursos compilados, directorio público, HTTPS, permisos de almacenamiento y configuración de producción. Confirmar también los datos del negocio que se imprimen en los comprobantes.
3. Mantener la base ya existente en SmartASP.NET; respaldarla antes del despliegue y revisar el estado de migraciones. No ejecutar seeders ni comandos que reinicien la base.
4. Crear un usuario/token de consulta para la API analítica, con habilidad `read` y el permiso `ver reportes gráficos`. Configurarlo como secreto, sin publicarlo en Git.
5. Configurar Streamlit Cloud con `LARAVEL_DATA_CONTRACT = "snapshot"`, la URL HTTPS de Laravel y el token. El modo `local_laravel` funciona únicamente en el equipo que tiene PHP y el proyecto local.
6. Subir los cambios y los artefactos seleccionados al repositorio/rama que use Streamlit Cloud y revisar el arranque del despliegue. Guardar archivos localmente no actualiza la aplicación pública.
7. Comprobar desde internet el contrato protegido y los cinco conjuntos de datos. Después de migrar el consumidor, cerrar o proteger el endpoint público antiguo de ventas.

## Punto 12: recorrido completo y aplicación móvil

1. Probar de forma controlada apertura de caja, venta, pago, boleta/factura interna, consulta y cierre.
2. Probar compra, aprobación, recepción y pago; verificar propagación a stock por talla y egresos.
3. Comprobar anulación/devolución aplicable y preservación de relaciones históricas, evitando afectar operaciones reales durante la prueba.
4. Confirmar que Streamlit refleja operaciones nuevas y correcciones mediante sincronización, manteniendo idénticos los archivos del modelo.
5. Configurar Flutter con esa misma URL HTTPS, su autenticación y conversión de tipos numéricos. Probar listados y detalles en un dispositivo fuera de la red local.

## Otros pendientes que se mantienen

- SUNAT sigue en espera. Los comprobantes generados aquí son internos; no se ha activado emisión electrónica ni validación de los RUC ficticios de demostración.
- Contrastar la versión final de la tesis con el método realmente activo, las métricas y la procedencia de los datos. No presentar registros sintéticos como operaciones observadas ni sus tiempos como mediciones experimentales.
- Completar y validar costos históricos faltantes si se requiere calcular rentabilidad histórica; los costos actuales no sustituyen los de las ventas antiguas.
- Evaluar el modelo con actividad real futura antes de atribuirle precisión de pronóstico o decidir otro entrenamiento.
- Conservar los planes y registros de reversión locales; no ejecutar borrados generales ni reiniciar identidades.

## Corrección adicional de ventas y empresas

El panel lateral esperaba una venta en la raíz de una respuesta que la API móvil envuelve en `data`; además usaba `precioUnitario` y `descripcionTalla`, mientras el modelo entrega `precio_unitario` y `descripcion`. Se cambió a la ruta web autenticada, con validación de la respuesta, manejo de errores, cancelación de consultas anteriores y representación segura de textos. La API móvil conserva su contrato.

Clientes ahora admite DNI de ocho dígitos o RUC de once. Se conserva `dniCliente` como columna histórica del número de documento; `tipo_documento` identifica su significado. `nombreCliente` contiene el nombre de la persona o la razón social de la empresa, sin duplicar ese dato en otra columna. Las empresas usan el catálogo «No aplica» para el género. Se añadió dirección al cliente y su copia histórica en la factura.

En SQL Server se sustituyeron los índices únicos de correo/teléfono por índices únicos filtrados para valores no nulos. Así varios clientes pueden omitir esos datos, sin permitir duplicados cuando están informados.

Antes del cambio había 3.397 boletas y 660 facturas; las facturas existentes tenían RUC y razón social históricos válidos en cuanto a formato y presencia, sin verificación tributaria. El lote nuevo afecta exclusivamente 180 ventas del lote de demostración: diez empresas ficticias, dieciocho facturas por empresa. La carga quedó aplicada: 3.217 boletas y 840 facturas, sin cambiar la cantidad de ventas ni importes. Se controlan 476 filas entre altas de clientes y cambios de ventas, pagos y contadores de caja.

Un primer intento se revirtió por las restricciones de contactos vacíos. Un segundo intento perdió la conexión antes de completar; una lectura posterior confirmó los recuentos originales y ninguna empresa nueva. La carga final se realizó mediante consultas agrupadas y verificó antes del commit las huellas de stock, detalles, compras, movimientos e importes. Las identidades pueden tener saltos por transacciones revertidas; no se reiniciaron.

Comandos de comprobación y reversión, desde la raíz de Laravel:

```powershell
php scripts/demo_invoices.php audit
php scripts/demo_invoices.php verify
php scripts/demo_invoices.php rollback-check
# Solo si se desea retirar las empresas y la redistribución de comprobantes:
php scripts/demo_invoices.php rollback
```

No se ha ejecutado la reversión. Si luego se desea retirar el lote de ventas/compras original, primero debe revertirse este lote de facturas; los controles del lote anterior detectan sus modificaciones y evitan una reversión incorrecta. Los planes y resultados privados se guardan en `storage/app/demo-invoices/`, excluido de Git.

El aviso principal de Streamlit se trasladó al desplegable «Procedencia de los datos» en «Datos y modelo». Se conserva la trazabilidad en la API y el registro de cargas. El panel de compras/caja muestra el reparto de comprobantes. Esta presentación no convierte las operaciones de demostración en actividad comercial observada.

## Verificación final

- 10 pruebas de Laravel (63 aserciones), 5 del panel JavaScript, 15 de Streamlit y 4 de simulación aprobadas.
- Verificación posterior del lote: huellas de existencias, detalles, compras, movimientos e importes sin cambios; 4.057 ventas, 3.217 boletas, 840 facturas, cero clientes huérfanos y cero tallas activas sin stock positivo.
- `rollback-check` verificó las 476 filas sin revertirlas; una segunda ejecución de `apply` devolvió `already_applied`.
- JSON del controlador web y PDF generados para la venta 4057 (boleta de S/ 20.00) y la 4056 (factura de S/ 235.00). El cliente JavaScript normalizó ambas muestras con sus precios y tallas actuales del contrato. Se revisaron visualmente ambos PDF; se amplió el papel según los datos de facturación para conservar completa la factura en una página. Los títulos indican boleta/factura interna, sin atribuir emisión electrónica a SUNAT.
- Prueba real del panel desde `D:/TESIS/eli_boutique_streamlit`, usando el lector local: actualización manual correcta, recuentos de comprobantes coincidentes, 480 unidades, seis productos bajos y cero agotados. Los archivos antiguos y todos los artefactos de la versión seleccionada conservaron sus hashes. La hora de consulta del lector se alineó a America/Lima.
- No se ha publicado Laravel, cambiado Cloud, ejecutado entrenamiento adicional, enviado documentos ni hecho commit/push.
