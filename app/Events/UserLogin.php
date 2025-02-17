<?php

namespace App\Events;


use App\Contracts\EventWebsocket;
use App\DTO\WebsocketDto;
use App\Models\Countries;
use App\Models\User;
use App\Models\UserParam;
use Illuminate\Foundation\Events\Dispatchable;
use Stevebauman\Location\Facades\Location;
use Stevebauman\Location\Request;
use Jenssegers\Agent\Facades\Agent;

class UserLogin
{
    use Dispatchable;

    public User $user;
    public object $request;

    public function __construct(User $user, object $request)
    {
        $this->user = $user;
        $this->request = $request;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getBrowser(): string
    {
        return Agent::browser();
        //return $this->request->header('User-Agent');
    }

    public function getIp(): string
    {
        return request()->header('X-Forwarded-For') ?? request()->header('CF-Connecting-IP') ?? request()->ip();
    }

    public function getRegion(): string
    {
        $position = Location::get($this->getIp());

        $city = $position->cityName ?? '';
        $countryCode = $position->countryCode ?? '';

        return $countryCode.' '. $city;
    }

}
