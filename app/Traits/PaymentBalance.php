<?php

namespace App\Traits;

use App\DTO\DataStringAndObject;
use App\DTO\DataStringDto;
use App\Models\Balance;
use App\Models\FiatCoin;
use App\Models\Payment;

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
            new DataStringAndObject(status: false, error: 'При пополнении не найден баланс для юзера '.$payment->user_id);
        }

        $currencyIncome = FiatCoin::select('code')->where('id', $payment->currency_income_id)->first();
        if(!$currencyIncome){
            new DataStringAndObject(status: false, error: 'не найдена входящая валюта для '.$payment->user_id);
        }

        //конвертация входящей валюты в текущую валюту юзера
        $cost = $this->convert(currencyPrev: $currencyCallback->code, currencyNext: $currencyIncome->code);
        if (!$cost){
            new DataStringAndObject(status: false, error: 'данные о курсе валют не получены: ' . $currencyCallback->code.' '.$currencyIncome->code);
        }

        $newAmount = $this->convertTotal(cost: $cost, amount: $amountIncome);

        return new DataStringAndObject(status: true, dataString: $newAmount, dataObject: $balance);
    }
}
