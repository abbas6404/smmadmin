<?php

namespace Database\Seeders;

use App\Models\ChromeProfile;
use App\Models\PcProfile;
use Illuminate\Database\Seeder;

class ChromeProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pcProfiles = PcProfile::all();

        foreach ($pcProfiles as $pcProfile) {
            ChromeProfile::create([
                'pc_profile_id' => $pcProfile->id,
                'profile_directory' => 'Profile ' . uniqid(),
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
                'status' => 'active'
            ]);

            ChromeProfile::create([
                'pc_profile_id' => $pcProfile->id,
                'profile_directory' => 'Profile ' . uniqid(),
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
                'status' => 'active'
            ]);
        }
    }
} 