<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('balances', function (Blueprint $table) {
            // Проверяем, существует ли каждый столбец, перед его удалением
            if (Schema::hasColumn('balances', 'to_date')) {
                $table->dropColumn('to_date');
            }

            if (Schema::hasColumn('balances', 'count')) {
                $table->dropColumn('count');
            }

            if (Schema::hasColumn('balances', 'nominal')) {
                $table->dropColumn('nominal');
            }
        });
    }

    public function down()
    {

    }

};
