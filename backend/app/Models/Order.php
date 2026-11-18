<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_phone',
        'customer_address',
        'items',
        'total_pcs',
        'locale',
    ];

    protected $casts = [
        'items' => 'array',
    ];
}
