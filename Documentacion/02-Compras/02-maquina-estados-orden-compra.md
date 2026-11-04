# ⚙️ MÁQUINA DE ESTADOS DETERMINISTA DE COMPRAS

> **Módulo:** Compras  
> **Catálogo Rector:** `App\Models\EstadoTransaccion`  
> **Servicio Responsable:** `App\Services\CompraService`

---

## 🚦 1. FLUJO CANÓNICO DE ESTADOS

Para evitar inconsistencias en el inventario o salidas de dinero no autorizadas, cada compra debe atravesar estrictamente las siguientes fases secuenciales:

```mermaid
stateDiagram-v2
    [*] --> Borrador: CompraService::create()
    Borrador --> Enviada: CompraService::send()
    Enviada --> Cotizada: CompraService::saveQuote()
    Cotizada --> Aprobada: CompraService::approve()
    Aprobada --> Recibida: CompraService::receive()
    Recibida --> Pagada: PagoService::register()
    
    Borrador --> Anulada: CompraService::anular()
    Enviada --> Anulada: CompraService::anular()
    Cotizada --> Anulada: CompraService::anular()
    Aprobada --> Anulada: CompraService::anular()
```

---

## 📝 2. DETALLE DE CADA TRANSICIÓN

| Estado | Evento Disparador | Impacto en Stock | Impacto en Caja | Regla de Validación |
| :--- | :--- | :---: | :---: | :--- |
| **1. Borrador** | Creación inicial de la orden. | Cero (0) | Cero (0) | Selecciona proveedor, productos y tallas deseadas. |
| **2. Enviada** | El sistema encola correo al proveedor (`Mail::to()->queue()`). | Cero (0) | Cero (0) | Solo una orden en `Borrador` puede enviarse. |
| **3. Cotizada** | Recepción de la proforma o cotización del proveedor. | Cero (0) | Cero (0) | Se registran precios unitarios cotizados y se adjunta PDF si existe. |
| **4. Aprobada** | Autorización gerencial de los costos cotizados. | Cero (0) | Cero (0) | Bloquea modificaciones a los precios acordados. |
| **5. Recibida** | Llegada física del pedido a tienda o almacén. | **+ Incrementa** | Cero (0) | Obligatorio antes de pagar. El stock de tallas y el Kardex se actualizan aquí. |
| **6. Pagada** | Registro de la cancelación de la orden. | Cero (0) | **- Egreso Diario** | Registra el desembolso y vincula comprobante del pago. |

---

## 🔒 3. REGLAS INQUEBRANTABLES DE NEGOCIO

1. **Inspección Física Obligatoria:** Queda terminantemente prohibido pagar una compra que no haya alcanzado el estado `Recibida`. No se desembolsa dinero de la tienda sin haber contado y verificado físicamente las prendas.
2. **Blindaje de Anulaciones:** Una compra en estado `Recibida` o `Pagada` no puede anularse mediante el botón simple de anulación, debido a que el stock ya fue absorbido en vitrina y el dinero salió de caja. Requiere un protocolo auditado de devolución a proveedor.
