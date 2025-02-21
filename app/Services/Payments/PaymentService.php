<?php

namespace App\Services\Payments;

use App\DTO\DataObjectDto;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function getPayments(User $user, array $data): DataObjectDto
    {
        $payments = Payment::select(
            'payments.amount',
            'fiat_coin.code as currency',
            'payments.invoice_uid as invoice_id',
            'payments.status',
            'payments.date_start',
            'payments.date_completion',
            'payments.bonus_amount',
            'income_fiat_coin.code as method'
        )
            ->where('payments.user_id', $user->id)
            ->join('fiat_coin', 'fiat_coin.id', '=', 'payments.currency_id')
            ->leftJoin('fiat_coin as income_fiat_coin', 'income_fiat_coin.id', '=', 'payments.currency_income_id')
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
