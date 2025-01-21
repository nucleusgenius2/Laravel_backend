<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use App\Models\UserParam;
use App\Services\GenerateUniqueString;
use App\Traits\StructuredResponse;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RegistrationController
{
    use StructuredResponse;

    public function registration(RegistrationRequest $request): JsonResponse
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => $data['password'],
                'created_at' => Carbon::now(),
            ]);

            $dataParam = [
                'id' => $user->id,
                'currency' => $data['currency'],
                'level' => 1,
                'password' => $data['password'],
                'referal' => GenerateUniqueString::generate($user->id, 10),
            ];

            if (isset($data['refCodey'])) {
                $userReferal = UserParam::select('id')->where('referal', $data['refCodey'])->first();

                if ($userReferal) {
                    $dataParam['refer_id'] = $userReferal->id;
                }
            }

            UserParam::create($dataParam);

            $token = $user->createToken('token', ['permission:user'])->plainTextToken;

            DB::commit();

            $this->status = 'success';
            $this->code = 200;
            $this->dataJson = $token;
            $this->message = 'Регистрация прошла успешно';
        }
        catch (\Exception $e) {
            DB::rollBack();

            $this->code = 500;
            $this->message = 'Ошибка при регистрации: ' . $e->getMessage();
        }

        return $this->responseJsonApi();
    }
}
