<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_params', function (Blueprint $table) {
            $table->integer('cfg_hidden_name')->default('0');
            $table->integer('cfg_hidden_stat')->default('0');
        });
    }
    public function down(): void
    {
        Schema::table('user_params', function (Blueprint $table) {
            $table->dropColumn(['cfg_hidden_name', 'cfg_hidden_stat']);
        });
    }

};
