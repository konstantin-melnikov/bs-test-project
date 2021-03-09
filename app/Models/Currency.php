<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'currencyID',
        'numCode',
        'сharCode',
        'par',
        'name',
        'value',
        'date'
    ];

    protected $casts = [
        'date' => 'datetime:Y-m-d',
    ];
}
