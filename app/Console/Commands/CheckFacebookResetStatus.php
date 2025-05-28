<?php

namespace App\Console\Commands;

use App\Models\FacebookAccount;
use App\Models\Setting;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CheckFacebookResetStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facebook:check-reset-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the status of Facebook account use counts and last reset time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Facebook Account Usage Status');
        $this->line('--------------------------------');
        
        // Get the last run time
        $lastRunSetting = Setting::where('key', 'facebook_reset_last_run')->first();
        $lastRunTime = $lastRunSetting ? $lastRunSetting->value : 'Never';
        
        if ($lastRunTime && $lastRunTime !== 'Never') {
            $lastRun = Carbon::parse($lastRunTime);
            $timeSince = $lastRun->diffForHumans();
            $this->info("Last reset: {$lastRunTime} ({$timeSince})");
        } else {
            $this->warn("Last reset: Never");
        }
        
        // Get daily limit setting
        $limitSetting = Setting::where('key', 'facebook_account_daily_use_limit')->first();
        $dailyLimit = $limitSetting ? (int)$limitSetting->value : 0;
        $this->info("Daily usage limit per account: {$dailyLimit}");
        
        // Get Facebook account statistics
        $totalAccounts = FacebookAccount::count();
        $usedAccounts = FacebookAccount::where('use_count', '>', 0)->count();
        $maxedOutAccounts = FacebookAccount::where('use_count', '>=', $dailyLimit)->count();
        $availableAccounts = $totalAccounts - $maxedOutAccounts;
        
        // Calculate average use count
        $avgUseCount = $usedAccounts > 0 
            ? FacebookAccount::where('use_count', '>', 0)->avg('use_count') 
            : 0;
        
        $this->line('--------------------------------');
        $this->info("Total Facebook accounts: {$totalAccounts}");
        $this->info("Accounts with uses: {$usedAccounts} (" . ($totalAccounts > 0 ? round(($usedAccounts / $totalAccounts) * 100) : 0) . "%)");
        $this->info("Accounts at limit: {$maxedOutAccounts} (" . ($totalAccounts > 0 ? round(($maxedOutAccounts / $totalAccounts) * 100) : 0) . "%)");
        $this->info("Available accounts: {$availableAccounts} (" . ($totalAccounts > 0 ? round(($availableAccounts / $totalAccounts) * 100) : 0) . "%)");
        $this->info("Average uses per account: " . number_format($avgUseCount, 1) . " (for accounts with uses)");
        
        // Usage distribution
        $this->line('--------------------------------');
        $this->info("Usage Distribution:");
        
        $usageStats = FacebookAccount::selectRaw('use_count, COUNT(*) as count')
            ->groupBy('use_count')
            ->orderBy('use_count')
            ->get();
            
        $table = [];
        foreach ($usageStats as $stat) {
            $table[] = [
                'Use Count' => $stat->use_count,
                'Accounts' => $stat->count,
                'Percentage' => ($totalAccounts > 0 ? round(($stat->count / $totalAccounts) * 100, 1) : 0) . '%',
                'Status' => $stat->use_count >= $dailyLimit ? 'At Limit' : 'Available'
            ];
        }
        
        $this->table(['Use Count', 'Accounts', 'Percentage', 'Status'], $table);
        
        return Command::SUCCESS;
    }
} 