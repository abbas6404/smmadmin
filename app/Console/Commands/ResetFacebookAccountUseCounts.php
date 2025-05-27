<?php

namespace App\Console\Commands;

use App\Models\FacebookAccount;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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
        
        try {
            $count = FacebookAccount::where('use_count', '>', 0)->count();
            
            FacebookAccount::query()
                ->update([
                    'use_count' => 0,
                    'have_use' => false
                ]);
            
            $this->info("Successfully reset use counts for {$count} Facebook accounts.");
            Log::info("Daily reset: Reset use counts for {$count} Facebook accounts.");
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error resetting Facebook account use counts: {$e->getMessage()}");
            Log::error("Error in daily reset of Facebook account use counts: {$e->getMessage()}");
            
            return Command::FAILURE;
        }
    }
}
