<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialEvent extends Model
{
    /** @use HasFactory<\Database\Factories\SpecialEventFactory> */
    use HasFactory;

    protected $table = 'special_events';
    protected $primaryKey = 'event_id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'starts_at',
        'ends_at',
        'discount_rate',
    ];

    public function eventProducts()
    {
        return $this->hasMany(EventProduct::class, 'event_id', 'event_id');
    }
}
