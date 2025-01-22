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
        Schema::create('fiat_coin', function (Blueprint $table) {
            $table->increments('id');
            $table->text('img')->nullable();
            $table->string('code', 5);
            $table->text('name');
            $table->text('country');
            $table->primary('id', 'btree');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fiat_coin');
    }
};
