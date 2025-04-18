<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('invoice_uid', 200)->comment('уникальный id от сервиса');
            $table->decimal('amount', 20, 12)->nullable()->comment('сумма от сервиса');
            $table->decimal('amount_income', 20, 12)->comment('входящая сумма');
            $table->string('processing', 50)->comment('платежная система');
            $table->integer('currency_id')->nullable()->comment('валюта от сервиса');
            $table->integer('currency_income_id')->comment('входящая валюта');
            $table->string('status', 30);
            $table->timestamp('date_completion')->nullable();
            $table->timestamp('date_start');
        });
    }


    public function down()
    {
		Schema::table('incom_payments', function (Blueprint $table) {
            $table->dropColumn([
                'user_id',
                'invoice_uid',
                'amount',
                'amount_income',
                'processing',
                'currency_id',
                'currency_income_id',
                'status',
                'date_completion',
                'date_start'
            ]);
        });
    }
};
