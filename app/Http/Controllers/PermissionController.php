<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions.
     */
    public function index()
    {
        $permissions = Permission::orderBy('category')
            ->orderBy('name')
            ->paginate(50);

        $categories = Permission::distinct('category')
            ->pluck('category')
            ->sort()
            ->values();

        return view('permissions.index', compact('permissions', 'categories'));
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create()
    {
        $categories = Permission::distinct('category')
            ->pluck('category')
            ->sort()
            ->values();

        return view('permissions.create', compact('categories'));
    }

    /**
     * Store a newly created permission in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:permissions',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
        ]);

        $permission = Permission::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'is_system' => false,
        ]);

        return redirect()->route('permissions.show', $permission)
            ->with('success', 'Permission created successfully.');
    }

    /**
     * Display the specified permission.
     */
    public function show(Permission $permission)
    {
        $permission->load('roles');
        
        return view('permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function edit(Permission $permission)
    {
        if ($permission->is_system) {
            return back()->withErrors(['error' => 'System permissions cannot be edited.']);
        }

        $categories = Permission::distinct('category')
            ->pluck('category')
            ->sort()
            ->values();

        return view('permissions.edit', compact('permission', 'categories'));
    }

    /**
     * Update the specified permission in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        if ($permission->is_system) {
            return back()->withErrors(['error' => 'System permissions cannot be updated.']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:permissions,slug,' . $permission->id,
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
        ]);

        $permission->update($validated);

        return redirect()->route('permissions.show', $permission)
            ->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified permission from storage.
     */
    public function destroy(Permission $permission)
    {
        if ($permission->is_system) {
            return back()->withErrors(['error' => 'System permissions cannot be deleted.']);
        }

        if ($permission->roles()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete permission that is assigned to roles.']);
        }

        $permission->delete();

        return redirect()->route('permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }
}