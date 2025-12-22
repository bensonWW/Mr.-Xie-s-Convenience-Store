<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * MemberLevel model - Normalized member level definitions.
 * 
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property int $threshold
 * @property float $discount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 */
class MemberLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'threshold',
        'discount',
    ];

    protected $casts = [
        'threshold' => 'integer',
        'discount' => 'decimal:4',
    ];

    /**
     * Get all users with this member level.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Find a member level by its slug.
     */
    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }

    /**
     * Get the default (normal) member level.
     */
    public static function getDefault(): ?self
    {
        return static::findBySlug('normal');
    }
}
