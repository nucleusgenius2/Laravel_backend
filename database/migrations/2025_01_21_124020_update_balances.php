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
        Schema::table('balances', function (Blueprint $table) {
            // Drop columns
            $table->dropColumn(['user', 'type', 'binch', 'left', 'right']);

            // Add new columns
            $table->dateTime('to_date')->nullable();
            $table->decimal('count', 15, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('balances', function (Blueprint $table) {
            // Add dropped columns back
            $table->string('user')->nullable();
            $table->string('type')->nullable();
            $table->integer('binch')->nullable();
            $table->integer('left')->nullable();
            $table->integer('right')->nullable();

            // Drop newly added columns
            $table->dropColumn(['to_date', 'count']);
        });
    }
};
