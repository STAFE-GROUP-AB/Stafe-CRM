<?php

namespace App\Http\Controllers;

use App\Models\Integration;
use App\Models\IntegrationCategory;
use App\Models\ApiConnection;
use App\Services\IntegrationService;
use Illuminate\Http\Request;

class IntegrationController extends Controller
{
    protected $integrationService;

    public function __construct(IntegrationService $integrationService)
    {
        $this->integrationService = $integrationService;
    }

    /**
     * Display the integration marketplace.
     */
    public function index()
    {
        $categories = IntegrationCategory::with(['integrations' => function ($query) {
            $query->where('is_active', true);
        }])->get();

        $integrations = Integration::with('category')
            ->where('is_active', true)
            ->latest()
            ->paginate(12);

        return view('integrations.index', compact('categories', 'integrations'));
    }

    /**
     * Show details of a specific integration.
     */
    public function show(Integration $integration)
    {
        $integration->load('category');
        
        $userConnections = auth()->user()
            ->apiConnections()
            ->where('integration_id', $integration->id)
            ->get();

        return view('integrations.show', compact('integration', 'userConnections'));
    }

    /**
     * Install an integration for the current user.
     */
    public function install(Request $request, Integration $integration)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'credentials' => 'required|array',
            'config' => 'nullable|array',
        ]);

        try {
            $connection = $this->integrationService->installIntegration(
                $integration,
                auth()->user(),
                $validated
            );

            return redirect()->route('integrations.connections.show', $connection)
                ->with('success', 'Integration installed successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Installation failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Show user's API connections.
     */
    public function connections()
    {
        $connections = auth()->user()
            ->apiConnections()
            ->with('integration.category')
            ->latest()
            ->paginate(15);

        return view('integrations.connections.index', compact('connections'));
    }

    /**
     * Show details of a specific API connection.
     */
    public function showConnection(ApiConnection $connection)
    {
        $this->authorize('view', $connection);
        
        $connection->load('integration.category');
        
        return view('integrations.connections.show', compact('connection'));
    }

    /**
     * Update an API connection.
     */
    public function updateConnection(Request $request, ApiConnection $connection)
    {
        $this->authorize('update', $connection);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'credentials' => 'nullable|array',
            'config' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $connection->update($validated);

        return redirect()->route('integrations.connections.show', $connection)
            ->with('success', 'Connection updated successfully.');
    }

    /**
     * Test an API connection.
     */
    public function testConnection(ApiConnection $connection)
    {
        $this->authorize('update', $connection);

        $result = $connection->testConnection();

        if ($result['success']) {
            return back()->with('success', 'Connection test successful.');
        } else {
            return back()->withErrors(['error' => 'Connection test failed: ' . $result['message']]);
        }
    }

    /**
     * Sync an API connection.
     */
    public function syncConnection(ApiConnection $connection)
    {
        $this->authorize('update', $connection);

        $result = $this->integrationService->syncConnection($connection);

        if ($result['success']) {
            return back()->with('success', 'Synchronization completed successfully.');
        } else {
            return back()->withErrors(['error' => 'Synchronization failed: ' . $result['message']]);
        }
    }

    /**
     * Delete an API connection.
     */
    public function destroyConnection(ApiConnection $connection)
    {
        $this->authorize('delete', $connection);

        $this->integrationService->uninstallIntegration($connection);

        return redirect()->route('integrations.connections.index')
            ->with('success', 'Integration uninstalled successfully.');
    }
}