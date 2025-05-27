<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withTrashed()->orderBy('id', 'desc');
        
        // Handle search
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('id', 'like', "%{$searchTerm}%");
            });
        }
        
        // Handle status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        $users = $query->paginate(500)->withQueryString();
        
        return view('backend.users.index', compact('users'));
    }

    public function create()
    {
        return view('backend.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'balance' => 'required|numeric|min:0',
            'custom_rate' => 'nullable|numeric|min:0',
            'daily_order_limit' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive'
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully');
    }

    public function show(User $user)
    {
        return view('backend.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('backend.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'custom_rate' => 'nullable|numeric|min:0',
            'daily_order_limit' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive'
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success', 'User deleted successfully');
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        return back()->with('success', 'User restored successfully');
    }
    
    public function showAddFunds(User $user)
    {
        return view('backend.users.add_funds', compact('user'));
    }
    
    public function addFunds(Request $request, User $user)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
        ]);
        
        $amount = $validated['amount'];
        $description = $validated['description'];
        
        // Get previous balance before update
        $previousBalance = $user->balance;
        
        // Add funds to user's balance
        $user->increment('balance', $amount);
        
        // Create balance history record
        $user->balanceHistories()->create([
            'amount' => $amount,
            'type' => 'credit',
            'description' => $description . ' (Added by admin: ' . Auth::guard('admin')->user()->name . ')',
            'previous_balance' => $previousBalance,
            'new_balance' => $user->balance
        ]);
        
        return redirect()->route('admin.users.show', $user)
            ->with('success', "Successfully added $" . number_format($amount, 2) . " to {$user->name}'s balance");
    }
} 