<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InitProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init';

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
        // создание папки логов
        $logPath = storage_path('logs/exnode');

        if (!file_exists($logPath)) {
            mkdir($logPath, 0775, true);
            chmod($logPath, 0775);
            chown($logPath, 'www-data');
        }

        $logPath = storage_path('logs/cryptocloud');

        if (!file_exists($logPath)) {
            mkdir($logPath, 0775, true);
            chmod($logPath, 0775);
            chown($logPath, 'www-data');
        }



    }
}
