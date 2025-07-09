<?php

namespace App\Http\Controllers;

use App\Services\AiService;
use App\Models\AiProvider;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;

class AiDemoController extends Controller
{
    public function aiConfiguration()
    {
        $aiService = new AiService();
        $providers = $aiService->getAvailableProviders();
        
        // Get the demo user
        $user = User::first();
        $userConfigurations = $user ? $aiService->getUserConfigurations($user->id) : collect();
        
        return view('ai.demo-configuration', compact('providers', 'userConfigurations'));
    }

    public function leadScoring()
    {
        $contacts = Contact::with(['leadScore', 'company'])->take(10)->get();
        
        return view('ai.demo-lead-scoring', compact('contacts'));
    }
}
