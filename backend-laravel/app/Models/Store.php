<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    /** @use HasFactory<\Database\Factories\StoreFactory> */
    use HasFactory;

    protected $table = 'stores';
    protected $primaryKey = 'store_id';

    protected $fillable = [
        'name',
        'email',
        'address',
        'phone'
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'store_id', 'store_id');
    }

    public function members()
    {
        return $this->belongsToMany(Member::class, 'store_members', 'store_id', 'member_id');
    }
}
