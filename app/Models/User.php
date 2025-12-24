<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

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
 * @property int $balance
 * @property bool $is_level_locked
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $orders
 * @property-read \App\Models\Cart|null $cart
 * @property-read \App\Models\Store|null $store
 * @property-read \App\Models\MemberLevel|null $memberLevel
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class User extends Authenticatable implements MustVerifyEmail
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
        'member_level_id',
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
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Get the member level relationship.
     */
    public function memberLevel()
    {
        return $this->belongsTo(MemberLevel::class, 'member_level_id');
    }

    /**
     * Alias for memberLevel() - cleaner access.
     */
    public function level()
    {
        return $this->memberLevel();
    }

    /**
     * Get the member level slug (backward compatibility accessor).
     * Access via $user->member_level_slug
     */
    public function getMemberLevelSlugAttribute(): string
    {
        return $this->memberLevel?->slug ?? 'normal';
    }

    /**
     * Set member level by slug (backward compatibility for tests).
     * Converts slug to member_level_id.
     * Usage: $user->member_level_slug = 'vip';
     */
    public function setMemberLevelSlugAttribute(?string $value): void
    {
        if ($value === null) {
            return;
        }

        // Try to find the level by slug and set member_level_id
        try {
            $level = MemberLevel::where('slug', $value)->first();
            if ($level) {
                $this->attributes['member_level_id'] = $level->id;
            }
        } catch (\Exception $e) {
            // Ignore - MemberLevel table might not exist yet
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
