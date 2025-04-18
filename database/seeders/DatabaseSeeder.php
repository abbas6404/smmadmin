<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed settings
        $this->call([
            SettingsSeeder::class,
            AdminSeeder::class,
            UserSeeder::class,
            PcProfileSeeder::class,
            ChromeProfileSeeder::class,
            SubmissionBatchSeeder::class,
            GmailAccountSeeder::class,
            FacebookAccountSeeder::class,
            ServiceSeeder::class,
            OrderSeeder::class,
            ManualPaymentSeeder::class,
            BalanceHistorySeeder::class,
        ]);
    }
}
