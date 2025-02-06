<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('user_params', function (Blueprint $table) {
            $table->renameColumn('currency', 'currency_id');
        });
    }

    public function down()
    {
        Schema::table('user_params', function (Blueprint $table) {
            $table->renameColumn('currency_id', 'currency');
        });
    }
};
