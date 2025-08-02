<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    /**
     * Display a listing of tenants.
     */
    public function index()
    {
        $tenants = Tenant::with('users')
            ->latest()
            ->paginate(15);

        return view('tenants.index', compact('tenants'));
    }

    /**
     * Show the form for creating a new tenant.
     */
    public function create()
    {
        return view('tenants.create');
    }

    /**
     * Store a newly created tenant in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tenants',
            'subdomain' => 'nullable|string|max:255|unique:tenants',
            'domain' => 'nullable|string|max:255|unique:tenants',
            'max_users' => 'nullable|integer|min:1',
            'storage_limit' => 'nullable|integer|min:1',
            'features' => 'nullable|array',
            'settings' => 'nullable|array',
            'status' => 'required|string|in:active,inactive,suspended',
        ]);

        $tenant = Tenant::create($validated);

        return redirect()->route('tenants.show', $tenant)
            ->with('success', 'Tenant created successfully.');
    }

    /**
     * Display the specified tenant.
     */
    public function show(Tenant $tenant)
    {
        $tenant->load(['users', 'tenantUsers']);
        
        return view('tenants.show', compact('tenant'));
    }

    /**
     * Show the form for editing the specified tenant.
     */
    public function edit(Tenant $tenant)
    {
        return view('tenants.edit', compact('tenant'));
    }

    /**
     * Update the specified tenant in storage.
     */
    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tenants,slug,' . $tenant->id,
            'subdomain' => 'nullable|string|max:255|unique:tenants,subdomain,' . $tenant->id,
            'domain' => 'nullable|string|max:255|unique:tenants,domain,' . $tenant->id,
            'max_users' => 'nullable|integer|min:1',
            'storage_limit' => 'nullable|integer|min:1',
            'features' => 'nullable|array',
            'settings' => 'nullable|array',
            'status' => 'required|string|in:active,inactive,suspended',
        ]);

        $tenant->update($validated);

        return redirect()->route('tenants.show', $tenant)
            ->with('success', 'Tenant updated successfully.');
    }

    /**
     * Remove the specified tenant from storage.
     */
    public function destroy(Tenant $tenant)
    {
        // Soft delete to preserve data integrity
        $tenant->update(['status' => 'inactive']);

        return redirect()->route('tenants.index')
            ->with('success', 'Tenant deactivated successfully.');
    }

    /**
     * Show tenant users management.
     */
    public function users(Tenant $tenant)
    {
        $tenant->load(['users', 'tenantUsers.user']);
        
        $availableUsers = User::whereNotIn('id', $tenant->users->pluck('id'))
            ->get();

        return view('tenants.users', compact('tenant', 'availableUsers'));
    }

    /**
     * Add a user to the tenant.
     */
    public function addUser(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string|in:owner,admin,member',
            'permissions' => 'nullable|array',
        ]);

        if ($tenant->hasReachedUserLimit()) {
            return back()->withErrors(['error' => 'Tenant has reached its user limit.']);
        }

        $tenant->users()->attach($validated['user_id'], [
            'role' => $validated['role'],
            'permissions' => $validated['permissions'] ?? [],
            'is_active' => true,
            'joined_at' => now(),
        ]);

        return back()->with('success', 'User added to tenant successfully.');
    }

    /**
     * Remove a user from the tenant.
     */
    public function removeUser(Tenant $tenant, User $user)
    {
        $tenant->users()->detach($user->id);

        return back()->with('success', 'User removed from tenant successfully.');
    }

    /**
     * Update user's role in the tenant.
     */
    public function updateUserRole(Request $request, Tenant $tenant, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|string|in:owner,admin,member',
            'permissions' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $tenant->users()->updateExistingPivot($user->id, [
            'role' => $validated['role'],
            'permissions' => $validated['permissions'] ?? [],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return back()->with('success', 'User role updated successfully.');
    }
}