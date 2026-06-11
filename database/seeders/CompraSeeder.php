<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CompraSeeder extends Seeder
{
    /**
     * Las compras se procesan cronológicamente dentro de:
     * - TransaccionSeeder (octubre 2025 - mayo 2026)
     * para mantener coherencia de stock.
     */
    public function run()
    {
        echo "ℹ️  Las compras se procesan dentro de TransaccionSeeder (orden cronológico)\n";
    }
}
