<?php

// Only SELECT statements. No Laravel bootstrap, migrations, model events or writes.
require __DIR__.'/../vendor/autoload.php';

$env = Dotenv\Dotenv::parse(file_get_contents(__DIR__.'/../.env'));
if (($env['DB_CONNECTION'] ?? '') !== 'sqlsrv') {
    fwrite(STDERR, "Esta auditoria corresponde a la conexion SQL Server actual.\n");
    exit(1);
}

try {
    $pdo = new PDO(
        'sqlsrv:Server='.$env['DB_HOST'].','.$env['DB_PORT'].';Database='.$env['DB_DATABASE']
        .';Encrypt='.($env['DB_ENCRYPT'] ?? 'yes')
        .';TrustServerCertificate='.($env['DB_TRUST_SERVER_CERTIFICATE'] ?? 'false').';LoginTimeout=15',
        $env['DB_USERNAME'], $env['DB_PASSWORD'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $query = fn ($sql) => $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    $local = array_map(fn ($file) => basename($file, '.php'), glob(__DIR__.'/../database/migrations/*.php'));
    $remote = array_column($query('SELECT migration FROM migrations ORDER BY migration'), 'migration');
    $report = [
        'checked_at' => gmdate(DATE_ATOM),
        'git_head' => trim((string) shell_exec('git rev-parse --short HEAD')),
        'pending_local_migrations' => array_values(array_diff($local, $remote)),
        'database_only_migrations' => array_values(array_diff($remote, $local)),
        'counts' => $query('SELECT (SELECT COUNT(*) FROM ventas) ventas, (SELECT COUNT(*) FROM compras) compras, (SELECT COUNT(*) FROM productos) productos, (SELECT COUNT(*) FROM pagos) pagos'),
        'sales_period' => $query('SELECT MIN(created_at) desde, MAX(created_at) hasta FROM ventas'),
        'negative_stock' => $query('SELECT COUNT(*) cantidad FROM producto_talla_stock WHERE stock < 0'),
        'sale_total_mismatches' => $query('SELECT COUNT(*) cantidad FROM ventas v JOIN (SELECT venta_id, SUM(subtotal) total FROM venta_detalles GROUP BY venta_id) d ON d.venta_id=v.id WHERE ABS(v.montoTotal-d.total)>0.02'),
        'missing_costs' => $query('SELECT COUNT(*) cantidad FROM productos WHERE deleted_at IS NULL AND costo_compra <= 0'),
        'required_columns' => $query("SELECT TABLE_NAME, COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE (TABLE_NAME='productos' AND COLUMN_NAME='costo_compra') OR (TABLE_NAME='venta_detalles' AND COLUMN_NAME='costo_unitario') OR (TABLE_NAME='pagos' AND COLUMN_NAME='metodo_pago') ORDER BY TABLE_NAME"),
    ];
    if (in_array('--check-snapshot', $argv, true)) {
        // Exercise the real read-only service against SQL Server, without booting Laravel
        // or creating an authenticated token/session in the production database.
        $container = new Illuminate\Container\Container();
        $connection = new Illuminate\Database\SqlServerConnection($pdo, $env['DB_DATABASE'], '', ['driver' => 'sqlsrv']);
        $container->instance('db', $connection);
        $container->instance('db.schema', $connection->getSchemaBuilder());
        Illuminate\Support\Facades\DB::setFacadeApplication($container);
        $snapshot = (new App\Services\AnalyticsSnapshotService())->snapshot();
        $report['snapshot_check'] = [
            'schema_version' => $snapshot['schema_version'],
            'revision' => $snapshot['revision'],
            'datasets' => array_map('count', $snapshot['data']),
            'sale_quantity_type' => gettype($snapshot['data']['ventas'][0]['cantidad_vendida'] ?? null),
        ];
    }
    echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR), PHP_EOL;
} catch (Throwable $e) {
    // Never echo the connection string, credentials or arbitrary driver exception text.
    fwrite(STDERR, 'No se pudo completar la auditoria de lectura. Codigo: '.$e->getCode().PHP_EOL);
    exit(1);
}
