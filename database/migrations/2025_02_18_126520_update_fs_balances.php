<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('fs_balances', 'bonus_id')) {
            Schema::table('fs_balances', function (Blueprint $table) {
                $table->integer('bonus_id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('fs_balances', 'bonus_id')) {
            Schema::table('fs_balances', function (Blueprint $table) {
                $table->dropColumn('bonus_id');
            });
        }
    }
};
