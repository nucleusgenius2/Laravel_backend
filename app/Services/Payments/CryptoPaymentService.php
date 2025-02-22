<?php

namespace App\Services\Payments;

use App\DTO\DataObjectDto;
use App\Models\PayMethod;
use App\Models\User;

class CryptoPaymentService
{
    public function getMethods(string $country): DataObjectDto
    {
        $payMethods = PayMethod::select('code', 'name', 'network', 'type')
            ->where('status', true)
            ->whereIn('countries', [$country, 'all'])
            ->get();

        return new DataObjectDto(status: true, data:  $payMethods);
    }
}
