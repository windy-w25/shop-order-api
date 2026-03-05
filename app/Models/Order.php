<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['shop_id','total_price','status'];

    public function shop() { 
        return $this->belongsTo(Shop::class); 
    }

    public function items() { 
        return $this->hasMany(OrderItem::class); 
    }
}
