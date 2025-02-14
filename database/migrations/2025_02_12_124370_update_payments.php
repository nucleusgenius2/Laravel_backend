<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('amount', 20, 12)->nullable(false)->change();

            $table->decimal('amount_income', 20, 12)->nullable()->change();

            $table->integer('currency_id')->nullable(false)->change();

            $table->integer('currency_income_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('amount', 20, 12)->nullable()->change();

            $table->decimal('amount_income', 20, 12)->nullable(false)->change();

            $table->integer('currency_id')->nullable()->change();

            $table->integer('currency_income_id')->nullable(false)->change();
        });
    }

};
