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
            $table->integer('bonus_id')->nullable();
			$table->decimal('bonus_amount', 20, 12)->nullable();
        });
    }

    public function down()
    {
		Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['bonus_id', 'bonus_amount']);
        });
    }

};
