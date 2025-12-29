<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $attribute_id
 * @property string $value
 * @property string|null $color_code
 * @property int $display_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ProductAttribute $attribute
 */
class ProductAttributeValue extends Model
{
    protected $fillable = [
        'attribute_id',
        'value',
        'color_code',
        'display_order',
    ];

    protected $casts = [
        'display_order' => 'integer',
    ];

    /**
     * Relationship to attribute.
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class, 'attribute_id');
    }

    /**
     * Get the parent product through attribute.
     */
    public function getProductAttribute(): ?Product
    {
        return $this->attribute?->product;
    }
}
