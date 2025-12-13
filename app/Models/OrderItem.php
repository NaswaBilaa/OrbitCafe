<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'drink_id',
        'quantity',
        'subtotal',
    ];


    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function drink()
    {
        return $this->belongsTo(Drink::class);
    }

    public function toppings()
    {
        return $this->hasMany(OrderItemTopping::class);
    }
}
