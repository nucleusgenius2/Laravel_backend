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
        Schema::create('config_winmove', function (Blueprint $table) {
            $table->string('param', 50)->comment('Parameter name');
            $table->boolean('is_active')->default(false)->comment('Active status');
            $table->integer('val')->comment('Value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('config_winmove');
    }
};
