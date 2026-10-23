# 📚 MAPA MAESTRO DE DOCUMENTACIÓN - ELI BOUTIQUE ERP

> **Versión del Sistema:** 2.0.0 (Arquitectura 360° Retail ERP - Cero Silos)  
> **Huso Horario Oficial:** Perú (America/Lima - UTC-5)  
> **Base de Datos:** Relacional con Transacciones ACID (`DB::transaction`) y Bloqueos Pesimistas (`lockForUpdate`)  
> **Fuente Única de Verdad (SSOT):** `producto_talla_stock` (Stock Físico) | `cajas` (Flujo de Efectivo) | `movimientos_inventario` (Kardex Valorizado)

---

## 🗺️ 1. VISIÓN TRANSVERSAL Y FILOSOFÍA DEL SISTEMA

Eli Boutique es una solución integral de gestión empresarial diseñada para el sector retail de moda y confección. Su arquitectura opera bajo el principio de **Cero Silos de Información**, lo que significa que ninguna operación comercial o administrativa ocurre de forma aislada:

```text
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                                   ARQUITECTURA 360°                                    │
└────────────────────────────────────────────────────────────────────────────────────────┘
          [02. Compras / Proveedores] ──(Recepción de Mercadería)──┐
                                                                   ▼
       ┌───────────────────────────────┐                  ┌─────────────────┐
       │ 03. Inventario Tallas (SSOT)  │ ◄───────────────┤ Kardex Almacén  │
       └──────────────┬────────────────┘                  └─────────────────┘
                      │ (Reserva Concurrente)                      ▲
                      ▼                                            │
          [01. Ventas / POS Express] ──────(Salida de Stock)───────┘
                      │
                      ▼ (Cobro Multimedio)
          [04. Caja Diaria / Arqueo] ───(Emisión Inmutable)───► [Comprobantes PDF]
                      │
                      ▼ (Auditoría Forense)
       [05. Reportes / Dashboard]  ◄─── [06. Seguridad, Roles y Permisos Spatie]
```

---

## 📁 2. ÍNDICE GENERAL DE MÓDULOS

La documentación se encuentra organizada modularmente en subcarpetas especializadas:

| Carpeta Módulo | Propósito Central | Documentos Clave |
| :--- | :--- | :--- |
| [📁 01-Ventas](file:///c:/Disco%20D/PROYECTOS/eli-boutique/Documentacion/01-Ventas/) | Punto de venta (POS), reserva de tallas, emisión de boletas/facturas térmicas y devoluciones. | Visión general, POS express, reserva concurrente, comprobantes PDF, reversiones. |
| [📁 02-Compras](file:///c:/Disco%20D/PROYECTOS/eli-boutique/Documentacion/02-Compras/) | Abastecimiento a proveedores, cotizaciones, recepción física con Kardex y liquidación. | Ciclo de compras, máquina de estados, recepción de prendas, pagos al proveedor, costos. |
| [📁 03-Inventario-y-Kardex](file:///c:/Disco%20D/PROYECTOS/eli-boutique/Documentacion/03-Inventario-y-Kardex/) | Matriz multitalla, kardex valorizado, importación masiva en Excel y control de mermas. | Matriz de tallas, movimientos de almacén, importación Excel, prevención de quiebres. |
| [📁 04-Caja-Diaria](file:///c:/Disco%20D/PROYECTOS/eli-boutique/Documentacion/04-Caja-Diaria/) | Cuadratura de caja, control de efectivo físico vs digital (Yape/Tarjetas) y arqueo. | Ciclo operativo, ecuación invariante de arqueo, medios de pago, egresos. |
| [📁 05-Reportes-y-Auditoria](file:///c:/Disco%20D/PROYECTOS/eli-boutique/Documentacion/05-Reportes-y-Auditoria/) | Analítica comercial con Chart.js, reportes ejecutivos en PDF y trazabilidad de logs. | Dashboards gráficos, informes de caja, reportes consolidados, auditoría de logs. |
| [📁 06-Seguridad-y-Maestros](file:///c:/Disco%20D/PROYECTOS/eli-boutique/Documentacion/06-Seguridad-y-Maestros/) | Control de acceso Spatie, asignación de colaboradores y gestión de entidades maestras. | Roles y permisos, colaboradores y comisiones, validación de clientes/proveedores. |

---

## 🔒 3. REGLAS DE ORO TRANSVERSALES (INVARIANTES)

1. **Atomicidad Transaccional:** Toda mutación de stock o dinero DEBE encapsularse en `DB::transaction()`. Si una sola operación falla, el sistema retrocede al estado original sin registrar registros huérfanos.
2. **Stock Siempre por Talla:** En moda, una blusa talla S no es intercambiable con una talla L. Jamás existe stock genérico a nivel de producto; el stock real reside en `producto_talla_stock`.
3. **Inmutabilidad de Cajas Históricas:** Una caja cerrada es legal y contablemente inmutable. Si ocurre una devolución de una venta pasada, el egreso se imputa a la caja abierta de hoy con motivo documentado.
4. **Registro Silencioso en Kardex:** Cada incremento o decremento de stock genera un asiento de auditoría en `movimientos_inventario` capturando el costo unitario para el cálculo del margen comercial.
