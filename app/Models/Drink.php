<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drink extends Model
{
    use HasFactory;

    protected $fillable = [
        'kategori_id',
        'name',
        'description',
        'price',
        'image',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
