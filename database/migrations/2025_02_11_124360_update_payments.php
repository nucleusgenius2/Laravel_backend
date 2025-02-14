<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
			$table->decimal('amount_wait', 20, 12)->nullable()->comment('Ожидаемая сумма оплаты');
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('amount_wait');
        });
    }

};
