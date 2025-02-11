<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'invoice_uid',
        'amount',
        'amount_income',
        'processing',
        'currency_id',
        'currency_income_id',
        'status',
        'date_completion',
        'date_start',
    ];

    public $timestamps = false;

    protected $casts = [
        'amount' => 'string',
        'amount_income' => 'string',
    ];

}
