<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FacebookQuickCheckSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing records
        DB::table('facebook_quick_check')->truncate();
        
        $statuses = ['pending', 'active', 'in_use', 'blocked'];
        
        for ($i = 1; $i <= 30; $i++) {
            DB::table('facebook_quick_check')->insert([
                'email' => 'quickcheck' . $i . '@facebook.com',
                'password' => 'password123',
                'two_factor_secret' => rand(0, 5) > 3 ? '123456' : null,
                'status' => $statuses[array_rand($statuses)],
                'check_result' => rand(0, 1) ? 'Account valid and accessible' : null,
                'last_checked_at' => rand(0, 3) > 0 ? Carbon::now()->subHours(rand(1, 72)) : null,
                'checked_by' => rand(0, 3) > 0 ? 'admin@example.com' : null,
                'check_count' => rand(0, 10),
                'notes' => rand(0, 2) > 0 ? 'Test quick check account #' . $i : null,
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
} 