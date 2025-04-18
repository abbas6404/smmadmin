<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChromeProfile extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pc_profile_id',
        'profile_directory',
        'user_agent',
        'status'
    ];

    /**
     * Get the PC profile that owns this Chrome profile.
     */
    public function pcProfile(): BelongsTo
    {
        return $this->belongsTo(PcProfile::class);
    }

    /**
     * Get the Gmail accounts for this Chrome profile.
     */
    public function gmailAccounts(): HasMany
    {
        return $this->hasMany(GmailAccount::class);
    }

    /**
     * Get the Facebook accounts for this Chrome profile.
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
     * Check if the profile is deleted
     */
    public function isDeleted(): bool
    {
        return $this->status === 'deleted';
    }
} 