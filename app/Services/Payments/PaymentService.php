<?php

namespace App\Services\Payments;

use App\DTO\DataObjectDto;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;

class PaymentService
{
    public function getPayments(User $user) : DataObjectDto
    {
        $payments = Payment::select(
            'payments.amount_income',
            'payments.currency_id',
            'payments.status',
            'payments.date_start',
            'payments.date_completion',
            'payments.processing',
            'payments.bonus_amount',
            'fiat_coin.code'
        )
            ->where('payments.user_id', $user->id)
            ->join('fiat_coin', 'fiat_coin.id', '=', 'payments.currency_id')
            ->get();

        // корректируем дату
        $payments->transform(function ($payment) {
            $payment->date = $payment->date_completion ? Carbon::parse($payment->date_completion)->format('Y-m-d') : Carbon::parse($payment->date_start)->format('Y-m-d');

            return $payment;
        });


        return new DataObjectDto(status: true, data: $payments);
    }
}
