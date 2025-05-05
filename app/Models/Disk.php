<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Disk extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pc_profile_id',
        'drive_letter',
        'file_system',
        'total_size',
        'free_space',
        'used_space',
        'health_percentage',
        'read_speed',
        'write_speed',
        'last_checked_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_size' => 'integer',
        'free_space' => 'integer',
        'used_space' => 'integer',
        'health_percentage' => 'decimal:2',
        'read_speed' => 'integer',
        'write_speed' => 'integer',
        'last_checked_at' => 'datetime'
    ];

    /**
     * Get the PC profile that owns this disk.
     */
    public function pcProfile(): BelongsTo
    {
        return $this->belongsTo(PcProfile::class);
    }

    /**
     * Get the usage percentage of the disk.
     */
    public function getUsagePercentageAttribute(): float
    {
        if ($this->total_size === 0) {
            return 0;
        }
        return round(($this->used_space / $this->total_size) * 100, 2);
    }

    /**
     * Get the formatted total size.
     */
    public function getFormattedTotalSizeAttribute(): string
    {
        return $this->formatBytes($this->total_size);
    }

    /**
     * Get the formatted free space.
     */
    public function getFormattedFreeSpaceAttribute(): string
    {
        return $this->formatBytes($this->free_space);
    }

    /**
     * Get the formatted used space.
     */
    public function getFormattedUsedSpaceAttribute(): string
    {
        return $this->formatBytes($this->used_space);
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
