<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\Charge;
use Exception;

class FundsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get paginated balance history
        $balanceHistory = $user->balanceHistories()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Calculate total spent from orders
        $totalSpent = $user->orders()
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        return view('frontend.funds.index', compact('balanceHistory', 'totalSpent'));
    }

    public function add()
    {
        return view('frontend.funds.add');
    }

    public function process(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:bank,bkash,nagad,rocket',
            'sender_number' => 'required|string|max:20',
            'transaction_reference' => 'required|string|max:255',
            'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        try {
            DB::beginTransaction();

            $user = auth()->user();
            $amount = $request->amount;

            // Handle file upload if provided
            $proofPath = null;
            if ($request->hasFile('payment_proof')) {
                $file = $request->file('payment_proof');
                $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $proofPath = $file->storeAs('payment_proofs', $filename, 'public');
            }

            // Create pending transaction record
            DB::table('manual_payments')->insert([
                'user_id' => $user->id,
                'amount' => $amount,
                'payment_method' => $request->payment_method,
                'sender_number' => $request->sender_number,
                'transaction_reference' => $request->transaction_reference,
                'payment_proof' => $proofPath,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return redirect()
                ->route('funds.index')
                ->with('success', $this->getSuccessMessage($request->payment_method));

        } catch (Exception $e) {
            DB::rollBack();
            
            // Delete uploaded file if exists
            if ($request->hasFile('payment_proof')) {
                Storage::delete($request->file('payment_proof')->path());
            }
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Payment submission failed: ' . $e->getMessage());
        }
    }

    protected function getSuccessMessage($paymentMethod)
    {
        $messages = [
            'bkash' => 'Your bKash payment details have been submitted successfully!',
            'nagad' => 'Your Nagad payment details have been submitted successfully!',
            'rocket' => 'Your Rocket payment details have been submitted successfully!',
            'bank' => 'Your bank transfer details have been submitted successfully!'
        ];

        return $messages[$paymentMethod] . ' Please allow up to 24 hours for verification.';
    }
} 