<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       DB::statement("
            CREATE PROCEDURE getUserLevel (IN user_id INT)
            BEGIN
                DECLARE user_level INT;
                DECLARE maxAmount DECIMAL(10,2);
                DECLARE fullAmount DECIMAL(10,2);

                SELECT level INTO user_level FROM user_params WHERE id = user_id;
                SELECT max_amount INTO maxAmount FROM level WHERE id = user_level;
                SELECT SUM(amount) INTO fullAmount FROM incom_payments WHERE status = 1 AND user = user_id;
                SELECT user_level, maxAmount, fullAmount;
            END
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP PROCEDURE IF EXISTS getUserLevel");
    }
};
