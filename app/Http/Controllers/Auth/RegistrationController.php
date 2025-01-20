<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use App\Traits\StructuredResponse;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RegistrationController
{
    use StructuredResponse;

    public function registration(Request $request): JsonResponse
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required|string|max:10|regex:/(^[A-Za-z0-9-_]+$)+/',
            'phone' => 'required|string|max:16|regex:/(^[0-9-_]+$)+/',
            'email' => 'required|email|unique:users|max:30',
            'password' => 'required|string|confirmed|min:6|max:30',
            'currency' => 'required|string|min:1|max:5',
            'refCodey' => 'required|integer|min:1',
        ]);

        if ($validated->fails()) {
            $this->message = $validated ->errors();
        } else {
            $data = $validated->valid();

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => $data['password'],
                'created_at' => Carbon::now(),
            ]);

            $token = $user->createToken('token', ['permission:user'])->plainTextToken;

            $this->status = 'success';
            $this->code = 200;
            $this->dataJson = $token;
            $this->message = 'Регистрация прошла успешно';

            //event(new Registered($user));
        }

        return $this->responseJsonApi();
    }
}
