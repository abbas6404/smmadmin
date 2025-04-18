<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function login()
    {
        return view('backend.auth.login');
    }

    public function loginStore(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function dashboard()
    {
        return view('backend.dashboard');
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }

    public function profile()
    {
        $adminData = Auth::guard('admin')->user();
        return view('backend.profile.view', compact('adminData'));
    }

    public function profileUpdate(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:admins,email,' . Auth::guard('admin')->id(),
            'username' => 'required|unique:admins,username,' . Auth::guard('admin')->id(),
        ]);

        $admin = Admin::find(Auth::guard('admin')->id());
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->username = $request->username;

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('upload/admin_images'), $filename);
            $admin->profile_photo = $filename;
        }

        $admin->save();

        return redirect()->back()->with('success', 'Profile Updated Successfully');
    }
}
