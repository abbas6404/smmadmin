<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacebookQuickCheck extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'facebook_quick_check';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
        'two_factor_secret',
        'status',
        'check_result',
        'last_checked_at',
        'checked_by',
        'check_count',
        'notes',
        'account_cookies',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'last_checked_at' => 'datetime',
        'account_cookies' => 'json',
    ];

    /**
     * Check if the account is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the account is available
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    /**
     * Check if the account is in use
     */
    public function isInUse(): bool
    {
        return $this->status === 'in_use';
    }

    /**
     * Check if the account is blocked
     */
    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    /**
     * Scope a query to only include pending accounts
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include available accounts
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope a query to only include valid accounts
     */
    public function scopeValid($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Increment the check count
     */
    public function incrementCheckCount()
    {
        $this->increment('check_count');
        $this->last_checked_at = now();
        $this->save();
    }
}
