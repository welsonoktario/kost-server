<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->foreign('user_username')
                ->references('username')
                ->on('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->date('entry_date')->useCurrent();
            $table->date('leave_date')->nullable();
            $table->date('due_date')->nullable();
            $table->boolean('status');
            $table->string('ktp')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tenants');
    }
}
