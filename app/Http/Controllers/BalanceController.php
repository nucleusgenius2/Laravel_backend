<?php

namespace App\Http\Controllers;

use App\Http\Requests\BalanceRequest;
use App\Models\Account;
use App\Services\BalanceService;
use Illuminate\Support\Facades\Auth;


class BalanceController extends Controller
{
    public function index()
    {
        $balance = Account::select('balances.*')
            ->where('accounts.user_id', Auth::user()->id)
            ->rightJoin('balances', 'accounts.id', '=', 'balances.account_id')
            ->get();

        $this->status = 'success';
        $this->code = 200;
        $this->dataJson = $balance;

        return $this->responseJsonApi();
    }

    public function store(BalanceRequest $request, BalanceService $service)
    {
        $data = $request->validated();

        $balance = $service->addBalance($data);

        if($balance['status']){
            $this->status = 'success';
            $this->code = 200;
        }
        else{
            $this->code = 500;
            $this->message = $balance['error'];
        }

        return $this->responseJsonApi();
    }

    public function setDefault(BalanceRequest $request, BalanceService $service)
    {
        $data = $request->validated();

        $balance = $service->setDefault($data);

        if($balance['status']){
            $this->status = 'success';
            $this->code = 200;
        }
        else{
            $this->code = 500;
            $this->message = $balance['error'];
        }

        return $this->responseJsonApi();
    }


}
