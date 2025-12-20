<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoGeneroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Inserta los registros en la tabla tipo_generos
        DB::table('tipo_generos')->insert([
            ['descripcionTG' => 'Hombre'],
            ['descripcionTG' => 'Mujer'],
        ]);
    }
}
