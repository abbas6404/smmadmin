<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('frontend.services.index', compact('services'));
    }
} 