<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'product_id';
    public $timestamps = false;

    protected $fillable = [
        'store_id',
        'name',
        'price',
        'information',
        'stock',
        'status',
        'category',
        'image',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id', 'store_id');
    }

    public function cartLists()
    {
        return $this->hasMany(CartList::class, 'product_id', 'product_id');
    }

    public function eventProducts()
    {
        return $this->hasMany(EventProduct::class, 'product_id', 'product_id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'product_id', 'product_id');
    }
}
