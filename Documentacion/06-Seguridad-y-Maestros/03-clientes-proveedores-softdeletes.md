# 🏢 CLIENTES, PROVEEDORES Y PROTECCIÓN CON SOFT DELETES

> **Módulo:** Maestros / Integridad Referencial  
> **Modelos:** `App\Models\Cliente`, `App\Models\Proveedor`  
> **Trait Eloquent:** `Illuminate\Database\Eloquent\SoftDeletes`

---

## 📇 1. VALIDACIÓN ESTRICTA DE DOCUMENTOS PERUANOS

Tanto clientes como proveedores están sujetos a validación de identidad oficial:
* **DNI (Persona Natural):** 8 dígitos numéricos exactos (`regex:/^[0-9]{8}$/`).
* **RUC (Persona Jurídica o con Negocio):** 11 dígitos numéricos exactos (`regex:/^[0-9]{11}$/`), comenzando típicamente en 10 (natural con negocio) o 20 (empresa/sociedad).

---

## 🛡️ 2. EL PRINCIPIO DE PRESERVACIÓN HISTÓRICA (`SoftDeletes`)

En un sistema ERP de retail, **nunca se debe borrar permanentemente a un cliente o proveedor que tenga historial**:
* Si un proveedor confeccionista se retira del mercado y se elimina en el sistema:
  * Las compras que se le hicieron hace 6 meses **deben seguir siendo consultables**.
  * Si se hiciera un `DELETE` en cascada, las compras y la auditoría de caja desaparecerían, provocando descuadres contables o errores `500 Server Error`.

### La Solución Técnica en Eloquent:
1. Las tablas `clientes` y `proveedores` cuentan con la columna `deleted_at`.
2. Las relaciones en los modelos históricos utilizan obligatoriamente `->withTrashed()`:
   ```php
   public function cliente()
   {
       return $this->belongsTo(Cliente::class)->withTrashed();
   }

   public function proveedor()
   {
       return $this->belongsTo(Proveedor::class)->withTrashed();
   }
   ```
3. El registro queda oculto para nuevas operaciones, pero **todos los reportes pasados, comprobantes y balances de caja continúan funcionando al 100% sin romperse**.
