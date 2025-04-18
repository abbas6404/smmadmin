<?php

namespace App\Traits;

use App\Models\BalanceHistory;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasBalanceHistory
{
    public function balanceHistories(): HasMany
    {
        return $this->hasMany(BalanceHistory::class);
    }

    public function recordBalanceChange(
        float $amount,
        string $type,
        string $description,
        ?string $reference = null
    ): BalanceHistory {
        $previousBalance = $this->balance;
        $newBalance = $previousBalance + $amount;

        return $this->balanceHistories()->create([
            'amount' => $amount,
            'previous_balance' => $previousBalance,
            'new_balance' => $newBalance,
            'type' => $type,
            'description' => $description,
            'reference' => $reference
        ]);
    }
} 