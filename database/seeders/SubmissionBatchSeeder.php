<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubmissionBatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = ['facebook', 'gmail', 'twitter', 'instagram'];
        $users = DB::table('users')->pluck('id')->toArray();
        
        for ($i = 1; $i <= 10; $i++) {
            DB::table('submission_batch')->insert([
                'user_id' => $users[array_rand($users)],
                'name' => 'Batch #' . $i,
                'submission_type' => $types[array_rand($types)],
                'total_submissions' => rand(50, 200),
                'accurate_submissions' => rand(30, 150),
                'incorrect_submissions' => rand(0, 20),
                'approved' => rand(0, 1),
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
