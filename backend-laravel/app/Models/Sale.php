<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    /** @use HasFactory<\Database\Factories\SaleFactory> */
    use HasFactory;

    protected $table = 'sales';
    protected $primaryKey = 'sales_id';
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'sales_at',
        'amount',
        'pname',
        'pimage',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
