<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up()
    {
        Schema::table('play_game', function (Blueprint $table) {
            $table->integer('currency_id');
        });
    }

    public function down()
    {
        Schema::table('play_game', function (Blueprint $table) {
            $table->dropColumn('currency_id');
        });
    }

};
