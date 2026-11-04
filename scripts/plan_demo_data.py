"""Deterministic demonstration scenario. No network or database writes."""
from collections import defaultdict, Counter
from datetime import date, datetime, timedelta
from decimal import Decimal, ROUND_HALF_UP
from pathlib import Path
import argparse
import hashlib
import json
import random


def money(cents):
    return f"{Decimal(cents) / 100:.2f}"


def cents(value):
    return int((Decimal(str(value)) * 100).quantize(Decimal('1'), rounding=ROUND_HALF_UP))


def ref(table, key):
    return f"@{table}:{key}"


def stamp(day, hour, minute=0, second=0):
    return f"{day.isoformat()}T{hour:02}:{minute:02}:{second:02}"


def build_plan(baseline, cutoff=date(2026, 10, 15), seed=20261015):
    source = baseline['data']
    rng = random.Random(seed)
    dates = [str(v)[:10] for v in source['maximos'][0].values() if v and '-' in str(v)]
    dates += [row['fecha'][:10] for row in source['cajas']]
    latest = max(dates)
    start = date.fromisoformat(latest) + timedelta(days=1)
    if start > cutoff:
        raise ValueError('El histórico ya llega al corte; no se agregarán registros duplicados.')
    days = [start + timedelta(days=n) for n in range((cutoff-start).days+1)
            if (start + timedelta(days=n)).weekday() != 6]
    products = {int(p['id']): p for p in source['productos'] if p['deleted_at'] is None}
    stock_rows = [s for s in source['stocks'] if int(s['producto_id']) in products]
    variants = defaultdict(list)
    for s in stock_rows:
        variants[int(s['producto_id'])].append(int(s['producto_talla_id']))
    if any(not variants[p] for p in products):
        raise ValueError('Hay productos sin matriz de tallas; se requiere revisarlos antes de simular.')
    quantities = {int(r['producto_id']): int(r['unidades']) for r in source['ventas_producto']}
    weights = [0.8 * quantities.get(p, 100) + 0.2 * 350 for p in products]
    prices = {p: cents(row['precioP']) for p, row in products.items()}
    suppliers, costs = {}, {}
    for p in products:
        candidates = [r for r in source['compras_producto'] if int(r['producto_id']) == p and r['precio_promedio']]
        if not candidates:
            raise ValueError(f'Producto {p} sin referencia de costo de compras.')
        chosen = max(candidates, key=lambda r: int(r['lineas']))
        suppliers[p] = int(chosen['proveedor_id'])
        costs[p] = cents(chosen['precio_promedio'])
        if not 0 < costs[p] < prices[p]:
            raise ValueError(f'Revisar el costo de referencia del producto {p}.')
    states = {r['descripcionET']: int(r['id']) for r in source['estados']}
    receipts = {r['descripcionCOM'].lower(): int(r['id']) for r in source['comprobantes']}
    clients = [int(r['id']) for r in source['clientes']]
    staff = [int(r['id']) for r in source['colaboradores']]
    tables = {t: [] for t in ['cajas','compras','ventas','compra_detalles','venta_detalles','pagos','movimientos_inventario']}
    sales_by_day, demand = defaultdict(list), defaultdict(Counter)
    sale_code = int(source['maximos'][0]['venta'] or 0)
    # Generate demand first. Variation is synthetic and not an empirical seasonality claim.
    for day in days:
        monthly = {6:0.94,7:1.10,8:1.00,9:0.97,10:1.08}.get(day.month,1)
        n_sales = max(5, round(rng.triangular(7, 21, 12) * monthly * (1.18 if day.weekday()==5 else 1)))
        for index in range(n_sales):
            chosen = []
            for _ in range(rng.choices([1,2,3], [0.56,0.34,0.10])[0]):
                available = [p for p in products if p not in chosen]
                chosen.append(rng.choices(available, [weights[list(products).index(p)] for p in available])[0])
            lines = []
            for p in chosen:
                size = rng.choices(variants[p], [0.19,0.31,0.30,0.20][:len(variants[p])])[0]
                qty = rng.choices([1,2,3],[0.77,0.20,0.03])[0]
                lines.append((p,size,qty)); demand[day][p,size] += qty
            # Ordered by time later; these timestamps are synthetic, never stopwatch evidence.
            seconds = rng.randint(10*3600,18*3600+20*60)
            when = datetime.combine(day,datetime.min.time()) + timedelta(seconds=seconds)
            sales_by_day[day].append({'code':None, 'lines':lines, 'when':when,
                'client':rng.choice(clients), 'staff':rng.choice(staff) if staff else None})
        sales_by_day[day].sort(key=lambda sale:sale['when'])
        for sale in sales_by_day[day]:
            sale_code += 1
            sale['code']=f'{sale_code:07}'

    low_products = set(p for p in [3,9,13,14,16,18] if p in products)
    target, initial = {}, {}
    for s in stock_rows:
        p, size = int(s['producto_id']), int(s['producto_talla_id'])
        initial[p,size] = int(s['stock'])
        target[p,size] = rng.randint(2,3) if p in low_products else rng.randint(6,11)
    totals = Counter()
    for v in demand.values(): totals.update(v)
    allowance = {key: totals[key] + target[key] - initial[key] for key in initial}
    if min(allowance.values()) < 0:
        raise ValueError('El inventario inicial supera la demanda del período para alguna talla; revisar el escenario.')
    stock = initial.copy()
    purchase_code = int(source['maximos'][0]['compra'] or 0)
    cash_code = int(source['maximos'][0]['caja'] or 0)
    new_units = Counter()
    for day_index, day in enumerate(days):
        cash_code += 1
        cash_key = day.isoformat()
        cash = {'_key':cash_key,'codigoCaja':f'{cash_code:06}','fecha':cash_key,
                'hora_cierre':'19:00:00','clientesHoy':0,'productosVendidos':0,
                'ingresoDiario':'0.00','egresoDiario':'0.00',
                'created_at':stamp(day,8),'updated_at':stamp(day,19)}
        tables['cajas'].append(cash)
        replenishments = defaultdict(list)
        triggered_suppliers = set()
        for key in stock:
            p, size = key
            next_two = sum(demand[d][key] for d in days[day_index:day_index+3])
            near_end = len(days)-day_index <= 7
            reserve = target[key] if near_end else 2
            if stock[key] < next_two + reserve and allowance[key] > 0:
                triggered_suppliers.add(suppliers[p])
        # Consolidate the supplier's variants in one order instead of ordering a single
        # missing size every day. Coverage stays around ten business days.
        for key in stock:
            p, size = key
            if suppliers[p] in triggered_suppliers and allowance[key] > 0:
                reserve = target[key] if len(days)-day_index<=7 else 2
                coverage = sum(demand[d][key] for d in days[day_index:day_index+10])
                qty = min(allowance[key], max(0,coverage+reserve-stock[key]))
                if not qty: continue
                replenishments[suppliers[p]].append((p,size,qty))
                allowance[key] -= qty
        expense = 0
        for supplier, lines in sorted(replenishments.items()):
            purchase_code += 1
            code = f'{purchase_code:07}'
            order_day = days[max(0,day_index-1)]
            base = sum(costs[p]*qty for p,size,qty in lines)
            tax = int((Decimal(base)*Decimal('.18')).quantize(Decimal('1'),rounding=ROUND_HALF_UP))
            total = base + tax
            tables['compras'].append({'_key':code,'codigoCompra':code,
                'numero_documento_proveedor':f'DEMO-{code}','tipo_documento_proveedor':'DEMOSTRACION',
                'proveedor_id':supplier,'comprobante_id':receipts.get('factura',next(iter(receipts.values()))),
                'colaborador_id':rng.choice(staff) if staff else None,'estado_transaccion_id':states['Pagada'],
                'fecha_envio':order_day.isoformat(),'fecha_cotizacion':order_day.isoformat(),
                'fecha_aprobacion':order_day.isoformat(),'fecha_entrega_estimada':day.isoformat(),
                'subtotal':money(base),'descuento':'0.00','igv':money(tax),'total':money(total),
                'notas_proveedor':'DATOS DE DEMOSTRACION: reposición simulada, no operación comercial real.',
                'condiciones_pago':'Pago simulado al recibir','dias_credito':0,
                'created_at':stamp(order_day,8,5),'updated_at':stamp(day,18,40)})
            tables['pagos'].append({'_key':f'compra-{code}','venta_id':None,'compra_id':ref('compras',code),
                'comprobante_id':receipts.get('factura',next(iter(receipts.values()))),
                'importe':money(total),'vuelto':None,'metodo_pago':'transferencia',
                'referencia_pago':f'DEMO-C-{code}','created_at':stamp(day,18,40),'updated_at':stamp(day,18,40)})
            expense += total
            for p,size,qty in lines:
                before = stock[p,size]; stock[p,size] += qty
                tables['compra_detalles'].append({'_key':f'{code}-{p}-{size}', 'compra_id':ref('compras',code),
                    'producto_id':p,'producto_talla_id':size,'cantidad':qty,'precio_cotizado':money(costs[p]),
                    'precio_final':money(costs[p]),'descuento_unitario':'0.00','subtotal_linea':money(costs[p]*qty),
                    'created_at':stamp(order_day,8,15),'updated_at':stamp(day,8,45)})
                tables['movimientos_inventario'].append({'producto_id':p,'producto_talla_id':size,
                    'tipo_movimiento':'INGRESO_COMPRA','cantidad':qty,'costo_unitario':money(costs[p]),
                    'precio_unitario':money(prices[p]),'stock_anterior':before,'stock_nuevo':stock[p,size],
                    'origen_tipo':'App\\Models\\Compra','origen_id':ref('compras',code),
                    'referencia_documento':f'DEMO Compra {code}','user_id':None,
                    'motivo':'DEMO: recepción simulada; no evidencia de operación real.',
                    'created_at':stamp(day,8,45),'updated_at':stamp(day,8,45)})
        income, clients_day, units_day = 0,set(),0
        for sale in sales_by_day[day]:
            code, when = sale['code'], sale['when']
            paid = when + timedelta(seconds=rng.randint(60,240))
            base_total = sum(prices[p]*q for p,s,q in sale['lines'])
            taxable = int((Decimal(base_total)/Decimal('1.18')).quantize(Decimal('1'),rounding=ROUND_HALF_UP))
            tables['ventas'].append({'_key':code,'codigoVenta':code,'caja_id':ref('cajas',cash_key),
                'cliente_id':sale['client'],'colaborador_id':sale['staff'],'estado_transaccion_id':states['Pagado'],
                'subTotal':money(taxable),'IGV':money(base_total-taxable),'montoTotal':money(base_total),
                'created_at':when.isoformat(),'updated_at':paid.isoformat()})
            method = rng.choices(['efectivo','yape','plin','transferencia'],[.52,.27,.15,.06])[0]
            tender = ((base_total+999)//1000)*1000 if method=='efectivo' else base_total
            tables['pagos'].append({'_key':f'venta-{code}','venta_id':ref('ventas',code),'compra_id':None,
                'comprobante_id':receipts.get('boleta',next(iter(receipts.values()))),
                'importe':money(tender),'vuelto':money(tender-base_total),'metodo_pago':method,
                'referencia_pago':f'DEMO-V-{code}','created_at':paid.isoformat(),'updated_at':paid.isoformat()})
            income += base_total; clients_day.add(sale['client'])
            for p,size,qty in sale['lines']:
                before = stock[p,size]; stock[p,size] -= qty
                if stock[p,size] < 1:
                    raise ValueError(f'Quiebre de stock: {day}, producto {p}, talla {size}.')
                unit_base = int((Decimal(prices[p])/Decimal('1.18')).quantize(Decimal('1'),rounding=ROUND_HALF_UP))
                tables['venta_detalles'].append({'_key':f'{code}-{p}-{size}','venta_id':ref('ventas',code),
                    'producto_id':p,'producto_talla_id':size,'cantidad':qty,'precio_unitario':money(prices[p]),
                    'costo_unitario':money(costs[p]),'base_imponible':money(unit_base),
                    'igv':money(prices[p]-unit_base),'subtotal':money(prices[p]*qty),
                    'created_at':when.isoformat(),'updated_at':when.isoformat()})
                tables['movimientos_inventario'].append({'producto_id':p,'producto_talla_id':size,
                    'tipo_movimiento':'SALIDA_VENTA','cantidad':-qty,'costo_unitario':money(costs[p]),
                    'precio_unitario':money(prices[p]),'stock_anterior':before,'stock_nuevo':stock[p,size],
                    'origen_tipo':'App\\Models\\Venta','origen_id':ref('ventas',code),
                    'referencia_documento':f'DEMO Venta {code}','user_id':None,
                    'motivo':'DEMO: venta simulada; no evidencia de operación real.',
                    'created_at':when.isoformat(),'updated_at':when.isoformat()})
                units_day += qty; new_units[p] += qty
        cash.update(clientesHoy=len(clients_day),productosVendidos=units_day,
                    ingresoDiario=money(income),egresoDiario=money(expense))
    if stock != target:
        raise ValueError('El escenario no alcanza la distribución final prevista.')
    stock_updates = [{'id':int(s['id']),'before':int(s['stock']),
        'after':stock[int(s['producto_id']),int(s['producto_talla_id'])],
        'before_updated_at':s['updated_at'],'after_updated_at':stamp(cutoff,19)} for s in stock_rows]
    product_updates = [{'id':p,'before':str(row['costo_compra']),'after':money(costs[p])}
                       for p,row in products.items() if Decimal(row['costo_compra'])<=0]
    final_products = [{'producto_id':p,'producto':row['descripcionP'],
        'stock_final':sum(q for (pid,size),q in stock.items() if pid==p),
        'vendidas_nuevas':new_units[p],'vendidas_acumuladas':quantities.get(p,0)+new_units[p],
        'costo_referencia':money(costs[p])} for p,row in products.items()]
    summary = {'batch_id':f'demo-hasta-{cutoff.isoformat()}-s{seed}', 'desde':days[0].isoformat(),
        'hasta':cutoff.isoformat(),'dias_operacion':len(days),'seed':seed,'tipo_datos':'DEMOSTRACION',
        'opening_inventory_source':'Foto de inventario existente; saldo de apertura del escenario, no reconstrucción de movimientos reales anteriores.',
        'original_kardex_snapshot':source.get('movements_period',[]),
        'counts':{t:len(v) for t,v in tables.items()},'stock_minimo':min(stock.values()),
        'stock_maximo_por_talla':max(stock.values()),'productos_stock_bajo':[p for p in final_products if p['stock_final']<=15],
        'top_5':sorted(final_products,key=lambda p:p['vendidas_acumuladas'],reverse=True)[:5],
        'productos':final_products,'ingresos':money(sum(cents(c['ingresoDiario']) for c in tables['cajas'])),
        'egresos':money(sum(cents(c['egresoDiario']) for c in tables['cajas']))}
    plan = {'format_version':1,'baseline_fingerprint':baseline['fingerprint'],'summary':summary,
            'tables':tables,'stock_updates':stock_updates,'product_updates':product_updates}
    plan['plan_hash'] = hashlib.sha256(json.dumps(plan,sort_keys=True,ensure_ascii=False).encode()).hexdigest()
    return plan


def validate_plan(plan, baseline):
    """Independent chronological replay and accounting checks before any database write."""
    stock = {(int(s['producto_id']),int(s['producto_talla_id'])):int(s['stock']) for s in baseline['data']['stocks']}
    events = sorted(plan['tables']['movimientos_inventario'],key=lambda m:(m['created_at'],m['cantidad']<0))
    for event in events:
        key = event['producto_id'],event['producto_talla_id']
        if stock[key] != event['stock_anterior']: raise ValueError('Cadena de inventario interrumpida.')
        stock[key] += event['cantidad']
        if stock[key] != event['stock_nuevo'] or stock[key]<1: raise ValueError('Inventario inválido.')
    details = defaultdict(int)
    for d in plan['tables']['venta_detalles']:
        if cents(d['subtotal']) != cents(d['precio_unitario'])*d['cantidad']: raise ValueError('Subtotal inválido.')
        details[d['venta_id']] += cents(d['subtotal'])
    net = {p['venta_id']:cents(p['importe'])-cents(p['vuelto'] or 0)
           for p in plan['tables']['pagos'] if p['venta_id']}
    for sale in plan['tables']['ventas']:
        key=ref('ventas',sale['_key']); total=cents(sale['montoTotal'])
        if details[key]!=total or net[key]!=total or cents(sale['subTotal'])+cents(sale['IGV'])!=total:
            raise ValueError('Venta, pago e impuestos no coinciden.')
    low = [p for p in plan['summary']['productos'] if p['stock_final']<=15]
    if not 4<=len(low)<=6: raise ValueError('Cantidad de productos con stock bajo no prevista.')
    if any(row['stock_final']>50 for row in plan['summary']['productos']): raise ValueError('Sobreinventario final.')
    if len({s['codigoVenta'] for s in plan['tables']['ventas']}) != len(plan['tables']['ventas']): raise ValueError('Códigos duplicados.')
    if len(plan['tables']['compras']) > len(plan['tables']['ventas']) / 10:
        raise ValueError('Demasiadas órdenes: consolidar las reposiciones por proveedor.')
    # Simulated receipts/tender are reconciled by operation date, net of change.
    for cash in plan['tables']['cajas']:
        day=cash['fecha']
        paid=[p for p in plan['tables']['pagos'] if p['created_at'][:10]==day]
        income=sum(cents(p['importe'])-cents(p['vuelto'] or 0) for p in paid if p['venta_id'])
        expense=sum(cents(p['importe']) for p in paid if p['compra_id'])
        if income!=cents(cash['ingresoDiario']) or expense!=cents(cash['egresoDiario']): raise ValueError('Caja descuadrada.')
    return {'inventory_events_checked':len(events),'sales_checked':len(net),'cashboxes_checked':len(plan['tables']['cajas'])}


if __name__=='__main__':
    parser=argparse.ArgumentParser()
    parser.add_argument('--baseline',default='storage/app/demo-2026/baseline.json')
    parser.add_argument('--output',default='storage/app/demo-2026/plan.json')
    args=parser.parse_args()
    baseline=json.loads(Path(args.baseline).read_text(encoding='utf-8-sig'))
    plan=build_plan(baseline)
    checks=validate_plan(plan,baseline)
    Path(args.output).write_text(json.dumps(plan,ensure_ascii=False,indent=2),encoding='utf-8')
    Path(args.output).with_name('preview.json').write_text(json.dumps({'summary':plan['summary'],'checks':checks},ensure_ascii=False,indent=2),encoding='utf-8')
    print(json.dumps({'summary':plan['summary'],'checks':checks},ensure_ascii=True,indent=2))
