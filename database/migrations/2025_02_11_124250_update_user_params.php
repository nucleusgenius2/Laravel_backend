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
            $table->integer('cfg_sound')->nullable();
            $table->integer('cfg_music')->nullable();
            $table->integer('cfg_effect')->nullable();
            $table->integer('cfg_hidden_game')->nullable();
            $table->integer('cfg_animation')->nullable();
        });
    }

    public function down()
    {
        Schema::table('user_params', function (Blueprint $table) {
            $table->dropColumn([
                'cfg_sound',
                'cfg_music',
                'cfg_effect',
                'cfg_hidden_game',
                'cfg_animation',
            ]);
        });
    }

};
