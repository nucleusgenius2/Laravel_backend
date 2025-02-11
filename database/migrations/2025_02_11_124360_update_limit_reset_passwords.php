<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up()
    {
        Schema::table('limit_reset_passwords', function (Blueprint $table) {
			$table->string('code', 5);
        });
    }

    public function down()
    {
        Schema::table('limit_reset_passwords', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }

};
