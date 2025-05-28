<?php

namespace App\Console\Commands;

use App\Models\FacebookAccount;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ResetFacebookAccountUseCounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facebook:reset-use-counts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the daily use counts for all Facebook accounts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to reset Facebook account use counts...');
        $startTime = microtime(true);
        
        try {
            $count = FacebookAccount::where('use_count', '>', 0)->count();
            
            FacebookAccount::query()
                ->update([
                    'use_count' => 0
                ]);
            
            // Update the last run timestamp in settings
            $now = Carbon::now()->toDateTimeString();
            Setting::updateOrCreate(
                ['key' => 'facebook_reset_last_run'],
                ['value' => $now]
            );
            
            $executionTime = round(microtime(true) - $startTime, 2);
            $this->info("Successfully reset use counts for {$count} Facebook accounts in {$executionTime}s.");
            $this->info("Last run time recorded: {$now}");
            
            Log::info("Daily reset: Reset use counts for {$count} Facebook accounts.", [
                'execution_time' => $executionTime,
                'last_run' => $now
            ]);
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error resetting Facebook account use counts: {$e->getMessage()}");
            Log::error("Error in daily reset of Facebook account use counts: {$e->getMessage()}");
            
            return Command::FAILURE;
        }
    }
}
