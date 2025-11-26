<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    /** @use HasFactory<\Database\Factories\MemberFactory> */
    use HasFactory;

    protected $table = 'members';
    protected $primaryKey = 'member_id';
    
    protected $fillable = [
        'name',
        'email',
        'address',
        'password',
        'role',
        'registered_at',
        'birthday',
        'phone',
    ];

    public function cartLists()
    {
        return $this->hasMany(CartList::class, 'member_id', 'member_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'member_id', 'member_id');
    }

    public function usedCoupons()
    {
        return $this->hasMany(UsedCoupon::class, 'member_id', 'member_id');
    }

    public function stores()
    {
        return $this->belongsToMany(Store::class, 'store_members', 'member_id', 'store_id');
    }
}
