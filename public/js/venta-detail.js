(function (root) {
    'use strict';
    function number(value) {
        if (value === null || value === undefined || value === '' || !Number.isFinite(Number(value))) {
            throw new Error('La venta contiene importes incompletos.');
        }
        return Number(value);
    }
    function normalize(payload) {
        const sale = payload && payload.success === true ? payload.data : payload;
        if (!sale || !sale.id || !sale.codigoVenta || !Array.isArray(sale.detalles)) {
            throw new Error('La respuesta no contiene el detalle de la venta.');
        }
        const date = new Date(sale.created_at);
        if (!sale.created_at || Number.isNaN(date.getTime())) throw new Error('Fecha de venta no válida.');
        const client = sale.cliente;
        return {
            id: sale.id, code: sale.codigoVenta, date: date.toLocaleString('es-PE'),
            clientName: client ? [client.nombreCliente, client.apellidoCliente].filter(Boolean).join(' ') : 'Cliente no disponible',
            document: client ? `${client.tipo_documento || 'DNI'}: ${client.dniCliente || 'Sin documento'}` : 'Documento no disponible',
            receipt: sale.pago?.comprobante?.descripcionCOM || 'Sin comprobante',
            invoiceName: sale.razon_social_factura, invoiceRuc: sale.ruc_factura,
            subtotal: number(sale.subTotal), igv: number(sale.IGV), total: number(sale.montoTotal),
            items: sale.detalles.map(item => ({
                name: item.producto?.descripcionP || 'Producto no disponible',
                size: item.talla?.descripcion || 'Sin talla', quantity: number(item.cantidad),
                price: number(item.precio_unitario), total: number(item.subtotal)
            }))
        };
    }
    async function load(url, signal) {
        const response = await fetch(url, {signal, credentials: 'same-origin',
            headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'}});
        if (!response.ok) {
            if (response.status === 401) throw new Error('Tu sesión terminó. Vuelve a iniciar sesión.');
            if (response.status === 403) throw new Error('No tienes permiso para consultar esta venta.');
            throw new Error('No se pudo consultar la venta. Vuelve a intentarlo.');
        }
        return normalize(await response.json());
    }
    function render(sale, printUrl, doc) {
        const text = (id, value) => { doc.getElementById(id).textContent = value; };
        const money = value => 'S/ ' + value.toFixed(2);
        text('drawerCodigoVenta', '#' + sale.code); text('drawerFechaVenta', sale.date);
        const invoice = sale.receipt.toLowerCase() === 'factura';
        const name = invoice && sale.invoiceName ? sale.invoiceName : sale.clientName;
        text('drawerClienteNombre', name);
        text('drawerClienteDni', invoice && sale.invoiceRuc ? 'RUC: ' + sale.invoiceRuc : sale.document);
        text('drawerAvatar', name.charAt(0).toUpperCase());
        const container = doc.getElementById('drawerItemsList'); container.replaceChildren();
        let quantity = 0;
        for (const item of sale.items) {
            quantity += item.quantity;
            const card = doc.createElement('div'); card.className = 'receipt-item-card d-flex justify-content-between align-items-center';
            const description = doc.createElement('div');
            const title = doc.createElement('strong'); title.className = 'd-block text-dark'; title.textContent = item.name;
            const size = doc.createElement('small'); size.className = 'text-muted'; size.textContent = `Talla: ${item.size} | Cant: ${item.quantity}`;
            description.append(title, size);
            const amounts = doc.createElement('div'); amounts.className = 'text-end';
            const total = doc.createElement('strong'); total.className = 'd-block text-dark'; total.textContent = money(item.total);
            const price = doc.createElement('small'); price.className = 'text-muted'; price.textContent = money(item.price) + ' c/u';
            amounts.append(total, price); card.append(description, amounts); container.append(card);
        }
        if (!sale.items.length) text('drawerItemsList', 'No hay ítems registrados.');
        text('drawerCantProductos', quantity); text('drawerSubtotal', money(sale.subtotal));
        text('drawerIgv', money(sale.igv)); text('drawerTotal', money(sale.total));
        text('drawerComprobanteTipo', sale.receipt); doc.getElementById('drawerBtnImprimir').href = printUrl;
    }
    const api = {normalize, load, render};
    if (typeof module !== 'undefined' && module.exports) module.exports = api;
    else root.VentaDetail = api;
})(typeof window === 'undefined' ? globalThis : window);
