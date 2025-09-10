<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasUuids,
        HasApiTokens,
        HasFactory,
        Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => UserRole::class,
            'last_activity_at' => 'date',
            'is_banned' => 'boolean',
        ];
    }

    /**
     * Relations
     */

    public function passkeys(): HasMany
    {
        return $this->hasMany(Passkey::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    public function reward(): HasOne
    {
        return $this->hasOne(Reward::class)->withDefault([
            'total_points' => 0
        ]);
    }

    public function rewardPoints(): HasMany
    {
        return $this->hasMany(RewardPoint::class);
    }

    public function orderMakers(): HasMany
    {
       return $this->hasMany(OrderMaker::class);
    }

    /**
     * Helpers
     */

    public function getIsAdminAttribute(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function getIsTerminalAttribute(): bool
    {
        return $this->role === UserRole::TERMINAL;
    }

    public function getIsEmployeeAttribute(): bool
    {
        return $this->role === UserRole::EMPLOYEE;
    }
}
