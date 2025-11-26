<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreMember extends Model
{
    protected $table = 'store_members';
    public $timestamps = false;

    protected $fillable = [
        'store_id',
        'member_id',
    ];
}