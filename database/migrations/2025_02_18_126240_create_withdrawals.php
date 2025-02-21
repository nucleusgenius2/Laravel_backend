<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('invoice_uid', 200)->comment('уникальный id от сервиса');
            $table->decimal('amount', 20, 12)->comment('сумма от сервиса');
            $table->decimal('amount_income', 20, 12)->nullable()->comment('входящая сумма');
            $table->string('processing', 50)->comment('платежная система');
            $table->integer('currency_id')->comment('валюта от сервиса');
            $table->integer('currency_income_id')->nullable()->comment('входящая валюта');
            $table->string('status', 30);
            $table->timestamp('date_completion')->nullable();
            $table->timestamp('date_start');
        });
    }


    public function down()
    {
        Schema::dropIfExists('withdrawals');
    }

};
