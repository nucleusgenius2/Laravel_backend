<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up()
    {
        DB::unprepared("DROP FUNCTION IF EXISTS public.\"getMainBalance\"(integer);");

        DB::unprepared("
            CREATE OR REPLACE FUNCTION public.\"getMainBalance\"(
                user_i integer)
                RETURNS TABLE(amount numeric)
                LANGUAGE 'plpgsql'
                COST 100
                VOLATILE PARALLEL UNSAFE
                ROWS 1000
            AS \$BODY\$
            BEGIN
                RETURN QUERY
                SELECT
                    l.amount
                FROM user_params AS u
                LEFT JOIN accounts AS a
                    ON a.user_id = user_i
                    AND a.fiat_coin = u.currency_id
                    AND a.type = 'main'
                LEFT JOIN balances AS l
                    ON l.account_id = a.id
                WHERE u.id = user_i
                GROUP BY l.amount;
            END;
            \$BODY\$;
        ");
    }

    public function down()
    {
        DB::unprepared("DROP FUNCTION IF EXISTS public.\"getMainBalance\"(integer);");
    }

};
