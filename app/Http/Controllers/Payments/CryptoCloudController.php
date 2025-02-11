<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Http\Requests\CryptoCloudRequest;
use App\Models\Countries;
use App\Services\Payments\CryptoCloudService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class CryptoCloudController extends Controller
{
    protected CryptoCloudService $cryptoCloudService;

    public function __construct(CryptoCloudService $cryptoCloudService)
    {
        $this->cryptoCloudService = $cryptoCloudService;
    }

    public function createPayment(CryptoCloudRequest $request)
    {
        $data = $request->validated();

        $invoice = $this->cryptoCloudService->createInvoice(
            user: $request->user(),
            amount: $data['amount'],
            currency: $data['currency']
        );

        if ($invoice['status'] =='success'){
            $this->status = 'success';
            $this->code = 200;
            $this->dataJson = $invoice['data']['link'];
        }
        else{
            $this->code = 500;
            $this->dataJson = $invoice;
        }

        return $this->responseJsonApi();
    }


    public function callback(Request $request)
    {
        log::info('колбек');
        log::info($request->all());
        return response()->json();
    }

}
