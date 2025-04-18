<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
    }
} 