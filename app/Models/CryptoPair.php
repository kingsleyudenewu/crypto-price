<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CryptoPair extends Model
{
    use HasFactory;

    protected $fillable = ['pair', 'average_price', 'price_change', 'last_updated'];

    protected $casts = [
        'average_price' => 'float',
        'price_change' => 'float',
        'last_updated' => 'datetime',
    ];
}
