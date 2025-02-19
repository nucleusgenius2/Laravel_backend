<?php

namespace App\Services\Payments;

use App\DTO\DataStringDto;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExnodeService
{
    protected string $publicKey;
    protected string $privateKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->publicKey = config('payments.exnode.public');
        $this->privateKey = config('payments.exnode.secret');
        $this->baseUrl = config('payments.exnode.base_url').'/api/transaction/create/in';
    }
    public function createInvoice(User $user): DataStringDto
    {
        $transactionData = [
            'token' => 'BNB',
            'client_transaction_id' => uniqid(),
            'transaction_description' => 'Some description',
            'address_type' => 'SINGLE',
            'call_back_url' =>  config('payments.cryptocloud.callback'),
        ];

        // Генерируем кошелек
        $headers = $this->createWallet($transactionData);

        // Отправка запроса с использованием Laravel Http фасада
        $response = Http::withHeaders($headers)
            ->post($this->baseUrl, $transactionData);

        log::info($response);
        // Обрабатываем ответ
        if ($response->successful()) {
           // return $response->json();
        }

        return new DataStringDto(status: true, data: json_encode($response));

    }

    // Метод для создания подписи
    private function generateSignature($body, $timestamp)
    {
        $message = $timestamp . $body;

        return hash_hmac('sha512', $message, $this->privateKey);
    }

    // Метод для создания кошелька
    public function createWallet($transactionData): array
    {
        $timestamp = time();

        $body = json_encode($transactionData);

        $signature = $this->generateSignature($body, $timestamp);

        // Формируем заголовки
        $headers = [
            'Accept' => 'application/json',
            'ApiPublic' => $this->publicKey,
            'Content-Type' => 'application/json',
            'Signature' => $signature,
            'Timestamp' => $timestamp,
        ];


       // $response = $this->sendRequest($body, $headers);

        return $headers;
    }



    public function testToken()
    {
// Текущее время в формате Unix
        $timestamp = time();

        // Создаем подпись для запроса
        $signature = $this->generateSignature('', $timestamp); // Пустое тело для GET-запроса

        // Формируем заголовки
        $headers = [
            'Accept' => 'application/json',
            'ApiPublic' => $this->publicKey,
            'Signature' => $signature,
            'Timestamp' => $timestamp,
        ];

        // Отправляем GET-запрос на получение доступных токенов
        $response = Http::withHeaders($headers)
            ->get(config('payments.exnode.base_url'). '/user/token/fetch');

        // Логируем ответ для отладки
        Log::info('Exnode API response for fetch tokens', ['response' => $response->body()]);
        Log::info( $response);


        // Обрабатываем ответ
        if ($response->successful()) {
            $tokens = $response->json();
            Log::info('Available tokens:', $tokens);
            return $tokens; // Возвращаем доступные токены
        }

        // Если запрос не успешен, возвращаем ошибку
        Log::error('Error fetching available tokens', ['message' => $response->body()]);

        return new DataStringDto(status: true, data: $response->body());
    }


}
