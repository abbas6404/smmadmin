<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::where('status', 'active')
            ->orderBy('name')
            ->get();

        // Get system notification settings
        $setting = Setting::where('key', 'system_notification_active')->first();
        $systemNotificationActive = $setting && ($setting->value === '1' || $setting->value === true);
        
        $messageSetting = Setting::where('key', 'system_notification_message')->first();
        $systemNotificationMessage = $messageSetting ? $messageSetting->value : '';

        return view('frontend.services.index', compact('services', 'systemNotificationActive', 'systemNotificationMessage'));
    }
} 