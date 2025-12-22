<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $phone
 * @property string $role
 * @property string|null $address
 * @property string|null $birthday
 * @property int|null $store_id
 * @property int|null $member_level_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $orders
 * @property-read \App\Models\Cart|null $cart
 * @property-read \App\Models\Store|null $store
 * @property-read \App\Models\MemberLevel|null $memberLevel
 * @property-read string $member_level  Virtual accessor for backward compatibility
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'birthday',
        'store_id',
        'status',
        'balance',
        'member_level',      // Keep for SQLite compatibility during migration
        'member_level_id',   // New normalized FK
        'is_level_locked',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birthday' => 'date',
            'is_level_locked' => 'boolean',
        ];
    }

    /**
     * Get the member level relationship.
     * Named memberLevelModel to avoid conflict with member_level accessor.
     */
    public function memberLevelModel()
    {
        return $this->belongsTo(MemberLevel::class, 'member_level_id');
    }

    /**
     * Alias for memberLevelModel() - cleaner access.
     */
    public function level()
    {
        return $this->memberLevelModel();
    }

    /**
     * Get the member level slug (backward compatibility accessor).
     * This allows existing code using $user->member_level to continue working.
     */
    public function getMemberLevelAttribute($value): string
    {
        // If the old column still exists (SQLite), use it
        if ($value !== null) {
            return $value;
        }

        // Otherwise, get from relationship (use method call to avoid infinite loop)
        return $this->memberLevelModel?->slug ?? 'normal';
    }

    /**
     * Set the member level by slug (backward compatibility mutator).
     * Simplified: always try to set both for compatibility.
     */
    public function setMemberLevelAttribute($value): void
    {
        // Always set the old column for SQLite/backward compatibility
        $this->attributes['member_level'] = $value;

        // Try to set member_level_id if MemberLevel model exists
        // Use simple try-catch instead of expensive Schema::hasTable check
        try {
            $level = MemberLevel::where('slug', $value)->first();
            if ($level) {
                $this->attributes['member_level_id'] = $level->id;
            }
        } catch (\Exception $e) {
            // Silently ignore - FK will be resolved by migration or seeder
        }
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
