<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Balance;
use App\Models\FiatCoin;
use App\Models\UserParam;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BalanceService
{
    public function addBalance(array $data): array
    {
        $user = Auth::user();

        DB::beginTransaction();

        try {
            $fiat = FiatCoin::select('id')->where('code', $data['currency'])->first();

            if(!$fiat){
                throw new \Exception('Не валидный код валюты: ' . $data['currency']);
            }

            if(Account::where([['user_id', $user->id],['fiat_coin' , $fiat->id]])->exists()){
                throw new \Exception('счет с данной валютой уже есть ' . $data['currency']);
            }

            $accountMain = Account::create([
                'user_id' => $user->id,
                'type' => 'main',
                'fiat_coin' => $fiat->id
            ]);

            $accountMintwin = Account::create([
                'user_id' => $user->id,
                'type' => 'mintwin',
                'fiat_coin' => $fiat->id
            ]);

            $balance = [
                ['amount' => 0, 'account_id' => $accountMain->id,'count' => 0 ],
                ['amount' => 0, 'account_id' => $accountMintwin->id,'count' => 0],
            ];

            Balance::insert($balance);

            DB::commit();


            return [
                'status' => true,
            ];

        }
        catch (\Exception $e) {
            DB::rollBack();

            return [
                'status' => false,
                'error' => $e->getMessage(),
            ];

        }
    }

    public function setDefault(array $data): array
    {
        $user = Auth::user();
        DB::beginTransaction();

        try {
            $fiat = FiatCoin::select('id')->where('code', $data['currency'])->first();

            if(!$fiat){
                throw new \Exception('Не валидный код валюты: ' . $data['currency']);
            }

            if(!Account::where([['user_id', $user->id],['fiat_coin' , $fiat->id]])->exists()){
                throw new \Exception('у юзера нет счета с данной валютой ' . $data['currency']);
            }

            $update = UserParam::where('id', $user->id)->update([
                'currency' => $fiat->id
            ]);

           //тут мы должны с конвертировать баланс с бонусного счета в выбранную по умолчанию валюту

            DB::commit();

            return [
                'status' => true
            ];
        }
        catch (\Exception $e) {
            DB::rollBack();

            return [
                'status' => false,
                'error' => $e->getMessage(),
            ];

        }
    }
}
