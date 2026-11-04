<?php
// Changes only identified demonstration sales. No mail, fiscal emission or stock mutations.
require __DIR__.'/demo_data.php';

const INVOICE_BATCH = 'demo-facturas-2026-11-04';
const INVOICE_DIR = __DIR__.'/../storage/app/demo-invoices';

function invoiceRows(PDO $pdo, string $sql, array $args=[]): array {
    $q=$pdo->prepare($sql); $q->execute($args); return $q->fetchAll(PDO::FETCH_ASSOC);
}
function invoiceAudit(PDO $pdo): array {
    return [
        'receipts'=>invoiceRows($pdo,"SELECT c.descripcionCOM,COUNT(*) cantidad FROM pagos p JOIN comprobantes c ON c.id=p.comprobante_id WHERE p.venta_id IS NOT NULL GROUP BY c.descripcionCOM"),
        'orphans'=>(int)$pdo->query('SELECT COUNT(*) FROM ventas v LEFT JOIN clientes c ON c.id=v.cliente_id WHERE c.id IS NULL')->fetchColumn(),
        'sales'=>(int)$pdo->query('SELECT COUNT(*) FROM ventas')->fetchColumn(),
        'company_columns'=>(int)$pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='clientes' AND COLUMN_NAME='tipo_documento'")->fetchColumn(),
        'ruc_documents_existing'=>(int)$pdo->query('SELECT COUNT(*) FROM clientes WHERE LEN(dniCliente)=11 AND deleted_at IS NULL')->fetchColumn(),
        'invoices_missing_ruc_or_company'=>(int)$pdo->query("SELECT COUNT(*) FROM ventas v JOIN pagos p ON p.venta_id=v.id JOIN comprobantes c ON c.id=p.comprobante_id WHERE c.descripcionCOM='Factura' AND (v.ruc_factura IS NULL OR LEN(v.ruc_factura)<>11 OR v.razon_social_factura IS NULL OR LTRIM(RTRIM(v.razon_social_factura))='')")->fetchColumn(),
        'invalid_stock'=>(int)$pdo->query('SELECT COUNT(*) FROM producto_talla_stock s JOIN productos p ON p.id=s.producto_id WHERE p.deleted_at IS NULL AND s.stock<=0')->fetchColumn(),
        'client_unique_indexes'=>invoiceRows($pdo,"SELECT name,filter_definition FROM sys.indexes WHERE object_id=OBJECT_ID('clientes') AND is_unique=1"),
    ];
}
function invoiceInvariant(PDO $pdo): array {
    $result=[];
    foreach([
        'stocks'=>'SELECT id,producto_id,producto_talla_id,stock FROM producto_talla_stock ORDER BY id',
        'details'=>'SELECT * FROM venta_detalles ORDER BY id',
        'purchase_details'=>'SELECT * FROM compra_detalles ORDER BY id',
        'purchases'=>'SELECT * FROM compras ORDER BY id',
        'movements'=>'SELECT * FROM movimientos_inventario ORDER BY id',
        'sale_amounts'=>'SELECT id,subTotal,IGV,montoTotal,created_at,estado_transaccion_id,caja_id FROM ventas ORDER BY id',
        'payment_amounts'=>'SELECT id,venta_id,compra_id,importe,vuelto,created_at,metodo_pago,referencia_pago FROM pagos ORDER BY id',
        'cash_amounts'=>'SELECT id,fecha,ingresoDiario,egresoDiario,productosVendidos FROM cajas ORDER BY id',
    ] as $name=>$sql) $result[$name]=hash('sha256',json_encode(invoiceRows($pdo,$sql),JSON_THROW_ON_ERROR));
    return $result;
}
function invoicePrepare(PDO $pdo): array {
    if(invoiceRows($pdo,'SELECT batch_id FROM demo_data_batches WHERE batch_id=?',[INVOICE_BATCH])) {
        throw new RuntimeException('El lote ya existe. Conserva su plan y utiliza verify o rollback-check.');
    }
    $candidates=invoiceRows($pdo,"SELECT v.*,p.id pago_id,p.comprobante_id FROM ventas v
        JOIN pagos p ON p.venta_id=v.id JOIN comprobantes c ON c.id=p.comprobante_id
        JOIN estado_transacciones e ON e.id=v.estado_transaccion_id
        JOIN demo_data_records r ON r.table_name='ventas' AND r.record_id=v.id AND r.action='insert'
        JOIN demo_data_batches b ON b.batch_id=r.batch_id AND b.status='applied'
        WHERE c.descripcionCOM='Boleta' AND e.descripcionET='Pagado'
        AND v.ruc_factura IS NULL AND v.razon_social_factura IS NULL
        AND (SELECT SUM(d.cantidad) FROM venta_detalles d WHERE d.venta_id=v.id)>=2
        AND v.montoTotal>=80 ORDER BY v.created_at,v.id");
    if(count($candidates)<180) throw new RuntimeException('No hay suficientes ventas de demostración elegibles.');
    $companies=[];
    $names=['Comercial Aurora Textil S.A.C.','Inversiones Valle Claro S.A.C.','Servicios Corporativos Andina S.R.L.',
        'Distribuidora Brisa del Sur S.A.C.','Eventos Nueva Imagen S.A.C.','Comercial Río Sereno S.R.L.',
        'Corporación Horizonte Moda S.A.C.','Servicios Integrales Sol del Valle S.A.C.',
        'Inversiones Terra Elegante S.R.L.','Comercial Costa Serena S.A.C.'];
    for($i=0;$i<10;$i++) {
        // Fictitious 11-digit identifiers, not SUNAT-verified company identities.
        $companies[]=['nombreCliente'=>$names[$i].' (Demo)','apellidoCliente'=>'',
            'dniCliente'=>'20999000'.str_pad((string)($i+1),3,'0',STR_PAD_LEFT),
            'tipo_documento'=>'RUC','direccionCliente'=>'Dirección de prueba '.($i+1).' - Lima',
            'correoCliente'=>null,'telefonoCliente'=>null];
    }
    $chosen=[];
    // Evenly spread across the entire eligible period, including its latest sale.
    for($i=0;$i<180;$i++) $chosen[]=$candidates[(int)round($i*(count($candidates)-1)/179)];
    $plan=['batch_id'=>INVOICE_BATCH,'invariant'=>invoiceInvariant($pdo),'companies'=>$companies,'sales'=>[],
        'summary'=>['desde'=>'2026-06-12','hasta'=>'2026-10-15','counts'=>['clientes'=>10,'ventas_actualizadas'=>180],
            'tipo_datos'=>'DEMOSTRACION','ruc_sunat_verificado'=>false]];
    foreach($chosen as $i=>$sale) $plan['sales'][]=['id'=>(int)$sale['id'],'pago_id'=>(int)$sale['pago_id'],
        'company'=>$i%10,'before'=>array_intersect_key($sale,array_flip(['cliente_id','ruc_factura','razon_social_factura','direccion_factura','updated_at'])),
        'payment_before'=>['comprobante_id'=>$sale['comprobante_id']]];
    return $plan;
}
function invoiceTrackedUpdates(PDO $pdo,string $table,array $updates,array $beforeRows,array &$records): void {
    if(!in_array($table,['ventas','pagos','cajas'],true)) throw new RuntimeException('Tabla no permitida.');
    if(!$updates) return;
    $columns=array_keys(reset($updates));
    foreach(array_chunk($updates,100,true) as $chunk) {
        $values=[];$parameters=[];
        foreach($chunk as $id=>$after) {
            if(!isset($beforeRows[$id])) throw new RuntimeException('Falta una fila a actualizar.');
            $before=array_intersect_key($beforeRows[$id],$after);
            $records[]=['batch_id'=>INVOICE_BATCH,'table_name'=>$table,'record_id'=>$id,'action'=>'update',
                'before_json'=>json_encode($before,JSON_THROW_ON_ERROR),'after_json'=>json_encode($after,JSON_THROW_ON_ERROR)];
            $values[]='('.implode(',',array_fill(0,count($columns)+1,'?')).')';
            array_push($parameters,$id,...array_values($after));
        }
        $set=implode(',',array_map(fn($c)=>"[$c]=s.[$c]",$columns));
        $names='[id],'.implode(',',array_map(fn($c)=>"[$c]",$columns));
        $pdo->prepare("UPDATE t SET $set FROM [$table] t JOIN (VALUES ".implode(',',$values).") s($names) ON s.id=t.id")->execute($parameters);
    }
}
function invoiceCheck(PDO $pdo,array $plan): array {
    if(invoiceInvariant($pdo)!==$plan['invariant']) throw new RuntimeException('Cambiaron importes, fechas, estados, stock o movimientos.');
    $invalid=(int)$pdo->query("SELECT COUNT(*) FROM ventas v JOIN pagos p ON p.venta_id=v.id
        JOIN comprobantes co ON co.id=p.comprobante_id JOIN clientes c ON c.id=v.cliente_id
        JOIN demo_data_records r ON r.record_id=v.id AND r.table_name='ventas' AND r.batch_id='".INVOICE_BATCH."'
        WHERE co.descripcionCOM<>'Factura' OR c.tipo_documento<>'RUC' OR LEN(c.dniCliente)<>11
        OR COALESCE(v.ruc_factura,'')<>c.dniCliente OR COALESCE(v.razon_social_factura,'')<>c.nombreCliente
        OR COALESCE(v.direccion_factura,'')<>COALESCE(c.direccionCliente,'')")->fetchColumn();
    if($invalid) throw new RuntimeException('Facturas incoherentes con el cliente empresa.');
    $count=(int)$pdo->query("SELECT COUNT(*) FROM demo_data_records WHERE batch_id='".INVOICE_BATCH."' AND table_name='ventas'")->fetchColumn();
    if($count!==180) throw new RuntimeException('Cantidad de facturas inesperada.');
    $badCash=(int)$pdo->query("SELECT COUNT(*) FROM cajas c JOIN demo_data_records r ON r.record_id=c.id AND r.table_name='cajas' AND r.batch_id='".INVOICE_BATCH."'
        WHERE c.clientesHoy<>(SELECT COUNT(DISTINCT v.cliente_id) FROM ventas v JOIN estado_transacciones e ON e.id=v.estado_transaccion_id WHERE v.caja_id=c.id AND e.descripcionET='Pagado')")->fetchColumn();
    if($badCash) throw new RuntimeException('Contador de clientes en caja no coincide.');
    return ['status'=>'verified','companies'=>10,'invoices'=>180,'unchanged'=>array_keys($plan['invariant']),'audit'=>invoiceAudit($pdo)];
}
function invoiceApply(PDO $pdo,array $plan): array {
    $hash=hash('sha256',json_encode($plan,JSON_THROW_ON_ERROR));
    $pdo->exec('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');$pdo->beginTransaction();
    try {
        $old=invoiceRows($pdo,'SELECT plan_hash,status FROM demo_data_batches WITH(UPDLOCK,HOLDLOCK) WHERE batch_id=?',[INVOICE_BATCH]);
        if($old) {
            if($old[0]['plan_hash']!==$hash) throw new RuntimeException('El lote ya existe con otro plan.');
            $pdo->rollBack(); return ['status'=>'already_'.$old[0]['status']];
        }
        if(invoiceInvariant($pdo)!==$plan['invariant']) throw new RuntimeException('La base cambió desde la planificación.');
        $pdo->prepare("INSERT INTO demo_data_batches(batch_id,plan_hash,baseline_fingerprint,status,summary_json,created_at) VALUES(?,?,?,'applying',?,SYSUTCDATETIME())")
            ->execute([INVOICE_BATCH,$hash,$hash,json_encode($plan['summary'],JSON_THROW_ON_ERROR)]);
        $genre=(int)$pdo->query("SELECT id FROM tipo_generos WHERE descripcionTG='No aplica'")->fetchColumn();
        $receipt=(int)$pdo->query("SELECT id FROM comprobantes WHERE descripcionCOM='Factura'")->fetchColumn();
        if(!$genre||!$receipt) throw new RuntimeException('Faltan catálogos de empresa o Factura.');
        $records=[];$ids=[];$now=date('Y-m-d H:i:s');$companies=[];
        $documents=array_column($plan['companies'],'dniCliente');
        $placeholders=implode(',',array_fill(0,count($documents),'?'));
        if(invoiceRows($pdo,"SELECT id FROM clientes WHERE dniCliente IN ($placeholders)",$documents)) throw new RuntimeException('Un documento de prueba ya existe.');
        foreach($plan['companies'] as $company) {
            $companies[]=$company+['tipo_genero_id'=>$genre,'created_at'=>$now,'updated_at'=>$now,'deleted_at'=>null];
        }
        $inserted=demoInsertRows($pdo,'clientes',array_keys($companies[0]),$companies,['id','dniCliente']);
        $byDocument=array_column($inserted,'id','dniCliente');
        foreach($companies as $company) {
            $id=(int)$byDocument[$company['dniCliente']];$ids[]=$id;
            $records[]=['batch_id'=>INVOICE_BATCH,'table_name'=>'clientes','record_id'=>$id,'action'=>'insert','before_json'=>null,
                'after_json'=>json_encode($company,JSON_THROW_ON_ERROR)];
        }
        $saleIds=array_column($plan['sales'],'id');$paymentIds=array_column($plan['sales'],'pago_id');
        $placeholders=implode(',',array_fill(0,count($saleIds),'?'));
        $salesBefore=array_column(invoiceRows($pdo,"SELECT * FROM ventas WITH(UPDLOCK,HOLDLOCK) WHERE id IN ($placeholders)",$saleIds),null,'id');
        $paymentsBefore=array_column(invoiceRows($pdo,"SELECT * FROM pagos WITH(UPDLOCK,HOLDLOCK) WHERE id IN ($placeholders)",$paymentIds),null,'id');
        $cash=[];$salesAfter=[];$paymentsAfter=[];
        foreach($plan['sales'] as $sale) {
            $current=$salesBefore[$sale['id']]??throw new RuntimeException('Falta una venta.');
            foreach($sale['before'] as $column=>$value) if(!demoSameValue($current[$column],$value,$column)) throw new RuntimeException('La venta cambió desde la planificación.');
            $payment=$paymentsBefore[$sale['pago_id']]??throw new RuntimeException('Falta un pago.');
            if((int)$payment['venta_id']!==$sale['id'] || (int)$payment['comprobante_id']!==(int)$sale['payment_before']['comprobante_id']) throw new RuntimeException('El pago cambió desde la planificación.');
            $company=$plan['companies'][$sale['company']];
            $salesAfter[$sale['id']]=['cliente_id'=>$ids[$sale['company']],
                'ruc_factura'=>$company['dniCliente'],'razon_social_factura'=>$company['nombreCliente'],
                'direccion_factura'=>$company['direccionCliente']];
            $paymentsAfter[$sale['pago_id']]=['comprobante_id'=>$receipt];
            $cash[(int)$current['caja_id']]=true;
        }
        invoiceTrackedUpdates($pdo,'ventas',$salesAfter,$salesBefore,$records);
        invoiceTrackedUpdates($pdo,'pagos',$paymentsAfter,$paymentsBefore,$records);
        $cashIds=array_keys($cash);$placeholders=implode(',',array_fill(0,count($cashIds),'?'));
        $cashBefore=array_column(invoiceRows($pdo,"SELECT * FROM cajas WITH(UPDLOCK,HOLDLOCK) WHERE id IN ($placeholders)",$cashIds),null,'id');
        $cashCounts=invoiceRows($pdo,"SELECT caja_id,COUNT(DISTINCT cliente_id) total FROM ventas v JOIN estado_transacciones e ON e.id=v.estado_transaccion_id WHERE caja_id IN ($placeholders) AND e.descripcionET='Pagado' GROUP BY caja_id",$cashIds);
        $cashAfter=[];foreach($cashCounts as $row) $cashAfter[(int)$row['caja_id']]=['clientesHoy'=>(int)$row['total']];
        invoiceTrackedUpdates($pdo,'cajas',$cashAfter,$cashBefore,$records);
        demoAddLedger($pdo,$records);
        $result=invoiceCheck($pdo,$plan);
        $pdo->prepare("UPDATE demo_data_batches SET status='applied',completed_at=SYSUTCDATETIME() WHERE batch_id=?")->execute([INVOICE_BATCH]);
        $pdo->commit();return $result+['records_tracked'=>count($records)];
    } catch(Throwable $e) {if($pdo->inTransaction())$pdo->rollBack();throw $e;}
}
function invoiceRollback(PDO $pdo,bool $check): array {
    $pdo->exec('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');$pdo->beginTransaction();
    try {
        $batch=invoiceRows($pdo,'SELECT status FROM demo_data_batches WITH(UPDLOCK,HOLDLOCK) WHERE batch_id=?',[INVOICE_BATCH]);
        if(!$batch||$batch[0]['status']!=='applied') throw new RuntimeException('Lote no aplicado.');
        $records=invoiceRows($pdo,'SELECT * FROM demo_data_records WHERE batch_id=?',[INVOICE_BATCH]);
        $grouped=[];$currentRows=[];
        foreach($records as $record) $grouped[$record['table_name']][]=$record;
        foreach($grouped as $table=>$tableRecords) {
            if(!in_array($table,['clientes','ventas','pagos','cajas'],true)) throw new RuntimeException('Tabla no permitida.');
            foreach(array_chunk($tableRecords,100) as $chunk) {
                $ids=array_column($chunk,'record_id');$placeholders=implode(',',array_fill(0,count($ids),'?'));
                $rows=array_column(invoiceRows($pdo,"SELECT * FROM [$table] WITH(UPDLOCK,HOLDLOCK) WHERE id IN ($placeholders)",$ids),null,'id');
                $currentRows[$table]=($currentRows[$table]??[])+$rows;
                foreach($chunk as $record) {
                    $row=$rows[$record['record_id']]??throw new RuntimeException('Falta una fila controlada.');
                    foreach(json_decode($record['after_json'],true,512,JSON_THROW_ON_ERROR) as $column=>$value)
                        if(!demoSameValue($row[$column]??null,$value,$column)) throw new RuntimeException('Hay cambios posteriores; no se revierte.');
                }
            }
        }
        if($check){$pdo->rollBack();return ['status'=>'rollback_ready_without_changes','records_checked'=>count($records)];}
        foreach($grouped as $table=>$tableRecords) {
            $updates=[];$unused=[];
            foreach($tableRecords as $record) if($record['action']==='update') $updates[$record['record_id']]=json_decode($record['before_json'],true,512,JSON_THROW_ON_ERROR);
            if($updates) invoiceTrackedUpdates($pdo,$table,$updates,$currentRows[$table],$unused);
        }
        $ids=array_column($grouped['clientes']??[],'record_id');
        if($ids) $pdo->prepare('DELETE FROM clientes WHERE id IN ('.implode(',',array_fill(0,count($ids),'?')).')')->execute($ids);
        $pdo->prepare("UPDATE demo_data_batches SET status='rolled_back',rolled_back_at=SYSUTCDATETIME() WHERE batch_id=?")->execute([INVOICE_BATCH]);
        $pdo->commit();return ['status'=>'rolled_back'];
    }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();throw $e;}
}

if(realpath($_SERVER['SCRIPT_FILENAME']??'')===__FILE__) {
    try {
        $pdo=demoConnection();$command=$argv[1]??'audit';
        if($command==='audit') $result=invoiceAudit($pdo);
        elseif($command==='plan') { $plan=invoicePrepare($pdo); demoWriteJson(INVOICE_DIR.'/plan.json',$plan);$result=['planned_sales'=>count($plan['sales']),'companies'=>count($plan['companies'])]; }
        elseif(in_array($command,['apply','verify'],true)) {
            $plan=json_decode(file_get_contents(INVOICE_DIR.'/plan.json'),true,512,JSON_THROW_ON_ERROR);
            $result=$command==='apply'?invoiceApply($pdo,$plan):invoiceCheck($pdo,$plan); demoWriteJson(INVOICE_DIR.'/'.$command.'.json',$result);
        } elseif(in_array($command,['rollback-check','rollback'],true)) $result=invoiceRollback($pdo,$command==='rollback-check');
        else throw new RuntimeException('Comando no disponible.');
        echo json_encode($result,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR),PHP_EOL;
    }catch(Throwable $e){fwrite(STDERR,get_class($e).': '.($e instanceof PDOException?'Operación no completada; transacción revertida si estaba abierta. SQLSTATE='.$e->getCode().' DRIVER='.($e->errorInfo[1]??'unknown'):$e->getMessage()).PHP_EOL);exit(1);}
}
