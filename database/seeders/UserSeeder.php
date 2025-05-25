<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create two specific test users
        User::create([
            'name' => 'Test User',
            'email' => 'user@user.com',
            'balance' => 1000.00,
            'password' => Hash::make('12345678'),
            'status' => 'active'
        ]);

        User::create([
            'name' => 'Another User',
            'email' => 'another@another.com',
            'balance' => 500.00,
            'password' => Hash::make('12345678'),
            'status' => 'active'
        ]);
        
        // Create 98 additional random users
        $faker = Faker::create();
        
        for ($i = 0; $i < 98; $i++) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'balance' => $faker->randomFloat(2, 0, 5000),
                'password' => Hash::make('password'),
                'status' => $faker->randomElement(['active', 'inactive']),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'created_at' => $faker->dateTimeBetween('-6 months', 'now')
            ]);
        }
    }
} 