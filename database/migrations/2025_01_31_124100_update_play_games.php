<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('play_game', function (Blueprint $table) {
            $table->dropColumn('user');
            $table->unsignedBigInteger('user_id')->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('play_game', function (Blueprint $table) {
            $table->dropColumn('user_id'); 
            $table->string('user')->after('id'); 
        });
    }
};
