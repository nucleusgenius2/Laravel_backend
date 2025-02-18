<?php

namespace App\Services\Payments;

use App\DTO\DataObjectDto;
use App\Models\Payment;
use App\Models\User;
use App\Models\Withdrawal;
use Carbon\Carbon;

class WithdrawalService
{
    public function getPayments(User $user) : DataObjectDto
    {
        $payments = Withdrawal::select(
            'withdrawals.amount_income',
            'withdrawals.currency_id',
            'withdrawals.status',
            'withdrawals.date_start',
            'withdrawals.date_completion',
            'withdrawals.processing',
            'fiat_coin.code'
        )
            ->where('withdrawals.user_id', $user->id)
            ->join('fiat_coin', 'fiat_coin.id', '=', 'withdrawals.currency_id')
            ->get();

        // корректируем дату
        $payments->transform(function ($payment) {
            $payment->date = $payment->date_completion ? Carbon::parse($payment->date_completion)->format('Y-m-d') : Carbon::parse($payment->date_start)->format('Y-m-d');

            return $payment;
        });


        return new DataObjectDto(status: true, data: $payments);
    }
}
