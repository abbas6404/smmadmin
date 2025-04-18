<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'service_id',
        'description',
        'price',
        'min_price',
        'max_price',
        'min_quantity',
        'max_quantity',
        'type',
        'category',
        'features',
        'requirements',
        'average_time',
        'status',
        'is_featured',
        'position',
        'api_provider'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'min_price' => 'decimal:2',
        'max_price' => 'decimal:2',
        'min_quantity' => 'integer',
        'max_quantity' => 'integer',
        'average_time' => 'integer',
        'is_featured' => 'boolean',
        'position' => 'integer',
        'features' => 'json',
        'requirements' => 'json',
        'api_provider' => 'json'
    ];

    /**
     * Get the orders for the service.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Scope a query to only include active services.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include featured services.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to filter by type.
     */
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get the formatted price.
     */
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }

    /**
     * Get the estimated delivery time.
     */
    public function getEstimatedDeliveryAttribute()
    {
        if (!$this->average_time) {
            return 'N/A';
        }

        $hours = floor($this->average_time / 60);
        $minutes = $this->average_time % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }

        return $minutes . ' minutes';
    }

    /**
     * Get the total number of orders for this service.
     */
    public function getOrdersCountAttribute()
    {
        return $this->orders()->count();
    }

    /**
     * Get the number of completed orders for this service.
     */
    public function getCompletedOrdersCountAttribute()
    {
        return $this->orders()->where('status', 'completed')->count();
    }

    /**
     * Get the number of pending orders for this service.
     */
    public function getPendingOrdersCountAttribute()
    {
        return $this->orders()->where('status', 'pending')->count();
    }
} 