# 📊 REPORTES GRÁFICOS Y ANALÍTICA CON CHART.JS

> **Módulo:** Reportes  
> **Controlador:** `App\Http\Controllers\ReporteGraficoController`  
> **Motor Gráfico:** Chart.js  
> **Vistas Blade:** `resources/views/ReporteGrafico/`

---

## 📈 1. PROPÓSITO DEL DASHBOARD COMERCIAL

El módulo de reportes gráficos transforma los registros transaccionales en inteligencia visual para la toma de decisiones gerenciales en Eli Boutique:

1. **Tendencia de Ventas Diarias y Mensuales:** Visualización de picos de venta según días festivos (Día de la Madre, Fiestas Patrias, Campaña Navideña).
2. **Top 5 Prendas Más Vendidas:** Análisis de rotación de vestidos, blusas, pantalones y conjuntos.
3. **Distribución por Categorías y Género:** Proporción de ingresos generados por líneas de damas, caballeros o accesorios.
4. **Relación Ingresos vs. Egresos:** Comparativa entre el dinero recaudado por ventas y el dinero invertido en compras a proveedores.

---

## 💻 2. ARQUITECTURA DE DATOS PARA GRÁFICOS

Para evitar cuellos de botella en la base de datos al renderizar dashboards:
* Se utilizan agregaciones directas mediante SQL (`SUM`, `COUNT`, `groupBy`).
* Se consumen endpoints asíncronos vía AJAX/JSON que alimentan las instancias de Chart.js sin necesidad de recargar la página completa.
* Se implementan filtros dinámicos por rango de fechas (mes actual, trimestre, año).
