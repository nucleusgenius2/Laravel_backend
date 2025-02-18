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
            $table->dropColumn(['to_date', 'count', 'nominal']);
        });
    }

    public function down()
    {

    }

};
