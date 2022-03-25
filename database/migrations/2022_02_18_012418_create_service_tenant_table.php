<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateServiceTenantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_tenant', function (Blueprint $table) {
            $table->foreignId('service_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum('status', ['ditolak', 'diterima', 'pending'])->default('pending');
            $table->dateTime('tanggal')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_tenant');
    }
}
