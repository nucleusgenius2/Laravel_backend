<?php

namespace App\Services\Payments;

use App\DTO\DataObjectDto;
use App\Models\Payment;
use App\Models\User;
use App\Models\Withdrawal;
use Carbon\Carbon;

class WithdrawalService
{
    public function getPayments(User $user, array $data) : DataObjectDto
    {
        $payments = Withdrawal::select(
            'withdrawals.amount',
            'fiat_coin.code as currency',
            'withdrawals.invoice_uid as invoice_id',
            'withdrawals.status',
            'withdrawals.date_start',
            'withdrawals.date_completion',
            'income_fiat_coin.code as method'
        )
            ->where('withdrawals.user_id', $user->id)
            ->join('fiat_coin', 'fiat_coin.id', '=', 'withdrawals.currency_id')
            ->leftJoin('fiat_coin as income_fiat_coin', 'income_fiat_coin.id', '=', 'withdrawals.currency_income_id')
            ->paginate($data['count'] ?? 5, ['*'], 'page',  $data['page'] ?? 1);


        // корректируем дату
        $payments->transform(function ($payment) {
            $payment->date = $payment->date_completion ?? $payment->date_start;

            unset($payment->date_completion, $payment->date_start);
            return $payment;
        });


        return new DataObjectDto(status: true, data: $payments);
    }
}
