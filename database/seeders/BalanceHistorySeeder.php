<?php

namespace Database\Seeders;

use App\Models\BalanceHistory;
use App\Models\ManualPayment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class BalanceHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        foreach ($users as $user) {
            // Get user's manual payments
            $payments = ManualPayment::where('user_id', $user->id)
                ->where('status', 'approved')
                ->get();
                
            // Get user's orders
            $orders = Order::where('user_id', $user->id)->get();
            
            // Create balance history for payments
            foreach ($payments as $payment) {
                $previousBalance = $user->balance - $payment->amount;
                
                BalanceHistory::create([
                    'user_id' => $user->id,
                    'amount' => $payment->amount,
                    'previous_balance' => $previousBalance,
                    'new_balance' => $user->balance,
                    'type' => 'credit',
                    'description' => 'Manual payment via ' . $payment->payment_method,
                    'reference' => $payment->transaction_reference
                ]);
            }
            
            // Create balance history for orders
            foreach ($orders as $order) {
                $previousBalance = $user->balance + $order->total_amount;
                
                BalanceHistory::create([
                    'user_id' => $user->id,
                    'amount' => $order->total_amount,
                    'previous_balance' => $previousBalance,
                    'new_balance' => $user->balance,
                    'type' => 'debit',
                    'description' => 'Order payment for ' . $order->description,
                    'reference' => 'ORDER-' . $order->id
                ]);
            }
        }
    }
} 