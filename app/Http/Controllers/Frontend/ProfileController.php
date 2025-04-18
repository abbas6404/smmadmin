<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        return view('frontend.profile.index');
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'timezone' => ['required', 'string', 'timezone'],
            'country' => ['required', 'string', 'max:2'],
        ]);

        $user->update($validated);

        return redirect()->route('profile.index')
            ->with('success', 'Profile updated successfully.');
    }

    public function security(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'two_factor' => ['boolean'],
            'login_notifications' => ['boolean'],
        ]);

        $user->update([
            'two_factor_enabled' => $validated['two_factor'] ?? false,
            'login_notifications_enabled' => $validated['login_notifications'] ?? false,
        ]);

        return redirect()->route('profile.index')
            ->with('success', 'Security settings updated successfully.');
    }

    public function password(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = auth()->user();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.index')
            ->with('success', 'Password changed successfully.');
    }
} 