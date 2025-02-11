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
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CryptoCloudWalletService
{
    use CurrencyRateProvider;
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

log::info('кастом валлет id'.$orderId);
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
        if($response['status']){

            DB::beginTransaction();

            try {
                $currency = FiatCoin::select('id','code','type')->where('code', $currency)->first();
                if (!$currency){
                    throw new \Exception('Валюта не доступна');
                }

                $currencyUserSelected = UserParam::where('id',$user->id )->value('currency_id');

                log::info('id валюты юзера '.  $currencyUserSelected);
                $payment = Payment::create([
                    'user_id' => $user->id,
                    'invoice_uid' => $orderId,
                    'processing' => 'crypto_cloud_wallet',
                    'amount_income' => $amount,
                    'currency_income_id' => $currencyUserSelected,
                    'status' => config('payments.status.not_paid'),
                    'date_start' => Carbon::now()
                ]);

                if (!$payment){
                    throw new \Exception('Ошибка при создании платежных данных');
                }

                DB::commit();
                log::info( $response['result']);
                return new DataStringDto(status: true, data: $response['result']['address']);
            }
            catch (\Exception $e) {
                DB::rollBack();

                return new DataStringDto(status: false, error: $e->getMessage() );
            }
        }
        else{
            log::error( $response['result']);
            return new DataStringDto(status: true, error: json_encode($response['result']));
        }

    }

    public function collback(array $requestData)
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
            log::error('Не валидный запрос на колбек крипто клауд');
            log::error(json_encode($requestData));
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
                    $payment->amount = $requestData['amount_crypto'];
                    $payment->currency_id = $currencyCallback->id;
                    $payment->status = config('payments.status.success');
                    $payment->save();

                    log::info('user_id '.$payment->user_id);

                    $balance = Balance::where('id', function ($query) use ($payment) {
                        $query->select('balances.id')
                            ->from('user_params')
                            ->where('user_params.id', $payment->user_id)
                            ->join('accounts', function ($join) {
                                $join->on('user_params.id', '=', 'accounts.user_id')
                                    ->where('accounts.type', '=', 'main')
                                    ->on('accounts.fiat_coin', '=', 'user_params.currency_id');
                            })
                            ->join('balances', 'accounts.id', '=', 'balances.account_id');
                    })->first();
/*
                    $balanceId = UserParam::select('balances.id as balance_id')
                        ->where('user_params.id',$payment->user_id)
                        ->join('accounts', function ($join) {
                            $join->on('user_params.id', '=', 'accounts.user_id')
                                ->where('accounts.type', '=', 'main')
                                ->on('accounts.fiat_coin', '=', 'user_params.currency_id');
                        })
                        ->join('balances', 'accounts.id', '=', 'balances.account_id' )
                        ->first();
*/

                    if(!$balance){
                        throw new \Exception('При пополнении не найден баланс для юзера '.$payment->user_id);
                    }

                    $currencyIncome = FiatCoin::select('code')->where('id', $payment->currency_income_id)->first();
                    if(!$currencyIncome){
                        throw new \Exception('не найдена входящая валюта для '.$payment->user_id);
                    }


                    //конвертация входящей валюты в текущую валюту юзера
                    $cost = $this->convert(currencyPrev: $currencyCallback->code, currencyNext:  $currencyIncome->code);
                    if (!$cost){
                        throw new \Exception('Данные о курсе валют не получены: ' . $currencyCallback->code.' '.$currencyIncome->code);
                    }

                    $newAmount = $this->convertTotal(cost: $cost, amount: $requestData['amount_crypto']);

    log::channel('cryptocloud')->error('currencyCallback->code'  .$currencyCallback->code);
   log::channel('cryptocloud')->error('urrencyIncome->code'  . $currencyIncome->code);
 log::channel('cryptocloud')->error('итог'  .$newAmount);

                    if( $balance->amount ){
                        $balance->amount = $balance->amount + $newAmount;
                    }
                    else{
                        $balance->amount = $newAmount;
                    }

                    $balance->save();

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

