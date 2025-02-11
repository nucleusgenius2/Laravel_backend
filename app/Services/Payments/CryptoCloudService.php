<?php

namespace App\Services\Payments;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CryptoCloudService
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
     * Создание счета на оплату
     */
    public function createInvoice(User $user, string $amount, string $currency): array
    {
        $orderId = uniqid();

        $response = Http::withHeaders([
            'Authorization' => 'Token ' . $this->apiKey,
            'Content-Type'  => 'application/json',
        ])
            ->post("{$this->baseUrl}/invoice/create", [
                'shop_id'  => $this->shopId,
                'amount'   => $amount,
                'currency' => $currency,
                'order_id' => $orderId,
                'email' => $user->email
            ]);

        return [
            'status' => $response['status'],
            'data'  => $response['result']
        ];
    }

    /**
     * Получение информации о платеже
     */
    public function getInvoiceStatus(string $invoiceId)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Token ' . $this->apiKey,
        ])->get("{$this->baseUrl}/v1/invoice/info/{$invoiceId}");

        log::info($response->json());
        return $response->json();
    }
}
