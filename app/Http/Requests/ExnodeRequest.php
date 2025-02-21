<?php

namespace App\Http\Requests;

use App\Exceptions\ValidationExceptionResponse;
use Illuminate\Foundation\Http\FormRequest;

class ExnodeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|string',
            'currency' => [
                'required',
                'string',
                'in:BTC,BCH,ETH,LTC,DASH,DOGE,TRX,USDTTRC,BNB,TON,DAIERC,MATIC,USDTERC,USDTBSC,BTCBBSC,USDTPOLY,USDCERC,NOT,USDTTON,AVAX,DAIPOLY,DYDXERC,HMSTRTON'
            ],
        ];

    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $errors = $validator->errors();

        throw new ValidationExceptionResponse($errors);
    }
}
