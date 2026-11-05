<?php

namespace App\Services;

use App\Models\{Caja, Cliente, Compra, Producto, Proveedor, User, Venta};
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

/** Lecturas: no guarda modelos ni dispara eventos de ventas, compras, stock o caja. */
class MobileReadService
{
    private const MODELS = ['clientes' => Cliente::class, 'productos' => Producto::class,
        'ventas' => Venta::class, 'compras' => Compra::class, 'proveedores' => Proveedor::class, 'cajas' => Caja::class];

    public function listing(string $module, array $input): array
    {
        $query = $this->query($module, false);
        $columns = match ($module) {
            'clientes' => ['nombreCliente', 'apellidoCliente', 'dniCliente'],
            'productos' => ['codigoP', 'descripcionP'], 'ventas' => ['codigoVenta'],
            'compras' => ['codigoCompra'], 'proveedores' => ['nombreEmpresa', 'RUC'], 'cajas' => ['codigoCaja'],
        };
        if (!empty($input['q'])) {
            $term = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $input['q']).'%';
            $query->where(function ($q) use ($columns, $term, $module) {
                foreach ($columns as $column) { $q->orWhere($column, 'like', $term); }
                if ($module === 'ventas') { $q->orWhereHas('cliente', fn ($c) => $c
                    ->where('nombreCliente', 'like', $term)->orWhere('apellidoCliente', 'like', $term)); }
                if ($module === 'compras') { $q->orWhereHas('proveedor', fn ($p) => $p->where('nombreEmpresa', 'like', $term)); }
            });
        }
        if (in_array($module, ['ventas', 'compras'], true) && !empty($input['state'])) {
            $query->whereHas('estadoTransaccion', fn ($q) => $q->where('descripcionET', $input['state']));
        }
        $date = $module === 'cajas' ? 'fecha' : 'created_at';
        if (!empty($input['from'])) { $query->whereDate($date, '>=', $input['from']); }
        if (!empty($input['to'])) { $query->whereDate($date, '<=', $input['to']); }
        $page = $query->orderByDesc('id')->paginate($input['per_page'] ?? 25, ['*'], 'page', $input['page'] ?? 1);
        return ['success' => true, 'data' => array_map(fn ($row) => $this->serialize($row), $page->items()),
            'meta' => ['page' => $page->currentPage(), 'last_page' => $page->lastPage(),
                'total' => $page->total(), 'per_page' => $page->perPage(), 'generated_at' => now()->toIso8601String()]];
    }

    public function detail(string $module, int $id): array
    {
        return $this->serialize($this->query($module, true)->findOrFail($id));
    }

    private function query(string $module, bool $detail)
    {
        abort_unless(isset(self::MODELS[$module]), 404);
        $query = self::MODELS[$module]::query();
        $relations = match ($module) {
            'clientes' => ['tipoGenero'], 'productos' => ['categoriaProducto', 'productoGenero', 'tallaStocks.talla'],
            'ventas' => $detail ? ['cliente', 'estadoTransaccion', 'pago.comprobante', 'detalles.producto', 'detalles.talla']
                : ['cliente', 'estadoTransaccion', 'pago.comprobante'],
            'compras' => $detail ? ['proveedor', 'estadoTransaccion', 'comprobante', 'pago', 'detalles.producto', 'detalles.talla']
                : ['proveedor', 'estadoTransaccion', 'comprobante'],
            'cajas' => $detail ? ['ventas.cliente', 'ventas.estadoTransaccion'] : [], default => [],
        };
        return $query->with($relations);
    }

    private function serialize($model): array
    {
        $data = $model->toArray();
        if ($model instanceof Producto) {
            $data['stock_total'] = $model->stockTotal;
            // Respetar el catálogo de imágenes actual y las nuevas fotos cargadas desde la web.
            $data['imagenP'] = $model->imagen_url ?? $model->imagenP;
        }
        if ($model instanceof Caja) { $data['balance_diario'] = (float) $model->ingresoDiario - (float) $model->egresoDiario; }
        // PDO SQL Server puede devolver BIGINT como string. Mantener documentos y códigos como texto.
        return $this->normalize($data);
    }

    private function normalize(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) { $data[$key] = $this->normalize($value); }
            elseif ($value !== null && ($key === 'id' || str_ends_with((string) $key, '_id')
                || in_array($key, ['cantidad', 'stock', 'stock_total', 'dias_credito', 'clientesHoy', 'productosVendidos'], true))) {
                $data[$key] = (int) $value;
            }
        }
        return $data;
    }

    public function dashboard(User $user): array
    {
        $modules = app(MobileAccess::class)->modules($user);
        $box = in_array('cajas', $modules) ? Caja::where('fecha', now()->toDateString())->first() : null;
        return ['fecha' => now()->toDateString(), 'generated_at' => now()->toIso8601String(),
            'caja' => $box ? $this->serialize($box) : null,
            'ventas_hoy' => in_array('ventas', $modules) ? Venta::whereDate('created_at', now()->toDateString())
                ->whereHas('estadoTransaccion', fn ($q) => $q->where('descripcionET', 'Pagado'))->whereHas('pago')->count() : 0,
            'total_clientes' => in_array('clientes', $modules) ? Cliente::count() : null,
            'total_productos' => in_array('productos', $modules) ? Producto::count() : null,
            'total_proveedores' => in_array('proveedores', $modules) ? Proveedor::count() : null,
            'modules' => $modules];
    }

    public function charts(array $input): array
    {
        $latest = DB::table('ventas')->max('created_at');
        $to = CarbonImmutable::parse($input['to'] ?? ($latest ?: now()->toDateString()))->endOfDay();
        $from = CarbonImmutable::parse($input['from'] ?? $to->subDays(29)->toDateString())->startOfDay();
        abort_if($from->gt($to), 422, 'La fecha inicial debe ser anterior a la final.');
        abort_if($from->diffInDays($to) > 366, 422, 'Selecciona como máximo un año.');
        $sales = DB::table('ventas as v')->join('estado_transacciones as e', 'e.id', '=', 'v.estado_transaccion_id')
            ->where('e.descripcionET', 'Pagado')->whereExists(fn ($q) => $q->selectRaw('1')->from('pagos')->whereColumn('pagos.venta_id', 'v.id'))
            ->whereBetween('v.created_at', [$from->toDateTimeString(), $to->toDateTimeString()]);
        $dayExpression = DB::getDriverName() === 'sqlite' ? 'date(v.created_at)' : 'CAST(v.created_at AS DATE)';
        $byDay = (clone $sales)->selectRaw($dayExpression.' AS dia, SUM(v.montoTotal) AS monto, COUNT(*) AS cantidad')
            ->groupByRaw($dayExpression)->orderBy('dia')->get();
        $top = (clone $sales)->join('venta_detalles as d', 'd.venta_id', '=', 'v.id')
            ->leftJoin('productos as p', 'p.id', '=', 'd.producto_id')->selectRaw('d.producto_id, p.descripcionP AS nombre, SUM(d.cantidad) AS unidades')
            ->groupBy('d.producto_id', 'p.descripcionP')->orderByDesc('unidades')->orderBy('d.producto_id')->limit(5)->get();
        $products = Producto::with('tallaStocks')->get();
        $low = $products->filter(fn ($p) => $p->stockTotal <= 15)->sortBy(fn ($p) => $p->stockTotal)->values();
        $cash = DB::table('pagos as p')->leftJoin('ventas as v', 'v.id', '=', 'p.venta_id')
            ->leftJoin('compras as c', 'c.id', '=', 'p.compra_id')
            ->leftJoin('estado_transacciones as ev', 'ev.id', '=', 'v.estado_transaccion_id')
            ->leftJoin('estado_transacciones as ec', 'ec.id', '=', 'c.estado_transaccion_id')
            ->whereBetween('p.created_at', [$from->toDateTimeString(), $to->toDateTimeString()])
            ->where(fn ($q) => $q->where('ev.descripcionET', 'Pagado')->orWhere('ec.descripcionET', 'Pagada'))
            ->get(['p.venta_id', 'p.importe', 'p.vuelto']);
        $income = $cash->whereNotNull('venta_id')->sum(fn ($p) => (float) $p->importe - (float) $p->vuelto);
        $outcome = $cash->whereNull('venta_id')->sum(fn ($p) => (float) $p->importe - (float) $p->vuelto);
        return ['from' => $from->toDateString(), 'to' => $to->toDateString(), 'generated_at' => now()->toIso8601String(),
            'ventas' => (int) $byDay->sum('cantidad'), 'monto_ventas' => round((float) $byDay->sum('monto'), 2),
            'ingresos' => round($income, 2), 'egresos' => round($outcome, 2), 'balance' => round($income - $outcome, 2),
            'unidades_stock' => $products->sum(fn ($p) => $p->stockTotal),
            'serie_ventas' => $byDay->map(fn ($r) => ['fecha' => substr($r->dia, 0, 10), 'monto' => (float) $r->monto, 'ventas' => (int) $r->cantidad])->all(),
            'top_productos' => $top->map(fn ($r) => ['id' => (int) $r->producto_id, 'nombre' => $r->nombre ?? 'Producto histórico', 'unidades' => (int) $r->unidades])->all(),
            'stock_minimo' => $low->map(fn ($p) => ['id' => (int) $p->id, 'nombre' => $p->descripcionP, 'stock' => $p->stockTotal])->all()];
    }
}
