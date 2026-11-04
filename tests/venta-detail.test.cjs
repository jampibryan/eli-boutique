const {test} = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const sandbox = {module:{exports:{}},fetch:(...args)=>global.fetch(...args)};
vm.runInNewContext(fs.readFileSync(require('node:path').join(__dirname,'../public/js/venta-detail.js'),'utf8'),sandbox);
const detail = sandbox.module.exports;
const fixture = () => ({id:1,codigoVenta:'0000001',created_at:'2026-10-15T12:00:00-05:00',
    subTotal:'100.00',IGV:'18.00',montoTotal:'118.00',
    cliente:{nombreCliente:'Empresa <prueba>',apellidoCliente:'',tipo_documento:'RUC',dniCliente:'20999000001'},
    pago:{comprobante:{descripcionCOM:'Factura'}},razon_social_factura:'Empresa congelada',ruc_factura:'20999000001',
    detalles:[{cantidad:'2',precio_unitario:'59.00',subtotal:'118.00',talla:{descripcion:'M'},producto:{descripcionP:'Prenda'}}]});
test('web and wrapped mobile contracts expose the same historical values', () => {
    const sale=detail.normalize(fixture());
    assert.equal(JSON.stringify(sale),JSON.stringify(detail.normalize({success:true,data:fixture()})));
    assert.equal(sale.total,118);assert.equal(sale.items[0].price,59);assert.equal(sale.items[0].size,'M');
    assert.equal(sale.items[0].quantity,2);assert.equal(sale.document,'RUC: 20999000001');
});
test('authentication errors and malformed data never become NaN or a fake deleted client', () => {
    for(const data of [{message:'Unauthenticated.'},{success:false},null]) assert.throws(()=>detail.normalize(data));
    for(const amount of [undefined,null,'','NaN','abc']) assert.throws(()=>detail.normalize({...fixture(),montoTotal:amount}));
});
test('a missing historical relation still preserves amounts and item prices', () => {
    const sale=detail.normalize({...fixture(),cliente:null});
    assert.equal(sale.clientName,'Cliente no disponible');assert.equal(sale.total,118);
});
test('failed requests reject before rendering', async () => {
    const original=global.fetch;
    try {
        global.fetch=async()=>({ok:false,status:401});
        await assert.rejects(detail.load('/ventas/1'),/sesión terminó/);
    } finally { global.fetch=original; }
});
test('drawer renders snapshot amounts, RUC and size using text nodes', () => {
    class Element {
        constructor(){this.children=[];this.textContent='';}
        append(...values){this.children.push(...values);}
        replaceChildren(...values){this.children=[...values];}
        set innerHTML(value){throw new Error('Untrusted HTML must not be inserted');}
    }
    const nodes={};const doc={getElementById:id=>nodes[id]??(nodes[id]=new Element()),createElement:()=>new Element()};
    detail.render(detail.normalize(fixture()),'/ventas/1/comprobante',doc);
    assert.equal(nodes.drawerTotal.textContent,'S/ 118.00');
    assert.equal(nodes.drawerClienteNombre.textContent,'Empresa congelada');
    assert.equal(nodes.drawerClienteDni.textContent,'RUC: 20999000001');
    assert.equal(nodes.drawerCantProductos.textContent,2);
    assert.equal(nodes.drawerBtnImprimir.href,'/ventas/1/comprobante');
});
