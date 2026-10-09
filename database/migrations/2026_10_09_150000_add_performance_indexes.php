<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->index('created_at', 'idx_ventas_created_at');
        });

        Schema::table('compras', function (Blueprint $table) {
            $table->index('created_at', 'idx_compras_created_at');
        });

        if (Schema::hasTable('chat_histories')) {
            Schema::table('chat_histories', function (Blueprint $table) {
                $table->index('created_at', 'idx_chat_histories_created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropIndex('idx_ventas_created_at');
        });

        Schema::table('compras', function (Blueprint $table) {
            $table->dropIndex('idx_compras_created_at');
        });

        if (Schema::hasTable('chat_histories')) {
            Schema::table('chat_histories', function (Blueprint $table) {
                $table->dropIndex('idx_chat_histories_created_at');
            });
        }
    }
};
