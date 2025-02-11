<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
    {
        DB::unprepared("DROP FUNCTION IF EXISTS public.\"getUserLevel\"(integer);");

        DB::unprepared("CREATE OR REPLACE FUNCTION public.\"getUserLevel\"(
                user_i integer)
            RETURNS TABLE(user_level integer, maxamount numeric, fullamount numeric) 
            LANGUAGE 'plpgsql'
            COST 100
            VOLATILE PARALLEL UNSAFE
            ROWS 1000

            AS \$BODY\$
            BEGIN
            RETURN QUERY
            SELECT 
                u.level,
                l.max_amount,
                COALESCE(SUM(i.win), 0)
            FROM user_params AS u
            LEFT JOIN level AS l ON l.id = u.level
            LEFT JOIN play_game AS i ON  i.user_id = user_i
            WHERE u.id = user_i
            GROUP BY u.level, l.max_amount;
            END;
            \$BODY\$;");
    }
};