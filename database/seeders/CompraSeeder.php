<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CompraSeeder extends Seeder
{
    /**
     * Las compras de octubre 2025 se procesan cronológicamente
     * dentro de VentaSeeder para mantener coherencia de stock.
     * (4 compras los sábados: Oct 4, 11, 18, 25)
     */
    public function run()
    {
        echo "ℹ️  Las compras se procesan dentro de VentaSeeder (orden cronológico)\n";
    }
}
