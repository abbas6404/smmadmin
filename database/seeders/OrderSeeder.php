<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $services = Service::all();
        $statuses = ['pending', 'processing', 'completed', 'cancelled', 'failed', 'partial'];

        foreach ($users as $user) {
            // Create 10 orders for each user
            for ($i = 0; $i < 10; $i++) {
                $service = $services->random();
                $quantity = rand($service->min_quantity, min($service->max_quantity ?? 1000, 1000));
                $price = $service->price;
                $totalAmount = $quantity * $price;
                
                // Set a random date within the last 30 days (one month)
                $randomDate = Carbon::now()->subDays(rand(0, 30));
                
                // Set a random status
                $status = $statuses[array_rand($statuses)];
                $startCount = 0;
                
                // Set a more realistic start_count based on status
                if (in_array($status, ['completed', 'partial'])) {
                    $startCount = rand(1, $quantity);
                } else if ($status == 'processing') {
                    $startCount = rand(1, intval($quantity * 0.5));
                }
                
                Order::create([
                    'user_id' => $user->id,
                    'service_id' => $service->id,
                    'link' => 'https://example.com/' . uniqid(),
                    'link_uid' => rand(1000000, 9999999),
                    'quantity' => $quantity,
                    'price' => $price,
                    'total_amount' => $totalAmount,
                    'status' => $status,
                    'start_count' => $startCount,
                    'remains' => $quantity - $startCount,
                    'description' => 'Test order for ' . $service->name,
                    'api_provider_id' => 'API-' . uniqid(),
                    'api_order_id' => 'ORDER-' . uniqid(),
                    'created_at' => $randomDate,
                    'updated_at' => $randomDate->copy()->addHours(rand(1, 24))
                ]);
            }
        }
    }

    /**
     * Get a random order status
     */
    private function getRandomStatus(): string
    {
        $statuses = ['pending', 'processing', 'completed', 'cancelled', 'failed', 'partial'];
        return $statuses[array_rand($statuses)];
    }
} 