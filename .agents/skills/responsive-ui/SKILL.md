---
name: responsive-ui
description: Guía de diseño responsivo y adaptabilidad para Eli Boutique con Tailwind CSS, Bootstrap 5 y Blade para Punto de Venta (POS), tablets y escritorios.
---

# 📱 RESPONSIVE UI - SKILL (ELI BOUTIQUE)

> **Activación:** `skill:responsive-ui` o al editar vistas Blade y componentes frontend  
> **Propósito:** Garantizar que los formularios de ventas, carrito de compras, catálogo de productos y reportes se visualicen óptimamente en pantallas de escritorio, laptops y tablets de mostrador.

---

## 📐 BREAKPOINTS Y ADAPTABILIDAD

| Dispositivo | Ancho | Estrategia de Layout |
| :--- | :---: | :--- |
| **Móvil / POS compacto** | `< 768px` | Grillas de 1 columna (`col-12`), botones táctiles grandes, menús colapsables. |
| **Tablet / Mostrador** | `768px - 1024px` | Grillas de 2 columnas (`col-md-6`), carrito visible o flotante, inputs accesibles. |
| **Desktop / Laptop** | `> 1024px` | Grillas de 3 a 4 columnas (`col-lg-4` o `col-xl-3`), tablas con paginación limpia. |

---

## 🎨 PRINCIPIOS DE ESTILO BOUTIQUE

1. **Paleta de Colores Institucional:**
   - Dorado Boutique: `#D4AF37`
   - Carbón / Negro Elegante: `#2C2C2C`
   - Fondo Suave: `#F8F9FA`
   - Éxito / Pagado: `#28a745`
   - Alerta / Pendiente: `#ffc107`
   - Error / Anulado: `#dc3545`
2. **Badges de Estado Claros:**
   - Estados de transacciones siempre visibles con insignias redondas (`status-badge`).
3. **Formularios Intuitivos:**
   - Selectores de tallas con feedback visual inmediato del stock disponible antes de agregar al carrito.
