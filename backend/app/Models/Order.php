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
        'pdf_path',
        'whatsapp_sent_at',
    ];

    protected $casts = [
        'items' => 'array',
        'whatsapp_sent_at' => 'datetime',
    ];
}
