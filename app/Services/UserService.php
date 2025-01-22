<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserParam;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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

            $user = User::create($userData);

            $dataParam = [
                'id' => $user->id,
                'currency' => $data['currency'],
                'level' => 1,
                'referal' => GenerateUniqueString::generate($user->id, 10),
            ];

            if (isset($data['refCodey'])) {
                $userReferal = UserParam::select('id')->where('referal', $data['refCodey'])->first();

                if ($userReferal) {
                    $dataParam['refer_id'] = $userReferal->id;
                }
            }

            $userParam = UserParam::create($dataParam);

            $token = $user->createToken('token', ['permission:user'])->plainTextToken;

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

    public function authSocial($socialDataUser): array
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
                'currency' => session('user_auth.currency'),
                'provider_type' => 'Google',
                'provider_id' => $socialDataUser->getId(),
            ];

            session()->forget('user_auth.currency');

            if (session()->has('user_auth.ref')) {
                $data['refCodey'] = session('user_auth.ref');
                session()->forget('user_auth.ref');
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
                    'status' => true,
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
            $fullAmount = $result[0]->fullAmount;

            return [
                'user_level' => $userLevel,
                'max_amount' => $maxAmount,
                'full_amount' => $fullAmount,
            ];
        }

        return null;
    }
}
