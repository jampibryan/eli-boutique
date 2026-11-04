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
        if (!Schema::hasTable('chat_histories')) {
            Schema::create('chat_histories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('module', 50)->default('General');
                $table->text('question');
                $table->longText('context')->nullable();
                $table->longText('response');
                $table->string('model', 50)->default('deepseek-chat');
                $table->integer('prompt_tokens')->nullable();
                $table->integer('completion_tokens')->nullable();
                $table->integer('total_tokens')->nullable();
                $table->decimal('cost', 18, 8)->nullable();
                $table->integer('response_time_ms')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_histories');
    }
};
