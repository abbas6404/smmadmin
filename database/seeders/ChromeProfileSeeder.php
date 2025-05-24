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
        $totalProfiles = 110; // Total number of profiles to create
        
        if ($pcProfiles->count() > 0) {
            // Calculate how many profiles to create per PC profile
            $profilesPerPc = ceil($totalProfiles / $pcProfiles->count());
            
            $count = 0;
            foreach ($pcProfiles as $pcProfile) {
                // Create profiles for each PC, but don't exceed total
                for ($i = 0; $i < $profilesPerPc && $count < $totalProfiles; $i++) {
                    ChromeProfile::create([
                        'pc_profile_id' => $pcProfile->id,
                        'profile_directory' => 'Profile ' . uniqid(),
                        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
                        'status' => 'active'
                    ]);
                    $count++;
                }
            }
        } else {
            // If no PC profiles exist, create dummy PC profile and add all profiles to it
            $dummyPcProfile = PcProfile::create([
                'pc_name' => 'Dummy PC',
                'email' => 'dummy@example.com',
                'password' => bcrypt('password'),
                'hardware_id' => 'dummy-hardware-id',
                'hostname' => 'dummy-hostname',
                'os_version' => 'Windows 10',
                'status' => 'active'
            ]);
            
            for ($i = 0; $i < $totalProfiles; $i++) {
                ChromeProfile::create([
                    'pc_profile_id' => $dummyPcProfile->id,
                    'profile_directory' => 'Profile ' . uniqid(),
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
                    'status' => 'active'
                ]);
            }
        }
    }
} 