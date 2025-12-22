<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id'];

    protected $appends = ['total_amount'];

    public function getTotalAmountAttribute()
    {
        return $this->items->reduce(function ($total, $item) {
            return $total + ($item->quantity * ($item->product->price ?? 0));
        }, 0);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}
