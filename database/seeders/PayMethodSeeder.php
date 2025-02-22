<?php

namespace Database\Seeders;

use App\Models\FiatCoin;
use App\Models\PayMethod;
use Illuminate\Database\Seeder;

class PayMethodSeeder extends Seeder
{
    public function run(): void
    {
        if(!PayMethod::exists()) {
            PayMethod::insert([
                [
                    'code' => 'RUB',
                    'name' => 'Ruble',
                    'processing' => json_encode(["paytop"]),
                    'status' => false,
                    'countries' => 'RU',
                    'network' => null,
                    'type' => 'fiat'
                ],
                [
                    'code' => 'BTC',
                    'name' => 'Bitcoin',
                    'processing' => json_encode(["crypto_cloud", "exnode"]),
                    'status' => false,
                    'countries' => 'all',
                    'network' => null,
                    'type' => 'crypto',
                ],
                [
                    'code' => 'USDT',
                    'name' => 'Tether',
                    'processing' => json_encode(["crypto_cloud", "exnode"]),
                    'status' => false,
                    'countries' => 'all',
                    'network' => 'TRX',
                    'type' => 'crypto',
                ],
                [
                    'code' => 'USDT',
                    'name' => 'Tether',
                    'processing' => json_encode(["crypto_cloud", "exnode"]),
                    'status' => false,
                    'countries' => 'all',
                    'network' => 'ETH',
                    'type' => 'crypto',
                ],
                [
                    'code' => 'USDT',
                    'name' => 'Tether',
                    'processing' => json_encode(["crypto_cloud", "exnode"]),
                    'status' => false,
                    'countries' => 'all',
                    'network' => 'TON',
                    'type' => 'crypto',
                ],
                [
                    'code' => 'ETH',
                    'name' => 'Ethereum',
                    'processing' => json_encode(["crypto_cloud", "exnode"]),
                    'status' => false,
                    'countries' => 'all',
                    'network' => null,
                    'type' => 'crypto',
                ],
                [
                    'code' => 'BNB',
                    'name' => 'Binance Coin',
                    'processing' => json_encode(["crypto_cloud", "exnode"]),
                    'status' => true,
                    'countries' => 'all',
                    'network' => null,
                    'type' => 'crypto',
                ],
            ]);
        }
    }
}
