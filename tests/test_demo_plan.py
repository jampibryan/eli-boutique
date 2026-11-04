"""Replay the proposed demo in SQLite before touching SQL Server."""
import copy
import importlib.util
from pathlib import Path
import json
import sqlite3
from contextlib import closing
import unittest

ROOT=Path(__file__).resolve().parents[1]
spec=importlib.util.spec_from_file_location('plan_demo_data',ROOT/'scripts/plan_demo_data.py')
planner=importlib.util.module_from_spec(spec);spec.loader.exec_module(planner)


class DemoPlanTests(unittest.TestCase):
    @classmethod
    def setUpClass(cls):
        # Self-contained fixture; CI must not need private database exports.
        products=[{'id':str(p),'descripcionP':f'Producto {p}','precioP':'25.00','costo_compra':'0.00',
                   'deleted_at':None,'updated_at':'2026-06-11 10:00:00'} for p in range(1,20)]
        stocks=[{'id':str((p-1)*4+s),'producto_id':str(p),'producto_talla_id':str(s),
                 'stock':'0' if (p,s)==(18,1) else '2','updated_at':'2026-06-11 10:00:00'}
                 for p in range(1,20) for s in range(1,5)]
        cls.baseline={'fingerprint':'fixture','data':{'productos':products,'stocks':stocks,
            'maximos':[{'venta':'0','compra':'0','caja':'1','ultima_venta':'2026-05-30'}],
            'cajas':[{'fecha':'2026-06-11'}], 'ventas_producto':[{'producto_id':str(p),'unidades':'300'} for p in range(1,20)],
            'compras_producto':[{'producto_id':str(p),'proveedor_id':str(1+(p-1)//4),'precio_promedio':'13.75','lineas':'20'} for p in range(1,20)],
            'estados':[{'id':str(i),'descripcionET':name} for i,name in enumerate(['Pendiente','Pagado','Anulado','Borrador','Enviada','Cotizada','Aprobada','Recibida','Pagada'],1)],
            'comprobantes':[{'id':'1','descripcionCOM':'Boleta'},{'id':'2','descripcionCOM':'Factura'}],
            'clientes':[{'id':str(i)} for i in range(1,6)],'colaboradores':[{'id':'1'}]}}
        cls.plan=planner.build_plan(cls.baseline)

    def test_existing_cashbox_is_never_overlapped(self):
        self.assertGreater(self.plan['summary']['desde'],'2026-06-11')
        self.assertNotIn('2026-06-11',[c['fecha'] for c in self.plan['tables']['cajas']])

    def test_deterministic_plan_and_independent_validation(self):
        rebuilt=planner.build_plan(self.baseline)
        self.assertEqual(rebuilt['plan_hash'],self.plan['plan_hash'])
        self.assertGreater(planner.validate_plan(rebuilt,self.baseline)['sales_checked'],1000)

    def test_tampered_cash_and_stock_are_rejected(self):
        for table,field in [('cajas','ingresoDiario'),('movimientos_inventario','stock_nuevo')]:
            bad=copy.deepcopy(self.plan);bad['tables'][table][0][field]=0
            with self.assertRaises(ValueError): planner.validate_plan(bad,self.baseline)

    def test_sqlite_replay_stock_never_reaches_zero_and_matches_final(self):
        with closing(sqlite3.connect(':memory:')) as db:
            db.execute('CREATE TABLE stock(p INTEGER,s INTEGER,q INTEGER CHECK(q>=0),PRIMARY KEY(p,s))')
            db.executemany('INSERT INTO stock VALUES(?,?,?)',[(int(s['producto_id']),int(s['producto_talla_id']),int(s['stock'])) for s in self.baseline['data']['stocks']])
            events=sorted(self.plan['tables']['movimientos_inventario'],key=lambda m:(m['created_at'],m['cantidad']<0))
            for event in events:
                p,s=event['producto_id'],event['producto_talla_id']
                before=db.execute('SELECT q FROM stock WHERE p=? AND s=?',(p,s)).fetchone()[0]
                self.assertEqual(before,event['stock_anterior'])
                db.execute('UPDATE stock SET q=q+? WHERE p=? AND s=?',(event['cantidad'],p,s))
                after=db.execute('SELECT q FROM stock WHERE p=? AND s=?',(p,s)).fetchone()[0]
                self.assertEqual(after,event['stock_nuevo']);self.assertGreaterEqual(after,1)
            self.assertEqual(db.execute('SELECT MIN(q) FROM stock').fetchone()[0],2)
            self.assertEqual(db.execute('SELECT COUNT(*) FROM (SELECT p FROM stock GROUP BY p HAVING SUM(q)<=15)').fetchone()[0],6)


if __name__=='__main__': unittest.main()
