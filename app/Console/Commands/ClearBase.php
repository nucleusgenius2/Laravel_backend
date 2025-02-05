<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearBase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:clear-base';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Проверяем, включен ли режим отладки
        if (!config('app.debug')) {
            $this->error('дебаг мод не включен.');
            return 1;
        }

        $tables = ['users', 'user_params', 'accounts', 'balances', 'config_winmove', 'fiat_coin', 'countries'];

        foreach ($tables as $table) {
            DB::table($table)->truncate();
            $this->info("Таблица {$table} очищена.");
        }

        $this->info('таблицы users user_params accounts balances fiat_coin countries очищены');

        return 0;
    }
}
