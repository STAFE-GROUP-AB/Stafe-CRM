<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::active()->orderBy('price')->get();
        
        return view('landing.index', compact('plans'));
    }

    public function pricing()
    {
        $plans = SubscriptionPlan::active()->orderBy('price')->get();
        
        return view('landing.pricing', compact('plans'));
    }

    public function features()
    {
        return view('landing.features');
    }

    public function contact()
    {
        return view('landing.contact');
    }
}
