<?php

namespace App\Http\Controllers;

use App\Models\WorkflowTemplate;
use App\Models\WorkflowInstance;
use Illuminate\Http\Request;

class WorkflowController extends Controller
{
    /**
     * Display a listing of workflows.
     */
    public function index()
    {
        $workflows = WorkflowTemplate::with('steps', 'instances')
            ->latest()
            ->paginate(15);

        return view('workflows.index', compact('workflows'));
    }

    /**
     * Show the form for creating a new workflow.
     */
    public function create()
    {
        return view('workflows.create');
    }

    /**
     * Store a newly created workflow in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'trigger_type' => 'required|string|in:event,schedule,manual',
            'trigger_config' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $workflow = WorkflowTemplate::create($validated);

        return redirect()->route('workflows.show', $workflow)
            ->with('success', 'Workflow created successfully.');
    }

    /**
     * Display the specified workflow.
     */
    public function show(WorkflowTemplate $workflow)
    {
        $workflow->load(['steps', 'instances.steps']);
        
        return view('workflows.show', compact('workflow'));
    }

    /**
     * Show the form for editing the specified workflow.
     */
    public function edit(WorkflowTemplate $workflow)
    {
        $workflow->load('steps');
        
        return view('workflows.edit', compact('workflow'));
    }

    /**
     * Update the specified workflow in storage.
     */
    public function update(Request $request, WorkflowTemplate $workflow)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'trigger_type' => 'required|string|in:event,schedule,manual',
            'trigger_config' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $workflow->update($validated);

        return redirect()->route('workflows.show', $workflow)
            ->with('success', 'Workflow updated successfully.');
    }

    /**
     * Remove the specified workflow from storage.
     */
    public function destroy(WorkflowTemplate $workflow)
    {
        $workflow->delete();

        return redirect()->route('workflows.index')
            ->with('success', 'Workflow deleted successfully.');
    }

    /**
     * Execute a workflow manually.
     */
    public function execute(Request $request, WorkflowTemplate $workflow)
    {
        $context = $request->input('context', []);
        
        $instance = $workflow->execute(null, $context);

        return redirect()->route('workflows.show', $workflow)
            ->with('success', 'Workflow executed successfully.');
    }
}