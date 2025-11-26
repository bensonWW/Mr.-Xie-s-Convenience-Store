<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderList extends Model
{
    /** @use HasFactory<\Database\Factories\OrderListFactory> */
    use HasFactory;

    protected $table = 'order_lists';
    protected $primaryKey = 'olist_id';
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'product_id',
        'amount',
        'total_price',
        'pname',
        'pimage',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
