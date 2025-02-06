<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fiat_coin', function (Blueprint $table) {
			$table->string('type', 50);
        });
    }

    public function down(): void
    {
        Schema::table('fiat_coin', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
