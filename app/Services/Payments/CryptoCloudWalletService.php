<?php

namespace App\Services\Payments;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CryptoCloudWalletService
{
    protected string $apiKey;
    protected string $shopId;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('payments.cryptocloud.api_key');
        $this->shopId = config('payments.cryptocloud.shop_id');
        $this->baseUrl = config('payments.cryptocloud.base_url');
    }

    /**
     * Оплата на кошелек
     */
    public function createInvoice(User $user, string $currency): array
    {
        $orderId = uniqid();
        $url =$this->baseUrl.'/invoice/static/create';

log::info('кастом '.$orderId);
        $response = Http::withHeaders([
            'Authorization' => 'Token ' . $this->apiKey,
            'Content-Type'  => 'application/json',
        ])
            ->post( $url, [
                'shop_id'  => $this->shopId,
                'currency' => $currency,
                'identify' => $orderId,
            ]);
log::info($response);
        return [
            'status' => $response['status'],
            'data'  => $response['result']
        ];
    }
}
