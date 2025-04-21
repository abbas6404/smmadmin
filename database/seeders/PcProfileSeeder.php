<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PcProfile;
use Illuminate\Support\Facades\Hash;

class PcProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $profiles = [
            [
                'pc_name' => 'Test PC 1',
                'email' => 'test1@example.com',
                'password' => Hash::make('password123'),
                'hardware_id' => 'HW123456',
                'hostname' => 'test-pc-1',
                'os_version' => 'Windows 10 Pro',
                'user_agent' => '115-123',
                'profile_root_directory' => 'C:\\aio_innovation',
                'max_profile_limit' => 10,
                'max_order_limit' => 5,
                'min_order_limit' => 1,
                'status' => 'active',
                'access_token' => 'test_token_1'
            ],
            [
                'pc_name' => 'Test PC 2',
                'email' => 'test2@example.com',
                'password' => Hash::make('password123'),
                'hardware_id' => 'HW789012',
                'hostname' => 'test-pc-2',
                'os_version' => 'Windows 11 Pro',
                'user_agent' => '120-123',
                'profile_root_directory' => 'D:\\chrome_profiles',
                'max_profile_limit' => 15,
                'max_order_limit' => 8,
                'min_order_limit' => 2,
                'status' => 'active',
                'access_token' => 'test_token_2'
            ]
        ];

        foreach ($profiles as $profile) {
            PcProfile::create($profile);
        }
    }
} 