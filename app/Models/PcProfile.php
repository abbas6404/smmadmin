<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

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
        'email',
        'password',
        'hardware_id',
        'device_name',
        'hostname',
        'os_version',
        'user_agent',
        'profile_root_directory',
        'status',
        'access_token',
        'last_verified_at',
        'last_used_at',
        'max_profile_limit',
        'max_order_limit',
        'min_order_limit'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'last_verified_at' => 'datetime',
        'last_used_at' => 'datetime'
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
        return $currentProfiles >= $this->max_chrome_profiles;
    }

    /**
     * Check if link limit is reached
     */
    public function isLinkLimitReached(int $currentLinks): bool
    {
        return $currentLinks >= $this->max_gmail_accounts;
    }

    /**
     * Get count of active Chrome profiles
     */
    public function getActiveChromesCount(): int
    {
        return $this->chromeProfiles()
            ->where('status', 'active')
            ->count();
    }

    /**
     * Get count of active Gmail accounts
     */
    public function getActiveGmailsCount(): int
    {
        return $this->gmailAccounts()
            ->where('status', 'active')
            ->count();
    }

    /**
     * Get count of active Facebook accounts
     */
    public function getActiveFacebooksCount(): int
    {
        return $this->facebookAccounts()
            ->where('status', 'active')
            ->count();
    }

    /**
     * Update last used timestamp
     */
    public function updateLastUsed(): bool
    {
        return $this->update(['last_used_at' => now()]);
    }

    /**
     * Scope a query to only include active profiles
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive profiles
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function activate()
    {
        $this->update([
            'status' => 'active',
            'last_used_at' => now(),
        ]);
    }

    public function deactivate()
    {
        $this->update([
            'status' => 'inactive',
            'last_used_at' => now(),
        ]);
    }

    public function hasAvailableChromeProfiles()
    {
        return $this->chromeProfiles()->count() < $this->max_chrome_profiles;
    }

    public function hasAvailableGmailAccounts()
    {
        return $this->gmailAccounts()->count() < $this->max_gmail_accounts;
    }

    public function hasAvailableFacebookAccounts()
    {
        return $this->facebookAccounts()->count() < $this->max_facebook_accounts;
    }
} 