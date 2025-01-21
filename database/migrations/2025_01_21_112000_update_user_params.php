<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_params', function (Blueprint $table) {
            $table->string('referal', 10)->change();

            $table->integer('refer_id')->nullable();
        });
    }

    /**
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_params', function (Blueprint $table) {
            $table->integer('referal')->change();

            $table->dropColumn('refer_id');
        });
    }
};
