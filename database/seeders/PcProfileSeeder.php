<?php

namespace Database\Seeders;

use App\Models\PcProfile;
use Illuminate\Database\Seeder;

class PcProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PcProfile::create([
            'pc_name' => 'Main PC',
            'hardware_id' => 'PC-' . uniqid(),
            'max_profile_limit' => 5,
            'max_link_limit' => 50,
            'status' => 'active'
        ]);

        PcProfile::create([
            'pc_name' => 'Backup PC',
            'hardware_id' => 'PC-' . uniqid(),
            'max_profile_limit' => 3,
            'max_link_limit' => 30,
            'status' => 'active'
        ]);
    }
} 