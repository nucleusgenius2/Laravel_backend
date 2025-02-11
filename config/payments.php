<?php

return [
    'cryptocloud' => [
        'secret' => env('CRYPTO_CLOUD_SECRET'),
        'shop_id' => env('CRYPTO_CLOUD_SHOP_ID'),
        'api_key' => env('CRYPTO_CLOUD_API_KEY'),
        'base_url' => env('CRYPTO_CLOUD_BASE_URL'),
    ],

    'status' =>[
        'not_paid' => 'not_paid', //статус для не оплаченных инвойсов
        'success' => 'success', //статус оплаченных инвойсов
    ],
];
