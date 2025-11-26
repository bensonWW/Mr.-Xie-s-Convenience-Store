<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventProduct extends Model
{
    protected $table = 'event_products';
    protected $primaryKey = 'ep_id';
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'event_id',
    ];

    public function event()
    {
        return $this->belongsTo(SpecialEvent::class, 'event_id', 'event_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}