<?php

return [
    'cryptocloud' => [
        'secret' => env('CRYPTO_CLOUD_SECRET'),
        'shop_id' => env('CRYPTO_CLOUD_SHOP_ID'),
        'api_key' => env('CRYPTO_CLOUD_API_KEY'),
        'base_url' => env('CRYPTO_CLOUD_BASE_URL'),
    ],

    'exnode' => [
        'secret' => env('EXNODE_PRIVATE'),
        'public' => env('EXNODE_PUBLIC'),
        'base_url' => env('EXNODE_BASE_URL'),
        'callback' => env('EXNODE_CALLBACK'),
    ],

    'status' =>[
        'not_paid' => 'not_paid', //статус для не оплаченных инвойсов
        'success' => 'success', //статус оплаченных инвойсов
    ],
];
