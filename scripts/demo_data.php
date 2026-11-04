<?php

// Controlled demo data tooling. Uses its own transaction, never sends mail or calls SUNAT.
require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/demo_data_writer.php';
// Match the Laravel application's timezone when invoked outside its bootstrap.
date_default_timezone_set('America/Lima');

function demoConnection(): PDO
{
    $env = Dotenv\Dotenv::parse(file_get_contents(__DIR__.'/../.env'));
    if (($env['DB_CONNECTION'] ?? '') !== 'sqlsrv') {
        throw new RuntimeException('Se esperaba la conexión SQL Server revisada.');
    }
    return new PDO('sqlsrv:Server='.$env['DB_HOST'].','.$env['DB_PORT'].';Database='.$env['DB_DATABASE']
        .';Encrypt='.($env['DB_ENCRYPT'] ?? 'yes').';TrustServerCertificate='.($env['DB_TRUST_SERVER_CERTIFICATE'] ?? 'false')
        .';LoginTimeout=15', $env['DB_USERNAME'], $env['DB_PASSWORD'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
}

function demoExport(PDO $pdo): array
{
    $queries = [
        'productos' => 'SELECT id,codigoP,descripcionP,precioP,costo_compra,deleted_at,updated_at FROM productos ORDER BY id',
        'stocks' => 'SELECT id,producto_id,producto_talla_id,stock,updated_at FROM producto_talla_stock ORDER BY id',
        'tallas' => 'SELECT id,descripcion FROM producto_tallas ORDER BY id',
        'clientes' => 'SELECT id FROM clientes WHERE deleted_at IS NULL ORDER BY id',
        'proveedores' => 'SELECT id FROM proveedores WHERE deleted_at IS NULL ORDER BY id',
        'colaboradores' => 'SELECT id FROM colaboradores ORDER BY id',
        'estados' => 'SELECT id,descripcionET FROM estado_transacciones ORDER BY id',
        'comprobantes' => 'SELECT id,descripcionCOM FROM comprobantes ORDER BY id',
        'ventas_producto' => "SELECT d.producto_id,SUM(d.cantidad) unidades FROM venta_detalles d JOIN ventas v ON v.id=d.venta_id JOIN estado_transacciones e ON e.id=v.estado_transaccion_id WHERE e.descripcionET='Pagado' GROUP BY d.producto_id ORDER BY d.producto_id",
        'compras_producto' => 'SELECT d.producto_id,c.proveedor_id,AVG(d.precio_final) precio_promedio,MAX(c.created_at) ultima_compra,COUNT(*) lineas FROM compra_detalles d JOIN compras c ON c.id=d.compra_id GROUP BY d.producto_id,c.proveedor_id ORDER BY d.producto_id,c.proveedor_id',
        'cajas' => 'SELECT id,codigoCaja,fecha,ingresoDiario,egresoDiario FROM cajas ORDER BY id',
        'maximos' => 'SELECT (SELECT MAX(codigoVenta) FROM ventas) venta,(SELECT MAX(codigoCompra) FROM compras) compra,(SELECT MAX(codigoCaja) FROM cajas) caja,(SELECT MAX(created_at) FROM ventas) ultima_venta,(SELECT MAX(created_at) FROM compras) ultima_compra,(SELECT MAX(created_at) FROM pagos) ultimo_pago',
        'counts' => 'SELECT (SELECT COUNT(*) FROM ventas) ventas,(SELECT COUNT(*) FROM venta_detalles) venta_detalles,(SELECT COUNT(*) FROM compras) compras,(SELECT COUNT(*) FROM compra_detalles) compra_detalles,(SELECT COUNT(*) FROM pagos) pagos,(SELECT COUNT(*) FROM movimientos_inventario) movimientos_inventario',
        'identities' => "SELECT t.name tabla,c.name columna,c.is_identity FROM sys.tables t JOIN sys.columns c ON c.object_id=t.object_id WHERE c.name='id' AND t.name IN ('cajas','compras','compra_detalles','ventas','venta_detalles','pagos','movimientos_inventario') ORDER BY t.name",
        'movements_period' => 'SELECT tipo_movimiento,COUNT(*) cantidad,MIN(created_at) desde,MAX(created_at) hasta FROM movimientos_inventario GROUP BY tipo_movimiento ORDER BY tipo_movimiento',
    ];
    $data = [];
    foreach ($queries as $key => $sql) {
        $data[$key] = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    return ['fingerprint' => hash('sha256', json_encode($data, JSON_THROW_ON_ERROR)), 'data' => $data];
}

function demoWriteJson(string $path, array $data): void
{
    if (!is_dir(dirname($path))) { mkdir(dirname($path), 0777, true); }
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
}

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    try {
        $pdo = demoConnection();
        $command = $argv[1] ?? '';
        if ($command === 'export' && isset($argv[2])) {
            $export = demoExport($pdo);
            demoWriteJson($argv[2], $export);
            echo json_encode(['saved' => $argv[2], 'fingerprint' => $export['fingerprint'],
                'counts' => $export['data']['counts'], 'products' => $export['data']['productos'],
                'maximos' => $export['data']['maximos'], 'identities' => $export['data']['identities'],
                'movements_period'=>$export['data']['movements_period']], JSON_UNESCAPED_UNICODE), PHP_EOL;
        } elseif ($command === 'snapshot' && isset($argv[2])) {
            $container = new Illuminate\Container\Container();
            $connection = new Illuminate\Database\SqlServerConnection($pdo, (string)$pdo->query('SELECT DB_NAME()')->fetchColumn(), '', ['driver' => 'sqlsrv']);
            $container->instance('db', $connection);
            $container->instance('db.schema', $connection->getSchemaBuilder());
            Illuminate\Support\Facades\DB::setFacadeApplication($container);
            $snapshot = (new App\Services\AnalyticsSnapshotService())->snapshot();
            demoWriteJson($argv[2], $snapshot);
            echo json_encode(['saved' => $argv[2], 'datasets' => array_map('count', $snapshot['data'])]), PHP_EOL;
        } elseif (in_array($command,['apply','verify'],true) && isset($argv[2])) {
            $plan = json_decode(file_get_contents($argv[2]),true,512,JSON_THROW_ON_ERROR);
            $result = $command==='apply' ? demoApply($pdo,$plan) : demoCheck($pdo,$plan);
            if (isset($argv[3])) { demoWriteJson($argv[3],$result); }
            echo json_encode($result,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR),PHP_EOL;
        } elseif ($command==='rollback' && isset($argv[2])) {
            echo json_encode(demoRollback($pdo,$argv[2]),JSON_THROW_ON_ERROR),PHP_EOL;
        } elseif ($command==='rollback-check' && isset($argv[2])) {
            $result=demoRollback($pdo,$argv[2],true);
            if(isset($argv[3])) { demoWriteJson($argv[3],$result); }
            echo json_encode($result,JSON_THROW_ON_ERROR),PHP_EOL;
        } elseif ($command==='ledger-schema') {
            $pdo->beginTransaction();
            try {
                demoCreateLedger($pdo);
                $pdo->exec("INSERT INTO migrations(migration,batch) SELECT '2026_11_04_000000_create_demo_data_ledger',COALESCE(MAX(batch),0)+1 FROM migrations WHERE NOT EXISTS (SELECT 1 FROM migrations WHERE migration='2026_11_04_000000_create_demo_data_ledger') HAVING COUNT(*)>0");
                $pdo->commit();echo "Control de esquema del lote registrado.\n";
            } catch(Throwable $e) { if($pdo->inTransaction()){$pdo->rollBack();}throw $e; }
        } else {
            throw new RuntimeException('Comando no disponible.');
        }
    } catch (Throwable $e) {
        fwrite(STDERR, 'No se completó la operación. Clase: '.get_class($e).'. Código: '.$e->getCode().' '.($e instanceof RuntimeException && !$e instanceof PDOException ? $e->getMessage() : 'Transacción revertida si estaba abierta; no se muestran datos de conexión.').PHP_EOL);
        exit(1);
    }
}
