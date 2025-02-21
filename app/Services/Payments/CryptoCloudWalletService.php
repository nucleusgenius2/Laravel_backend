<?php

namespace App\Services\Payments;

use App\DTO\DataArrayDto;
use App\DTO\DataEmptyDto;
use App\DTO\DataStringDto;
use App\Models\Account;
use App\Models\Balance;
use App\Models\FiatCoin;
use App\Models\Payment;
use App\Models\User;
use App\Models\UserParam;
use App\Services\GenerateUniqueString;
use App\Traits\CurrencyRateProvider;
use App\Traits\PaymentBalance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CryptoCloudWalletService
{
    use PaymentBalance;

    protected string $apiKey;

    protected string $shopId;

    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('payments.cryptocloud.api_key');
        $this->shopId = config('payments.cryptocloud.shop_id');
        $this->baseUrl = config('payments.cryptocloud.base_url');
    }


    public function createInvoice(User $user, string $currency, string $amount): DataStringDto
    {
        $orderId = GenerateUniqueString::generate(userId: $user->id, length: 40);
        $url = $this->baseUrl.'/invoice/static/create';

        $response = Http::withHeaders([
            'Authorization' => 'Token ' . $this->apiKey,
            'Content-Type'  => 'application/json',
        ])
            ->post( $url, [
                'shop_id'  => $this->shopId,
                'currency' => $currency,
                'identify' => $orderId,
            ]);

        if($response['status']){

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
                    'processing' => 'crypto_cloud_wallet',
                    'amount_wait' => $amount,
                    'currency_id' => $currencyUserSelected,
                    'status' => config('payments.status.not_paid'),
                    'date_start' => Carbon::now()
                ]);

                if (!$payment){
                    throw new \Exception('Ошибка при создании платежных данных');
                }

                DB::commit();
                return new DataStringDto(status: true, data: $response['result']['address']);
            }
            catch (\Exception $e) {
                DB::rollBack();

                return new DataStringDto(status: false, error: $e->getMessage() );
            }
        }
        else{
            log::channel('cryptocloud')->error( $response['result']);
            return new DataStringDto(status: true, error: json_encode($response['result']));
        }

    }

    public function collback(array $requestData): void
    {
        $validated = Validator::make($requestData, [
            'status' => 'required|string|in:success,error',
            'invoice_id' => 'required|string|max:200',
            'amount_crypto' => 'required|numeric',
            'currency' => 'required|string|size:3',
            'order_id' => 'required|string|max:100',
            'token' => 'required|string',
            'invoice_info' => 'nullable|string'
        ]);

        if ($validated->fails()) {
            log::channel('cryptocloud')->error('Не валидный запрос на колбек крипто клауд');
            log::channel('cryptocloud')->error(json_encode($requestData));
        } else {
            if($requestData['status'] === 'success'){
                DB::beginTransaction();
                try {
                    $payment = Payment::where([
                        ['invoice_uid', $requestData['order_id']],
                        ['status', config('payments.status.not_paid')]
                    ])->first();

                    if (!$payment) {
                        throw new \Exception('не найден открытый инвойс для '. $requestData['invoice_id']);
                    }

                    $currencyCallback = FiatCoin::where('code', $requestData['currency'])->first();
                    if(!$currencyCallback){
                        throw new \Exception('Оплатил не допустимой валютой '.$requestData['invoice_id'].' юзер '.$payment->user_id);
                    }

                    $payment->date_completion = Carbon::now();
                    $payment->currency_income_id = $currencyCallback->id;
                    $payment->status = config('payments.status.success');
                    $payment->amount_income = $requestData['amount_crypto'];

                    $dataStringAndObject = $this->getConvertedAmount(
                        payment: $payment,
                        currencyCallback: $currencyCallback,
                        amountIncome: $requestData['amount_crypto']
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
                }
                catch (\Exception $e) {
                    DB::rollBack();

                    log::channel('cryptocloud')->error('Пришел колбек, завершился ошибкой: '.$e->getMessage());
                }
            }

        }


    }
}

