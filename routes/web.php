<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\SystemSettings;
use App\Livewire\AiConfiguration;
use App\Livewire\LeadScoringDashboard;
use App\Http\Controllers\AiDemoController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', Dashboard::class)->name('dashboard');
Route::get('/settings', SystemSettings::class)->name('settings');

// AI Features Routes
Route::get('/ai/configuration', AiConfiguration::class)->name('ai.configuration');
Route::get('/ai/lead-scoring', LeadScoringDashboard::class)->name('ai.lead-scoring');

// AI Demo Routes (for demonstration without authentication)
Route::get('/ai/demo/configuration', [AiDemoController::class, 'aiConfiguration'])->name('ai.demo.configuration');
Route::get('/ai/demo/lead-scoring', [AiDemoController::class, 'leadScoring'])->name('ai.demo.lead-scoring');

// Placeholder routes for navigation (will be implemented with Livewire components)
Route::get('/contacts', function () {
    return view('contacts.index');
})->name('contacts.index');

Route::get('/companies', function () {
    return view('companies.index');
})->name('companies.index');

Route::get('/deals', function () {
    return view('deals.index');
})->name('deals.index');

Route::get('/tasks', function () {
    return view('tasks.index');
})->name('tasks.index');

// Placeholder create routes
Route::get('/contacts/create', function () {
    return view('contacts.create');
})->name('contacts.create');

Route::get('/companies/create', function () {
    return view('companies.create');
})->name('companies.create');

Route::get('/deals/create', function () {
    return view('deals.create');
})->name('deals.create');

Route::get('/tasks/create', function () {
    return view('tasks.create');
})->name('tasks.create');
