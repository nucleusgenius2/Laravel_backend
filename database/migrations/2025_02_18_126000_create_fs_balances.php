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
        Schema::create('fs_balances', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('count');
			$table->decimal('nominal', 20, 2);
			$table->timestamp('to_date');
			$table->unsignedBigInteger('user_id');
			$table->string('type', 50);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fs_balances');
    }
};
