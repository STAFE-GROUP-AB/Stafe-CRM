<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', Dashboard::class)->name('dashboard');

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
