<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pay_methods', function (Blueprint $table) {
            $table->id();
            $table->string('code', 4);
            $table->string('name', 15);
            $table->string('network', 4)->nullable();
            $table->jsonb('processing');
            $table->boolean('status');
            $table->string('countries', 3)->default('all');
            $table->string('type', 20);

            //уникальный индекс по трем колонкам
            $table->unique(['code', 'network', 'countries']);
        });

    }

    public function down(): void {
        Schema::dropIfExists('pay_methods');
    }
};
