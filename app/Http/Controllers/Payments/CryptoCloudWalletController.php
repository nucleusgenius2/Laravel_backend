<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Http\Requests\CryptoCloudRequest;
use App\Http\Requests\CryptoCloudWalletRequest;
use App\Models\Countries;
use App\Services\Payments\CryptoCloudWalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class CryptoCloudWalletController extends Controller
{
    protected CryptoCloudWalletService $service;

    public function __construct(CryptoCloudWalletService $service)
    {
        $this->service = $service;
    }

    public function createPayment(CryptoCloudWalletRequest $request)
    {
        $data = $request->validated();
        $invoice = $this->service->createInvoice(
            user: $request->user(),
            currency: $data['currency']
        );

        if ($invoice['status'] =='success'){
            $this->status = 'success';
            $this->code = 200;
            $this->dataJson = $invoice['data'];
        }
        else{
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
