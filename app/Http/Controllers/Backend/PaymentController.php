<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ManualPayment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = ManualPayment::with('user')->latest()->paginate(20);
        return view('backend.payments.index', compact('payments'));
    }

    public function pending()
    {
        $payments = ManualPayment::with('user')
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);
        return view('backend.payments.index', compact('payments'));
    }

    public function approve(ManualPayment $payment)
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', 'This payment cannot be approved.');
        }

        $payment->status = 'approved';
        $payment->save();

        // Add funds to user's balance
        $payment->user->increment('balance', $payment->amount);

        // Create balance history record
        $payment->user->balanceHistories()->create([
            'amount' => $payment->amount,
            'type' => 'credit',
            'description' => 'Manual payment approved #' . $payment->id,
            'previous_balance' => $payment->user->balance - $payment->amount,
            'new_balance' => $payment->user->balance
        ]);

        return back()->with('success', 'Payment approved successfully');
    }

    public function reject(ManualPayment $payment)
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', 'This payment cannot be rejected.');
        }

        $payment->status = 'rejected';
        $payment->save();

        return back()->with('success', 'Payment rejected successfully');
    }

    public function show(ManualPayment $payment)
    {
        $payment->load('user');
        return view('backend.payments.show', compact('payment'));
    }
} 