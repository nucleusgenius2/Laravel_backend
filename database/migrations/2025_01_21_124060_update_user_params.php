<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::table('user_params', function (Blueprint $table) {
            $table->dropColumn('currency');

            $table->integer('currency');
        });
    }

    public function down()
    {
        Schema::table('user_params', function (Blueprint $table) {
            $table->dropColumn('currency');

            $table->string('currency');
        });
    }
};
