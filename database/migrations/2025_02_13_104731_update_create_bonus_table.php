<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bonus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 50);
            $table->string('type', 50);
            $table->decimal('amount', 8, 2)->nullable();
            $table->decimal('bonus', 8, 2)->nullable();
            $table->integer('valid')->nullable();
            $table->string('bonus_type', 255);
            $table->integer('bonus_count');
            $table->decimal('bonus_nominal', 8, 2)->nullable();
            $table->integer('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bonus');
    }
};
