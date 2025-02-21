<?php

namespace App\Traits;

use App\DTO\DataStringAndObject;
use App\DTO\DataStringDto;
use App\Models\Balance;
use App\Models\FiatCoin;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

trait PaymentBalance
{
    use CurrencyRateProvider;

    /**
     * Возвращает сумму пополнения уже сконвертированную в главную валюту юзера, а также объект баланса юзера
     * @param Payment $payment
     * @param FiatCoin $currencyCallback
     * @param string $amountIncome
     * @return DataStringAndObject
     */
    protected function getConvertedAmount(Payment $payment, FiatCoin $currencyCallback, string $amountIncome): DataStringAndObject
    {
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

        if(!$balance){
            return new DataStringAndObject(status: false, error: 'При пополнении не найден баланс для юзера '.$payment->user_id);
        }

        $currencyMain = FiatCoin::where('id', $payment->currency_id)->value('code');
        if(!$currencyMain){
            return new DataStringAndObject(status: false, error: 'Не найдена выбранная валюта юзера '.$payment->user_id);
        }

        //конвертация входящей валюты в текущую валюту юзера
        $cost = $this->convert(currencyPrev: $currencyCallback->code, currencyNext: $currencyMain);
        if (!$cost){
            return new DataStringAndObject(status: false, error: 'Данные о курсе валют не получены: ' . $currencyCallback.' '.$currencyMain->code);
        }

        $newAmount = $this->convertTotal(cost: $cost, amount: $amountIncome);

        return new DataStringAndObject(status: true, dataString: $newAmount, dataObject: $balance);
    }
}
