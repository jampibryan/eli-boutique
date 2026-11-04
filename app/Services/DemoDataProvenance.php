<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DemoDataProvenance
{
    public function summary(): array
    {
        if (!Schema::hasTable('demo_data_batches')) { return ['has_demo_data'=>false,'batches'=>[]]; }
        $batches = DB::table('demo_data_batches')->where('status','applied')->orderBy('batch_id')->get()
            ->map(function ($batch) {
                $summary=json_decode($batch->summary_json,true,512,JSON_THROW_ON_ERROR);
                return ['batch_id'=>$batch->batch_id,'desde'=>$summary['desde'],'hasta'=>$summary['hasta'],
                    'tipo_datos'=>'DEMOSTRACION','counts'=>$summary['counts']];
            })->all();
        return ['has_demo_data'=>count($batches)>0,'batches'=>$batches];
    }

    public function mark(array $data): array
    {
        $summary=$this->summary();
        $marked=[];
        if ($summary['has_demo_data'] && Schema::hasTable('demo_data_records')) {
            $rows=DB::table('demo_data_records as r')->join('demo_data_batches as b','b.batch_id','=','r.batch_id')
                ->where('b.status','applied')->get(['r.table_name','r.record_id','r.action']);
            foreach($rows as $row) { $marked[$row->table_name][$row->action][(int)$row->record_id]=true; }
        }
        foreach(['ventas'=>['venta_detalles','detalle_id'],'compras'=>['compra_detalles','detalle_id'],
            'pagos'=>['pagos','pago_id'],'cajas'=>['cajas','caja_id']] as $dataset=>[$table,$key]) {
            $data[$dataset]=array_map(function($row) use($marked,$table,$key) {
                $row['es_demostracion']=isset($marked[$table]['insert'][$row[$key]]);
                return $row;
            },$data[$dataset]);
        }
        $data['productos']=array_map(function($row) use($marked,$summary) {
            $row['stock_incluye_demostracion']=$summary['has_demo_data'];
            $row['costo_completado_desde_compras']=isset($marked['productos']['update'][$row['producto_id']]);
            return $row;
        },$data['productos']);
        return ['data'=>$data,'provenance'=>$summary];
    }
}
