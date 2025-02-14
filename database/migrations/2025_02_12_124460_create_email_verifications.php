<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('email_verifications', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('email', 100);
            $table->string('code', 5);
            $table->integer('count')->default(0);
  
        });
    }

    public function down()
    {
        Schema::dropIfExists('email_verifications'); 
    }
};
