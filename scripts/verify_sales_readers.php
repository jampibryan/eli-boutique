<?php
// Read-only end-to-end check of current database -> web JSON / invoice templates.
require __DIR__.'/../vendor/autoload.php';
try {
    ini_set('memory_limit','512M');
    $app=require __DIR__.'/../bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    $directory=__DIR__.'/../storage/app/demo-invoices/reader-check';
    if(!is_dir($directory)) mkdir($directory,0777,true);
    $samples=[];$controller=$app->make(App\Http\Controllers\VentaController::class);
    foreach(['Boleta','Factura'] as $type) {
        $query=App\Models\Venta::whereHas('pago.comprobante',fn($q)=>$q->where('descripcionCOM',$type));
        if($type==='Factura') $query->whereIn('id',Illuminate\Support\Facades\DB::table('demo_data_records')
            ->where('batch_id','demo-facturas-2026-11-04')->where('table_name','ventas')->select('record_id'));
        $sale=$query->orderByDesc('id')->firstOrFail();
        $request=Illuminate\Http\Request::create('/ventas/'.$sale->id,'GET',[],[],[],['HTTP_ACCEPT'=>'application/json']);
        $response=$controller->show($request,$sale);
        $payload=json_decode($response->getContent(),true,512,JSON_THROW_ON_ERROR);
        if(!$payload['cliente']||!$payload['detalles']||!is_numeric($payload['montoTotal'])) throw new RuntimeException('Detalle incompleto.');
        $samples[]=$payload;
        $pdfResponse=$controller->pdfComprobante($sale->id);
        $bytes=$pdfResponse->getContent();
        if(!str_starts_with($bytes,'%PDF-')) throw new RuntimeException('El comprobante no es PDF.');
        file_put_contents($directory.'/'.strtolower($type).'.pdf',$bytes);
    }
    file_put_contents($directory.'/web-payloads.json',json_encode($samples,JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE));
    $summary=['status'=>'WEB_JSON_AND_PDF_OK','samples'=>array_map(fn($p)=>[
        'sale_id'=>$p['id'],'comprobante'=>$p['pago']['comprobante']['descripcionCOM'],
        'items'=>count($p['detalles']),'total'=>$p['montoTotal']],$samples)];
    file_put_contents($directory.'/summary.json',json_encode($summary,JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR));
    echo json_encode($summary,JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR),PHP_EOL;
}catch(Throwable $e){fwrite(STDERR,'No se completó la verificación de lectura. Clase: '.get_class($e).PHP_EOL);exit(1);}
