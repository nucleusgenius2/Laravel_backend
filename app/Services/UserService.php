<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Balance;
use App\Models\ConfigWinmove;
use App\Models\FiatCoin;
use App\Models\User;
use App\Models\UserParam;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserService
{

    public function createUser(array $data): array
    {
        DB::beginTransaction();
        try {
            $userData = [
                'name' => GenerateUniqueString::generateName(10),
                'created_at' => Carbon::now(),
                'password' => $data['password'] ?? Str::random()
            ];
            if (isset($data['email'])){
                $userData['email'] = $data['email'];
            }
            if (isset($data['phone'])){
                $userData['phone'] = $data['phone'];
            }
            if (isset($data['provider_type'])){
                $userData['provider_type'] = $data['provider_type'];
            }
            if (isset($data['provider_id'])){
                $userData['provider_id'] = $data['provider_id'];
            }
            //создали юзера
            $user = User::create($userData);

            $fiat = FiatCoin::select('id')->where('code', $data['currency'])->first();

            if(!$fiat){
                throw new \Exception('Не валидный код валюты: ' . $data['currency']);
            }

            $dataParam = [
                'id' => $user->id,
                'currency' => $fiat->id,
                'level' => 1,
                'referal' => GenerateUniqueString::generate($user->id, 10),
            ];

            if (isset($data['refCode'])) {
                $userReferal = UserParam::select('id')->where('referal', $data['refCode'])->first();

                if ($userReferal) {
                    $dataParam['refer_id'] = $userReferal->id;
                }
            }


            //создали параметры юзера
            $userParam = UserParam::create($dataParam);

            $token = $user->createToken('token', ['permission:user'])->plainTextToken;

            //создание счетов юзера
            $accountMain = Account::create([
                'user_id' => $user->id,
                'type' => 'main',
                'fiat_coin' => $fiat->id
            ]);


            $accountBonus = Account::create([
                'user_id' => $user->id,
                'type' => 'bonus',
                'fiat_coin' => $fiat->id
            ]);

            $accountMintwin = Account::create([
                'user_id' => $user->id,
                'type' => 'mintwin',
                'fiat_coin' => $fiat->id
            ]);

            $bonusConfig = ConfigWinmove::where('param', 'reg_bonus')->first();

            $balance = [
                ['amount' => 0, 'account_id' => $accountMain->id,'count' => 0 ],
                ['amount' => 0, 'account_id' => $accountBonus->id, 'count' => 0 ],
                ['amount' => 0, 'account_id' => $accountMintwin->id,'count' => 0],
            ];

            if ($bonusConfig->is_active) {

                $accountFsbonus = Account::create([
                    'user_id' => $user->id,
                    'type' => 'fsbonus',
                    'fiat_coin' => $fiat->id
                ]);

                $balance[] = [
                    'amount' => 0,
                    'account_id' => $accountFsbonus->id,
                    'count' => $bonusConfig->val,
                ];

            }

            Balance::insert($balance);

            DB::commit();

            return [
                'status' => true,
                'token' => $token ,
                'fullUserData' => [
                    'user' => $user,
                    'userParam' => $userParam
                ]
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

    public function authSocial($socialDataUser, string $type, array $optionalDate): array
    {
        $user = User::select('users.*', 'user_params.level', 'user_params.currency', 'user_params.referal')
            ->join('user_params', 'users.id', '=', 'user_params.id')
            ->where('users.provider_id', $socialDataUser->getId())
            ->first();

        if ($user) {
            $token = $user->createToken('token', ['permission:user'])->plainTextToken;
            $returnData = [
                'token' => $token,
                'user' => $user->name,
                'level' => $this->getUserLevel($user->id),
                'currency' => $user->currency,
                'balance' => 0
            ];

            return [
                'status' => true,
                'data' => $returnData,
            ];
        }

        if (!$user) {
            $data = [
                'name' => $socialDataUser->getName(),
                'email' => $socialDataUser->getEmail(),
               // 'currency' => session('user_auth.currency') ?? 'USD',
                'currency' => $optionalDate['currency'] ?? 'USD',


                'provider_type' => $type,
                'provider_id' => $socialDataUser->getId(),
            ];

            //session()->forget('user_auth.currency');

           // if (session()->has('user_auth.ref')) {
            //    $data['refCode'] = session('user_auth.ref');
            //    session()->forget('user_auth.ref');
            //}

            if( $optionalDate['refCode'] ){
                $data['refCode'] = $optionalDate['refCode'];
            }

            $userData = $this->createUser($data);

            if ($userData['status']) {
                $returnData = [
                    'token' => $userData['token'],
                    'user' => $userData['fullUserData']['user']['name'],
                    'level' => $this->getUserLevel($userData['fullUserData']['user']['id']),
                    'currency' => $userData['fullUserData']['userParam']['currency'],
                    'balance' => 0
                ];

                return [
                    'status' => true,
                    'data' => $returnData,
                ];
            } else {
                return [
                    'status' => false,
                    'data' => $userData['error'],
                ];
            }
        }
    }


    public function getUserLevel(int $userId): array|null
    {
        // Выполнение хранимой процедуры
        $result = DB::select('CALL getUserLevel(?)', [$userId]);

        if (count($result) > 0) {
            $userLevel = $result[0]->user_level;
            $maxAmount = $result[0]->maxAmount;
            $fullAmount = $result[0]->fullAmount ?? 0;

            return [
                'user_level' => $userLevel,
                'max_amount' => $maxAmount,
                'full_amount' => $fullAmount,
            ];
        }

        return null;
    }
}
