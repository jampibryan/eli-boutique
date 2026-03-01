<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CompraSeeder extends Seeder
{
    /**
     * Las compras se procesan cronológicamente dentro de:
     * - TransaccionSeederOct (octubre 2025)
     * - TransaccionSeeder (noviembre 2025 - enero 2026)
     * para mantener coherencia de stock.
     */
    public function run()
    {
        echo "ℹ️  Las compras se procesan dentro de TransaccionSeederOct / TransaccionSeeder (orden cronológico)\n";
    }
}
