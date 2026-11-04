<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('demo_data_batches')) {
            Schema::create('demo_data_batches', function (Blueprint $table) {
                $table->string('batch_id',100)->primary();
                $table->char('plan_hash',64);
                $table->char('baseline_fingerprint',64);
                $table->string('status',20);
                $table->text('summary_json');
                $table->dateTime('created_at');
                $table->dateTime('completed_at')->nullable();
                $table->dateTime('rolled_back_at')->nullable();
            });
        }
        if (!Schema::hasTable('demo_data_records')) {
            Schema::create('demo_data_records', function (Blueprint $table) {
                $table->string('batch_id',100);
                $table->string('table_name',64);
                $table->unsignedBigInteger('record_id');
                $table->string('action',10);
                $table->text('before_json')->nullable();
                $table->text('after_json');
                $table->primary(['batch_id','table_name','record_id','action'],'pk_demo_data_records');
                $table->foreign('batch_id','fk_demo_data_records_batch')->references('batch_id')->on('demo_data_batches');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('demo_data_records');
        Schema::dropIfExists('demo_data_batches');
    }
};
