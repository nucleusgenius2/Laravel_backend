<?php

namespace App\Http\Controllers\State;

use App\Http\Requests\CountRequest;

class PlayGameController
{
    public function indexTable(CountRequest $request)
    {
        $data = $request->validated();

        
    }
}
