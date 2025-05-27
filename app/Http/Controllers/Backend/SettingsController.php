<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function profile()
    {
        $admin = auth('admin')->user();
        return view('backend.settings.profile', compact('admin'));
    }

    public function updateProfile(Request $request)
    {
        $admin = auth('admin')->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins,email,' . $admin->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }

        DB::table('admins')->where('id', $admin->id)->update($validated);

        return back()->with('success', 'Profile updated successfully');
    }

    public function security()
    {
        return view('backend.settings.security');
    }

    public function updateSecurity(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $admin = auth('admin')->user();

        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password' => 'The provided password does not match your current password.']);
        }

        DB::table('admins')->where('id', $admin->id)->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password updated successfully');
    }

    public function systemSettings()
    {
        $settings = \App\Models\Setting::all()->pluck('value', 'key')->toArray();
        return view('backend.settings.system', compact('settings'));
    }

    public function updateSystemSettings(Request $request)
    {
        $request->validate([
            'system_notification_message' => 'nullable|string|max:500',
            'facebook_account_daily_use_limit' => 'nullable|integer|min:1|max:100',
        ]);

        // Debug the incoming request
        \Illuminate\Support\Facades\Log::info('System settings update request:', [
            'has_notification_active' => $request->has('system_notification_active'),
            'notification_message' => $request->system_notification_message,
            'facebook_daily_limit' => $request->facebook_account_daily_use_limit
        ]);

        // Get all settings that need to be updated
        $settingsToUpdate = [
            'system_notification_active' => $request->has('system_notification_active') ? '1' : '0',
        ];
        
        // Add conditional settings
        if ($request->has('system_notification_message')) {
            $settingsToUpdate['system_notification_message'] = $request->system_notification_message;
        }
        
        if ($request->has('facebook_account_daily_use_limit')) {
            $settingsToUpdate['facebook_account_daily_use_limit'] = $request->facebook_account_daily_use_limit;
        }
        
        // Update all settings
        foreach ($settingsToUpdate as $key => $value) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return back()->with('success', 'System settings updated successfully');
    }
} 