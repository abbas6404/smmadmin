<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionBatch extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'submission_batch';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'submission_type',
        'total_submissions',
        'accurate_submissions',
        'incorrect_submissions',
        'approved',
        'notes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_submissions' => 'integer',
        'accurate_submissions' => 'integer',
        'incorrect_submissions' => 'integer',
        'approved' => 'boolean',
        'deleted_at' => 'datetime'
    ];

    /**
     * Get the user that owns the submission batch.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the Facebook accounts for this submission batch.
     */
    public function facebookAccounts(): HasMany
    {
        return $this->hasMany(FacebookAccount::class);
    }

    /**
     * Get the Gmail accounts for this submission batch.
     */
    public function gmailAccounts(): HasMany
    {
        return $this->hasMany(GmailAccount::class);
    }
} 