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
        DB::unprepared('
            CREATE OR REPLACE FUNCTION public."getUserLevel"(
              user_id integer)
                RETURNS TABLE(user_level integer, maxAmount numeric, fullAmount numeric)
                LANGUAGE plpgsql
                COST 100
                VOLATILE PARALLEL UNSAFE
                ROWS 1000
            AS $BODY$
            BEGIN
              RETURN QUERY
              SELECT
                u.level,
                l.max_amount,
                COALESCE(SUM(i.amount), 0)
              FROM user_params AS u
              LEFT JOIN level AS l ON l.id = u.level
              LEFT JOIN incom_payments AS i ON i.status::integer = 1 AND i.user = user_id
              WHERE u.id = user_id
              GROUP BY u.level, l.max_amount;
            END;
            $BODY$
        ');
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
