<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PcProfile extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pc_name',
        'hardware_id',
        'max_profile_limit',
        'max_link_limit',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'max_profile_limit' => 'integer',
        'max_link_limit' => 'integer'
    ];

    /**
     * Get the Chrome profiles for this PC profile.
     */
    public function chromeProfiles(): HasMany
    {
        return $this->hasMany(ChromeProfile::class);
    }

    /**
     * Get the Gmail accounts for this PC profile.
     */
    public function gmailAccounts(): HasMany
    {
        return $this->hasMany(GmailAccount::class);
    }

    /**
     * Get the Facebook accounts for this PC profile.
     */
    public function facebookAccounts(): HasMany
    {
        return $this->hasMany(FacebookAccount::class);
    }

    /**
     * Check if the profile is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the profile is inactive
     */
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    /**
     * Check if the profile is blocked
     */
    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    /**
     * Check if profile limit is reached
     */
    public function isProfileLimitReached(int $currentProfiles): bool
    {
        return $currentProfiles >= $this->max_profile_limit;
    }

    /**
     * Check if link limit is reached
     */
    public function isLinkLimitReached(int $currentLinks): bool
    {
        return $currentLinks >= $this->max_link_limit;
    }

    /**
     * Get count of active Chrome profiles
     */
    public function getActiveProfilesCount(): int
    {
        return $this->chromeProfiles()->where('status', 'active')->count();
    }
} 