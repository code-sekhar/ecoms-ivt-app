<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 'description','sub_description','image','stock', 'price','user_id', 'brand_id', 'category_id'
    ];
    protected $casts = [
        'image' => 'array', // JSON থেকে Array-এ রূপান্তর
    ];

    public function orders()
    {
        return $this->belongsToMany(orders::class, 'order_product')
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }
}
