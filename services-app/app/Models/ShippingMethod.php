<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    protected $fillable = [

        'name',
        'method_code',
        'shipping_price',
        'status'
    ];
}
