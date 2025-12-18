<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Address extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'recipient_name',
        'phone',
        'city',
        'district',
        'zip_code',
        'detail_address',
        'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
