<?php

function demoSpecs(): array
{
    return [
        'cajas' => ['codigoCaja','fecha','hora_cierre','clientesHoy','productosVendidos','ingresoDiario','egresoDiario','created_at','updated_at'],
        'compras' => ['codigoCompra','numero_documento_proveedor','tipo_documento_proveedor','proveedor_id','comprobante_id','colaborador_id','estado_transaccion_id','fecha_envio','fecha_cotizacion','fecha_aprobacion','fecha_entrega_estimada','subtotal','descuento','igv','total','notas_proveedor','condiciones_pago','dias_credito','created_at','updated_at'],
        'ventas' => ['codigoVenta','caja_id','cliente_id','colaborador_id','estado_transaccion_id','subTotal','IGV','montoTotal','created_at','updated_at'],
        'compra_detalles' => ['compra_id','producto_id','producto_talla_id','cantidad','precio_cotizado','precio_final','descuento_unitario','subtotal_linea','created_at','updated_at'],
        'venta_detalles' => ['venta_id','producto_id','producto_talla_id','cantidad','precio_unitario','costo_unitario','base_imponible','igv','subtotal','created_at','updated_at'],
        'pagos' => ['venta_id','compra_id','comprobante_id','importe','vuelto','metodo_pago','referencia_pago','created_at','updated_at'],
        'movimientos_inventario' => ['producto_id','producto_talla_id','tipo_movimiento','cantidad','costo_unitario','precio_unitario','stock_anterior','stock_nuevo','origen_tipo','origen_id','referencia_documento','user_id','motivo','created_at','updated_at'],
    ];
}

function demoCreateLedger(PDO $pdo): void
{
    $pdo->exec("IF OBJECT_ID('demo_data_batches','U') IS NULL CREATE TABLE demo_data_batches (
        batch_id nvarchar(100) NOT NULL PRIMARY KEY, plan_hash char(64) NOT NULL,
        baseline_fingerprint char(64) NOT NULL, status nvarchar(20) NOT NULL,
        summary_json nvarchar(max) NOT NULL, created_at datetime2 NOT NULL,
        completed_at datetime2 NULL, rolled_back_at datetime2 NULL)");
    $pdo->exec("IF OBJECT_ID('demo_data_records','U') IS NULL CREATE TABLE demo_data_records (
        batch_id nvarchar(100) NOT NULL, table_name nvarchar(64) NOT NULL,
        record_id bigint NOT NULL, action nvarchar(10) NOT NULL,
        before_json nvarchar(max) NULL, after_json nvarchar(max) NOT NULL,
        CONSTRAINT pk_demo_data_records PRIMARY KEY(batch_id,table_name,record_id,action),
        CONSTRAINT fk_demo_data_records_batch FOREIGN KEY(batch_id) REFERENCES demo_data_batches(batch_id))");
}

function demoSignature(array $row, array $fields): string
{
    return json_encode(array_map(fn ($field) => $row[$field] === null ? null : (string) $row[$field], $fields), JSON_THROW_ON_ERROR);
}

function demoInsertRows(PDO $pdo, string $table, array $columns, array $rows, array $returnFields = ['id']): array
{
    $result = [];
    $quoted = implode(',', array_map(fn ($c) => '['.$c.']', $columns));
    $output = implode(',', array_map(fn ($c) => 'INSERTED.['.$c.']', $returnFields));
    $chunkSize = min(100, intdiv(2000, count($columns)));
    foreach (array_chunk($rows, $chunkSize) as $chunk) {
        $values = implode(',', array_fill(0, count($chunk), '('.implode(',', array_fill(0, count($columns), '?')).')'));
        $parameters = [];
        foreach ($chunk as $row) { foreach ($columns as $column) { $parameters[] = $row[$column] ?? null; } }
        $statement = $pdo->prepare("INSERT INTO [$table] ($quoted) OUTPUT $output VALUES $values");
        $statement->execute($parameters);
        $result = array_merge($result, $statement->fetchAll(PDO::FETCH_ASSOC));
        $statement->closeCursor();
    }
    return $result;
}

function demoAddLedger(PDO $pdo, array $rows): void
{
    if ($rows) { demoInsertRows($pdo, 'demo_data_records', ['batch_id','table_name','record_id','action','before_json','after_json'], $rows, ['record_id']); }
}

function demoCheck(PDO $pdo, array $plan): array
{
    $batch = $plan['summary']['batch_id'];
    $counts = [];
    foreach (demoSpecs() as $table => $columns) {
        $statement = $pdo->prepare("SELECT COUNT(*) FROM [$table] t JOIN demo_data_records r ON r.record_id=t.id AND r.table_name=? AND r.batch_id=? AND r.action='insert'");
        $statement->execute([$table,$batch]);
        $counts[$table] = (int) $statement->fetchColumn();
        if ($counts[$table] !== count($plan['tables'][$table])) { throw new RuntimeException('Conteo inesperado en '.$table); }
    }
    $invalid = (int) $pdo->query('SELECT COUNT(*) FROM producto_talla_stock s JOIN productos p ON p.id=s.producto_id WHERE p.deleted_at IS NULL AND s.stock<=0')->fetchColumn();
    if ($invalid) { throw new RuntimeException('Hay tallas activas sin existencias positivas.'); }
    $statement = $pdo->prepare("SELECT COUNT(*) FROM ventas v JOIN demo_data_records r ON r.record_id=v.id AND r.table_name='ventas' AND r.batch_id=? JOIN (SELECT venta_id,SUM(subtotal) total FROM venta_detalles GROUP BY venta_id) d ON d.venta_id=v.id WHERE ABS(v.montoTotal-d.total)>0.005");
    $statement->execute([$batch]);
    if ((int) $statement->fetchColumn()) { throw new RuntimeException('Cabeceras y detalles no concilian.'); }
    $statement = $pdo->prepare("SELECT COUNT(*) FROM cajas c JOIN demo_data_records r ON r.record_id=c.id AND r.table_name='cajas' AND r.batch_id=?
        LEFT JOIN (SELECT v.caja_id,SUM(p.importe-COALESCE(p.vuelto,0)) total FROM pagos p JOIN ventas v ON v.id=p.venta_id GROUP BY v.caja_id) i ON i.caja_id=c.id
        LEFT JOIN (SELECT CAST(p.created_at AS date) fecha,SUM(p.importe) total FROM pagos p WHERE p.compra_id IS NOT NULL GROUP BY CAST(p.created_at AS date)) e ON e.fecha=c.fecha
        WHERE ABS(c.ingresoDiario-COALESCE(i.total,0))>0.005 OR ABS(c.egresoDiario-COALESCE(e.total,0))>0.005");
    $statement->execute([$batch]);
    if ((int) $statement->fetchColumn()) { throw new RuntimeException('Pagos y caja no concilian.'); }
    $read = $pdo->prepare('SELECT stock FROM producto_talla_stock WHERE id=?');
    foreach ($plan['stock_updates'] as $row) {
        $read->execute([$row['id']]);
        if ((int) $read->fetchColumn() !== $row['after']) { throw new RuntimeException('Stock final distinto al plan.'); }
    }
    return ['batch_id'=>$batch,'counts'=>$counts,'active_size_stock_nonpositive'=>0,'sale_mismatches'=>0,'cash_mismatches'=>0];
}

function demoApply(PDO $pdo, array $plan): array
{
    if (($plan['format_version'] ?? null) !== 1 || ($plan['summary']['tipo_datos'] ?? '') !== 'DEMOSTRACION') { throw new RuntimeException('Formato de plan no permitido.'); }
    foreach (demoSpecs() as $table => $columns) {
        foreach ($plan['tables'][$table] as $row) {
            if (array_diff(array_keys($row), array_merge($columns, ['_key']))) { throw new RuntimeException('Columna no permitida en '.$table); }
        }
    }
    $batch = $plan['summary']['batch_id'];
    $fileHash = hash('sha256', json_encode($plan, JSON_THROW_ON_ERROR));
    $pdo->exec('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
    $pdo->beginTransaction();
    try {
        demoCreateLedger($pdo);
        $exists = $pdo->prepare('SELECT plan_hash,status FROM demo_data_batches WITH(UPDLOCK,HOLDLOCK) WHERE batch_id=?');
        $exists->execute([$batch]); $previous = $exists->fetch(PDO::FETCH_ASSOC);
        if ($previous) {
            if ($previous['plan_hash'] !== $fileHash) { throw new RuntimeException('El lote ya existe con otro contenido.'); }
            $pdo->rollBack(); return ['status'=>'already_'.$previous['status'],'batch_id'=>$batch];
        }
        if (demoExport($pdo)['fingerprint'] !== $plan['baseline_fingerprint']) { throw new RuntimeException('La base cambió desde la planificación. Se aborta sin modificar registros.'); }
        $statement = $pdo->prepare("INSERT INTO demo_data_batches(batch_id,plan_hash,baseline_fingerprint,status,summary_json,created_at) VALUES(?,?,?,'applying',?,SYSUTCDATETIME())");
        $statement->execute([$batch,$fileHash,$plan['baseline_fingerprint'],json_encode($plan['summary'],JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)]);
        $signatures = ['cajas'=>['fecha'],'compras'=>['codigoCompra'],'ventas'=>['codigoVenta'],
            'compra_detalles'=>['compra_id','producto_id','producto_talla_id'],
            'venta_detalles'=>['venta_id','producto_id','producto_talla_id'],
            'pagos'=>['venta_id','compra_id'],
            'movimientos_inventario'=>['origen_tipo','origen_id','producto_id','producto_talla_id','tipo_movimiento']];
        $references = [];
        foreach (demoSpecs() as $table => $columns) {
            $resolved = []; $bySignature = [];
            foreach ($plan['tables'][$table] as $row) {
                $resolvedRow = [];
                foreach ($columns as $column) {
                    $value = $row[$column] ?? null;
                    if (is_string($value) && str_starts_with($value,'@')) {
                        if (!isset($references[$value])) { throw new RuntimeException('Referencia pendiente: '.$value); }
                        $value = $references[$value];
                    }
                    $resolvedRow[$column] = $value;
                }
                $signature = demoSignature($resolvedRow,$signatures[$table]);
                if (isset($bySignature[$signature])) { throw new RuntimeException('Fila duplicada en '.$table); }
                $bySignature[$signature] = ['key'=>$row['_key'] ?? null,'row'=>$resolvedRow];
                $resolved[] = $resolvedRow;
            }
            $outputs = demoInsertRows($pdo,$table,$columns,$resolved,array_merge(['id'],$signatures[$table]));
            $ledger = [];
            foreach ($outputs as $output) {
                $expected = $bySignature[demoSignature($output,$signatures[$table])] ?? null;
                if (!$expected) { throw new RuntimeException('No se pudo relacionar la fila insertada.'); }
                $id = (int) $output['id'];
                if ($expected['key'] !== null) { $references['@'.$table.':'.$expected['key']] = $id; }
                $ledger[] = ['batch_id'=>$batch,'table_name'=>$table,'record_id'=>$id,'action'=>'insert','before_json'=>null,
                    'after_json'=>json_encode($expected['row'],JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)];
            }
            demoAddLedger($pdo,$ledger);
            echo 'Insertado y registrado: '.$table.' ('.count($outputs).')'.PHP_EOL;
            flush();
        }
        $ledger = [];
        $update = $pdo->prepare('UPDATE producto_talla_stock SET stock=?,updated_at=? WHERE id=? AND stock=?');
        foreach ($plan['stock_updates'] as $row) {
            $update->execute([$row['after'],$row['after_updated_at'],$row['id'],$row['before']]);
            if ($update->rowCount() !== 1) { throw new RuntimeException('Stock cambió concurrentemente.'); }
            $ledger[] = ['batch_id'=>$batch,'table_name'=>'producto_talla_stock','record_id'=>$row['id'],'action'=>'update',
                'before_json'=>json_encode(['stock'=>$row['before'],'updated_at'=>$row['before_updated_at']],JSON_THROW_ON_ERROR),
                'after_json'=>json_encode(['stock'=>$row['after'],'updated_at'=>$row['after_updated_at']],JSON_THROW_ON_ERROR)];
        }
        $update = $pdo->prepare('UPDATE productos SET costo_compra=? WHERE id=? AND costo_compra=?');
        foreach ($plan['product_updates'] as $row) {
            $update->execute([$row['after'],$row['id'],$row['before']]);
            if ($update->rowCount() !== 1) { throw new RuntimeException('Costo cambió concurrentemente.'); }
            $ledger[] = ['batch_id'=>$batch,'table_name'=>'productos','record_id'=>$row['id'],'action'=>'update',
                'before_json'=>json_encode(['costo_compra'=>$row['before']],JSON_THROW_ON_ERROR),
                'after_json'=>json_encode(['costo_compra'=>$row['after']],JSON_THROW_ON_ERROR)];
        }
        demoAddLedger($pdo,$ledger);
        $verification = demoCheck($pdo,$plan);
        $statement = $pdo->prepare("UPDATE demo_data_batches SET status='applied',completed_at=SYSUTCDATETIME() WHERE batch_id=?");
        $statement->execute([$batch]);
        $pdo->commit();
        return ['status'=>'applied','verification'=>$verification,'summary'=>$plan['summary']];
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) { $pdo->rollBack(); }
        throw $e;
    }
}

function demoSameValue($actual, $expected, string $column): bool
{
    if ($actual === null || $expected === null) { return $actual === $expected; }
    if (preg_match('/_at$|^fecha|hora_cierre/',$column)) {
        return strtotime((string)$actual) === strtotime((string)$expected);
    }
    if (is_numeric($actual) && is_numeric($expected)) { return abs((float)$actual-(float)$expected)<0.00001; }
    return (string)$actual === (string)$expected;
}

function demoRollback(PDO $pdo, string $batch, bool $dryRun = false): array
{
    $pdo->exec('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
    $pdo->beginTransaction();
    try {
        $statement = $pdo->prepare("SELECT status FROM demo_data_batches WITH(UPDLOCK,HOLDLOCK) WHERE batch_id=?");
        $statement->execute([$batch]); $status = $statement->fetchColumn();
        if ($status !== 'applied') { throw new RuntimeException('Solo se puede revertir un lote aplicado y activo.'); }
        $statement = $pdo->prepare('SELECT * FROM demo_data_records WHERE batch_id=? ORDER BY table_name,record_id');
        $statement->execute([$batch]); $records = $statement->fetchAll(PDO::FETCH_ASSOC);
        $allowed = array_merge(array_keys(demoSpecs()),['productos','producto_talla_stock']);
        $grouped=[];
        foreach($records as $record) { $grouped[$record['table_name']][]=$record; }
        foreach ($grouped as $table=>$tableRecords) {
            if(!in_array($table,$allowed,true)) { throw new RuntimeException('Tabla ajena al lote.'); }
            foreach(array_chunk($tableRecords,100) as $chunk) {
                $read=$pdo->prepare("SELECT * FROM [$table] WITH(UPDLOCK,HOLDLOCK) WHERE id IN (".implode(',',array_fill(0,count($chunk),'?')).')');
                $read->execute(array_column($chunk,'record_id'));$currentRows=[];
                foreach($read->fetchAll(PDO::FETCH_ASSOC) as $row) { $currentRows[(int)$row['id']]=$row; }
                foreach($chunk as $record) {
                    $current=$currentRows[(int)$record['record_id']]??null;
                    if(!$current) { throw new RuntimeException('Falta una fila del lote.'); }
                    foreach(json_decode($record['after_json'],true,512,JSON_THROW_ON_ERROR) as $column=>$expected) {
                        if(!demoSameValue($current[$column]??null,$expected,$column)) { throw new RuntimeException('El lote tuvo cambios posteriores; no se revertirá automáticamente.'); }
                    }
                }
            }
        }
        if($dryRun) { $pdo->rollBack();return ['status'=>'rollback_ready_without_changes','batch_id'=>$batch,'records_checked'=>count($records)]; }
        foreach (array_reverse(array_keys(demoSpecs())) as $table) {
            $delete = $pdo->prepare("DELETE t FROM [$table] t JOIN demo_data_records r ON r.record_id=t.id WHERE r.batch_id=? AND r.table_name=? AND r.action='insert'");
            $delete->execute([$batch,$table]);
        }
        foreach ($records as $record) {
            if ($record['action'] !== 'update') { continue; }
            $before = json_decode($record['before_json'],true,512,JSON_THROW_ON_ERROR);
            $table = $record['table_name'];
            $columns = $table === 'productos' ? ['costo_compra'] : ['stock','updated_at'];
            $update = $pdo->prepare("UPDATE [$table] SET ".implode(',',array_map(fn($c)=>'['.$c.']=?',$columns)).' WHERE id=?');
            $update->execute(array_merge(array_map(fn($c)=>$before[$c],$columns),[$record['record_id']]));
        }
        $statement = $pdo->prepare("UPDATE demo_data_batches SET status='rolled_back',rolled_back_at=SYSUTCDATETIME() WHERE batch_id=?");
        $statement->execute([$batch]);$pdo->commit();
        return ['status'=>'rolled_back','batch_id'=>$batch];
    } catch (Throwable $e) { if($pdo->inTransaction()){$pdo->rollBack();} throw $e; }
}
