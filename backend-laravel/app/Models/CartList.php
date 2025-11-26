<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartList extends Model
{
    /** @use HasFactory<\Database\Factories\CartListFactory> */
    use HasFactory;

    protected $table = 'cart_lists';
    protected $primaryKey = 'clist_id';
    public $timestamps = false;

    protected $fillable = [
        'member_id',
        'product_id',
        'amount',
        'total_price',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
