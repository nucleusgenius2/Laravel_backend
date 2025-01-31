<?php

namespace Database\Seeders;

use App\Models\Advert;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AdvertSeeder extends Seeder
{
    public function run()
    {
        if(!Advert::exists()) {
            Advert::insert([
                [
                    'status' => true,
                    'img' => '',
                    'link_one' => 'https://demo.winmove.io/',
                    'link_two' => null,
                    'to_date' => Carbon::now()->addDays(3)->format('Y-m-d H:i:s'),
                ],
                [
                    'status' => true,
                    'img' => '',
                    'link_one' => 'https://demo.winmove.io/',
                    'link_two' => 'https://demo.winmove.io/',
                    'to_date' => Carbon::now()->addDays(4)->format('Y-m-d H:i:s'),
                ],
            ]);
        }
    }

}
