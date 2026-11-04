<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if(DB::connection()->getDriverName()!=='sqlsrv') return;
        // SQL Server unfiltered UNIQUE indexes admit only one NULL. Optional contacts
        // must remain unique when supplied, while several clients can omit them.
        $quote=fn($name)=>'['.str_replace(']',']]', $name).']';
        foreach(['correoCliente'=>'clientes_correo_no_nulo_unique','telefonoCliente'=>'clientes_telefono_no_nulo_unique'] as $column=>$newName) {
            $indexes=DB::select("SELECT i.name,i.is_unique_constraint FROM sys.indexes i
                JOIN sys.index_columns ic ON ic.object_id=i.object_id AND ic.index_id=i.index_id
                JOIN sys.columns c ON c.object_id=ic.object_id AND c.column_id=ic.column_id
                WHERE i.object_id=OBJECT_ID('clientes') AND i.is_unique=1 AND i.is_primary_key=0
                AND ic.key_ordinal>0 GROUP BY i.name,i.is_unique_constraint
                HAVING COUNT(*)=1 AND MAX(c.name)=?",[$column]);
            foreach($indexes as $index) {
                DB::statement($index->is_unique_constraint
                    ? 'ALTER TABLE [clientes] DROP CONSTRAINT '.$quote($index->name)
                    : 'DROP INDEX '.$quote($index->name).' ON [clientes]');
            }
            DB::statement('CREATE UNIQUE INDEX '.$quote($newName).' ON [clientes] ('.$quote($column).') WHERE '.$quote($column).' IS NOT NULL');
        }
    }

    public function down(): void
    {
        if(DB::connection()->getDriverName()!=='sqlsrv') return;
        foreach(['correoCliente','telefonoCliente'] as $column) {
            if(DB::table('clientes')->whereNull($column)->count()>1) {
                throw new RuntimeException('No se restaura un índice que impediría conservar contactos vacíos existentes.');
            }
        }
        DB::statement('DROP INDEX [clientes_correo_no_nulo_unique] ON [clientes]');
        DB::statement('DROP INDEX [clientes_telefono_no_nulo_unique] ON [clientes]');
        DB::statement('CREATE UNIQUE INDEX [clientes_correocliente_unique] ON [clientes] ([correoCliente])');
        DB::statement('CREATE UNIQUE INDEX [clientes_telefonocliente_unique] ON [clientes] ([telefonoCliente])');
    }
};
