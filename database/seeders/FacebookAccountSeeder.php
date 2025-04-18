<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FacebookAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pcProfiles = DB::table('pc_profiles')->pluck('id')->toArray();
        $chromeProfiles = DB::table('chrome_profiles')->pluck('id')->toArray();
        $submissionBatches = DB::table('submission_batch')
            ->where('submission_type', 'facebook')
            ->pluck('id')
            ->toArray();
        
        $statuses = ['pending', 'processing', 'active', 'inactive', 'remove'];
        
        for ($i = 1; $i <= 50; $i++) {
            DB::table('facebook_accounts')->insert([
                'pc_profile_id' => $pcProfiles[array_rand($pcProfiles)],
                'chrome_profile_id' => $chromeProfiles[array_rand($chromeProfiles)],
                'submission_batch_id' => !empty($submissionBatches) ? $submissionBatches[array_rand($submissionBatches)] : null,
                'email' => 'facebook' . $i . '@facebook.com',
                'password' => bcrypt('password123'),
                'total_count' => rand(0, 100),
                'have_use' => rand(0, 1),
                'have_page' => rand(0, 1),
                'have_post' => rand(0, 1),
                'status' => $statuses[array_rand($statuses)],
                'lang' => 'en',
                'note' => 'Test account #' . $i,
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
} 