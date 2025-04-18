<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'service_id',
        'link',
        'link_uid',
        'quantity',
        'price',
        'total_amount',
        'status',
        'start_count',
        'remains',
        'description',
        'error_message',
        'api_provider_id',
        'api_order_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'start_count' => 'integer',
        'remains' => 'integer'
    ];

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the service that the order belongs to.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Check if the order is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the order is processing
     */
    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    /**
     * Check if the order is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the order is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if the order is failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if the order is partial
     */
    public function isPartial(): bool
    {
        return $this->status === 'partial';
    }

    /**
     * Get the progress percentage
     */
    public function getProgressAttribute(): int
    {
        if ($this->quantity === 0) {
            return 0;
        }
        return (int) (($this->quantity - $this->remains) / $this->quantity * 100);
    }
} 