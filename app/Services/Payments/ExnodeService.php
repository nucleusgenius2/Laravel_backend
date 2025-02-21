<?php

namespace App\Services\Payments;

use App\DTO\DataArrayDto;
use App\DTO\DataEmptyDto;
use App\DTO\DataStringDto;
use App\Models\FiatCoin;
use App\Models\Payment;
use App\Models\User;
use App\Models\UserParam;
use App\Services\GenerateUniqueString;
use App\Traits\PaymentBalance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExnodeService
{
    use PaymentBalance;

    protected string $publicKey;

    protected string $privateKey;

    protected string $baseUrl;

    public function __construct()
    {
        $this->publicKey = config('payments.exnode.public');
        $this->privateKey = config('payments.exnode.secret');
        $this->baseUrl = config('payments.exnode.base_url');
    }

    public function createInvoice(User $user, string $currency, string $amount): DataStringDto
    {
        $orderId = GenerateUniqueString::generate(userId: $user->id, length: 40);

        $transactionData = [
            //'token' => 'BNB',
            'token' => $currency,
            'client_transaction_id' => $orderId,
            'transaction_description' => 'Some description',
            'address_type' => 'SINGLE',
            'call_back_url' =>  config('payments.exnode.callback'),
        ];

        // Генерируем кошелек
        $headers = $this->createWallet($transactionData);

        $response = Http::withHeaders($headers)
            ->post( $this->baseUrl.'/api/transaction/create/in', $transactionData);

        //log::info($response);

        if ($response->successful()) {

            DB::beginTransaction();

            try {
                $currency = FiatCoin::select('id','code','type')->where('code', $currency)->first();
                if (!$currency){
                    throw new \Exception('Валюта не доступна');
                }

                $currencyUserSelected = UserParam::where('id',$user->id)->value('currency_id');

                $payment = Payment::create([
                    'user_id' => $user->id,
                    'invoice_uid' => $orderId,
                    'processing' => 'exnode_wallet',
                    'amount_wait' => $amount,
                    'currency_id' => $currencyUserSelected,
                    'status' => config('payments.status.not_paid'),
                    'date_start' => Carbon::now()
                ]);

                if (!$payment){
                    throw new \Exception('Ошибка при создании платежных данных');
                }

                DB::commit();
                return new DataStringDto(status: true, data: $response['refer']);
            }
            catch (\Exception $e) {
                DB::rollBack();

                return new DataStringDto(status: false, error: $e->getMessage(), code: 500 );
            }
        }
        else{
            log::error( $response);
            return new DataStringDto(status: false, error: 'Ошибка при создании счета', code: 500);
        }
    }


    /**
     * Создание подписи запроса (сигнатура)
     * @param string $body
     * @param string $timestamp
     * @return string
     */
    private function generateSignature(string $body, string $timestamp): string
    {
        $message = $timestamp . $body;

        return hash_hmac('sha512', $message, $this->privateKey);
    }


    public function createWallet(array $transactionData): array
    {
        $timestamp = time();

        $body = json_encode($transactionData);

        $signature = $this->generateSignature($body, $timestamp);

        $headers = [
            'Accept' => 'application/json',
            'ApiPublic' => $this->publicKey,
            'Content-Type' => 'application/json',
            'Signature' => $signature,
            'Timestamp' => $timestamp,
        ];

        return $headers;
    }


    public function collback(array $requestData): DataEmptyDto
    {
        if (!isset($requestData['tracker_id'])) {
            return new DataEmptyDto(status: false, error: 'Нет данных', code: 422);
        }

        $trackerId = $requestData['tracker_id'];

        // Создаем подпись запроса
        $timestamp = time();
        $body = json_encode(['tracker_id' => $trackerId]);
        $signature = $this->generateSignature($body, $timestamp);

        $headers = [
            'Accept' => 'application/json',
            'ApiPublic' => $this->publicKey,
            'Signature' => $signature,
            'Timestamp' => $timestamp,
            'Content-Type' => 'application/json',
        ];

        $response = Http::withHeaders($headers)->post($this->baseUrl . '/api/transaction/get', [
            'tracker_id' => $trackerId
        ]);
        // Проверяем успешность ответа
        if (!$response->successful()) {
            log::channel('exnode')->error('Ошибка запроса Exnode'  .$trackerId);
            Log::channel('exnode')->error($response->body());
        }
        else {
            $responseData = $response->json();

            if (!isset($responseData['transaction'])) {
                log::channel('exnode')->error('ответ API не содержит transaction');
            }

            $transactionData = $responseData['transaction'];

            log::channel('exnode')->info('Exnode Transaction Response', ['data' => $transactionData]);

            if ($transactionData['status'] === 'SUCCESS') {
                try {
                    DB::beginTransaction();

                    $payment = Payment::where([
                        ['invoice_uid', $transactionData['client_transaction_id']],
                        ['status', config('payments.status.not_paid')]
                    ])->first();

                    if (!$payment) {
                        throw new \Exception('Не найден открытый инвойс для ' . $transactionData['client_transaction_id']);
                    }

                    $currencyCallback = FiatCoin::where('code', $transactionData['token'])->first();
                    if (!$currencyCallback) {
                        throw new \Exception('Оплатил не допустимой валютой ' .$transactionData['token'] . ' юзер ' . $payment->user_id);
                    }

                    $payment->date_completion = Carbon::now();
                    $payment->currency_income_id = $currencyCallback->id;
                    $payment->status = config('payments.status.success');
                    $payment->amount_income = $transactionData['total'];

                    $dataStringAndObject = $this->getConvertedAmount(
                        payment: $payment,
                        currencyCallback: $currencyCallback,
                        amountIncome: $transactionData['total']
                    );
                    if ($dataStringAndObject->status) {
                        $balance = $dataStringAndObject->dataObject;

                        $newAmount = $dataStringAndObject->dataString;

                        $balance->amount = ($balance->amount ?? 0) + $newAmount;
                        $payment->amount = $newAmount;

                        if (!$payment->save() || !$balance->save()) {
                            throw new \Exception('Ошибка сохранения данных в БД');
                        }
                    } else {
                        throw new \Exception($dataStringAndObject->error);
                    }

                    DB::commit();

                    return new DataEmptyDto(status: true);
                }
                catch (\Exception $e) {
                    DB::rollBack();
                    return new DataEmptyDto(status: false, error: $e, code: 500);
                }
            }
            else {
                log::channel('exnode')->error('Транзакция не завершена ' . $trackerId);
                return new DataEmptyDto(status: false, error: 'Ошибка обновления платежа', code: 500);
            }

        }
    }


    public function getAvailableTokens(): DataArrayDto
    {
        $timestamp = time();

        $signature = $this->generateSignature('', $timestamp);

        $headers = [
            'Accept' => 'application/json',
            'ApiPublic' => $this->publicKey,
            'Signature' => $signature,
            'Timestamp' => $timestamp,
        ];

        $response = Http::withHeaders($headers)
            ->get( $this->baseUrl. '/user/token/fetch');

        if ($response->successful()) {
            $tokens = $response->json();

            return new DataArrayDto(status: true, data: $tokens['tokens']);
        }
        else{
            return new DataArrayDto(status: true, error: json_encode($response->body()));
        }

    }


}
