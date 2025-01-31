<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advert extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'status',
        'img',
        'link_one',
        'link_two',
        'to_date'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

}
