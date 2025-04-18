<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacebookAccount extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pc_profile_id',
        'chrome_profile_id',
        'submission_batch_id',
        'email',
        'password',
        'total_count',
        'have_use',
        'have_page',
        'have_post',
        'status',
        'order_link_uid',
        'lang',
        'note',
        'account_cookies'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_count' => 'integer',
        'have_use' => 'boolean',
        'have_page' => 'boolean',
        'have_post' => 'boolean',
        'order_link_uid' => 'json',
        'account_cookies' => 'json'
    ];

    /**
     * Get the PC profile that owns this account.
     */
    public function pcProfile(): BelongsTo
    {
        return $this->belongsTo(PcProfile::class);
    }

    /**
     * Get the Chrome profile that owns this account.
     */
    public function chromeProfile(): BelongsTo
    {
        return $this->belongsTo(ChromeProfile::class);
    }

    /**
     * Check if the account is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the account is inactive
     */
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    /**
     * Check if the account is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the account is processing
     */
    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    /**
     * Check if the account is deleted
     */
    public function isDeleted(): bool
    {
        return $this->status === 'delete';
    }

    /**
     * Add an order link UID to the array
     */
    public function addOrderLinkUid(string $uid): void
    {
        $uids = $this->order_link_uid ?? [];
        if (!in_array($uid, $uids)) {
            $uids[] = $uid;
            $this->order_link_uid = $uids;
            $this->save();
        }
    }

    /**
     * Remove an order link UID from the array
     */
    public function removeOrderLinkUid(string $uid): void
    {
        $uids = $this->order_link_uid ?? [];
        $this->order_link_uid = array_values(array_diff($uids, [$uid]));
        $this->save();
    }

    /**
     * Get the submission batch this account belongs to
     */
    public function submissionBatch()
    {
        return $this->belongsTo(SubmissionBatch::class);
    }

    public function gmailAccount()
    {
        return $this->belongsTo(GmailAccount::class);
    }
} 