# 📊 IMPORTACIÓN MASIVA DE PRODUCTOS Y STOCK DESDE EXCEL

> **Módulo:** Inventario  
> **Servicio Responsable:** `App\Services\ProductoExcelService`  
> **Librería:** PhpSpreadsheet / Maatwebsite Excel

---

## 📥 1. PROPÓSITO DEL SERVICIO

Permite a los administradores cargar o actualizar cientos de prendas de vestir y sus stocks por tallas mediante una plantilla estandarizada de Microsoft Excel (.xlsx), evitando la digitación manual una por una.

---

## 📑 2. ESTRUCTURA DE LA PLANTILLA EXCEL

El archivo debe contener las siguientes columnas canónicas:

| Columna | Nombre | Tipo | Ejemplo | Descripción |
| :---: | :--- | :--- | :--- | :--- |
| **A** | `CODIGO` | Alfanumérico | `VEST-001` | Código único de la prenda. |
| **B** | `DESCRIPCION`| Texto | `Vestido Corto Seda` | Nombre y detalle del producto. |
| **C** | `CATEGORIA` | Texto | `Vestidos` | Debe coincidir con `categoria_productos`. |
| **D** | `GENERO` | Texto | `Damas` | Debe coincidir con `producto_generos`. |
| **E** | `PRECIO` | Decimal | `89.90` | Precio de venta público. |
| **F** | `COSTO` | Decimal | `45.00` | Costo de adquisición sugerido. |
| **G..K**| `TALLA_XS` a `TALLA_XL` | Entero | `5` | Stock físico disponible por talla. |

---

## 🛡️ 3. PROTOCOLO DE VALIDACIÓN Y ATOMICIDAD TOTAL

Para garantizar que un archivo dañado no corrompa la base de datos de producción:

1. **Transacción Global (`DB::transaction`):**
   * Toda la lectura e inserción del archivo se procesa dentro de una sola transacción.
   * Si en la fila 150 hay un error (ej. precio negativo o formato de celda inválido), **toda la importación se cancela de inmediato (Rollback)**.
   * Ningún registro a medias queda en la base de datos.
2. **Validación de Tallas:**
   * El servicio valida que cada columna de talla corresponda a una talla registrada en la tabla `producto_tallas`.
3. **Upsert Inteligente:**
   * Si el `codigoP` ya existe en el sistema, actualiza su descripción, precio y stock de tallas.
   * Si no existe, crea la prenda y genera sus registros en `producto_talla_stock`.
