<?php

namespace Database\Seeders;

use App\Models\ManualPayment;
use App\Models\User;
use Illuminate\Database\Seeder;

class ManualPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $paymentMethods = ['bank', 'bkash', 'nagad', 'rocket'];
        $statuses = ['pending', 'approved', 'rejected'];

        foreach ($users as $user) {
            // Create 2-4 manual payments for each user
            $numPayments = rand(2, 4);
            
            for ($i = 0; $i < $numPayments; $i++) {
                $amount = rand(10, 1000);
                $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
                $status = $statuses[array_rand($statuses)];
                
                $payment = ManualPayment::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'payment_method' => $paymentMethod,
                    'sender_number' => $paymentMethod !== 'bank' ? '01' . rand(100000000, 999999999) : null,
                    'transaction_reference' => 'TRX-' . uniqid(),
                    'payment_proof' => null,
                    'status' => $status,
                    'admin_note' => $status === 'approved' ? 'Payment verified and approved' : ($status === 'rejected' ? 'Payment verification failed' : null),
                    'processed_at' => $status !== 'pending' ? now()->subDays(rand(1, 30)) : null,
                    'processed_by' => $status !== 'pending' ? 1 : null, // Assuming admin ID 1
                ]);
                
                // If payment is approved, update user balance
                if ($status === 'approved') {
                    $user->balance += $amount;
                    $user->save();
                }
            }
        }
    }
} 