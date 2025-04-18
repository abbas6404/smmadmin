<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Facebook Page Likes',
                'description' => 'Get real Facebook page likes from active users',
                'price' => 0.50,
                'category' => 'facebook',
                'status' => 'active',
                'min_quantity' => 100,
                'max_quantity' => 10000,
                'service_type' => 'likes',
                'requirements' => json_encode([
                    'Facebook page URL',
                    'Page must be public'
                ])
            ],
            [
                'name' => 'Instagram Followers',
                'description' => 'Get real Instagram followers from active users',
                'price' => 1.00,
                'category' => 'instagram',
                'status' => 'active',
                'min_quantity' => 100,
                'max_quantity' => 5000,
                'service_type' => 'followers',
                'requirements' => json_encode([
                    'Instagram username',
                    'Account must be public'
                ])
            ],
            [
                'name' => 'Twitter Followers',
                'description' => 'Get real Twitter followers from active users',
                'price' => 0.75,
                'category' => 'twitter',
                'status' => 'active',
                'min_quantity' => 100,
                'max_quantity' => 10000,
                'service_type' => 'followers',
                'requirements' => json_encode([
                    'Twitter username',
                    'Account must be public'
                ])
            ]
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
} 