<?php

namespace App\Contracts;

interface EventWebsocket
{
    public function getType():string;

    public function getUserName():string;

    public function getUserId():int;

    public function getData():string;

}
