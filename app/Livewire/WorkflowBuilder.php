<?php

namespace App\Livewire;

use App\Models\WorkflowTemplate;
use App\Models\WorkflowStep;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class WorkflowBuilder extends Component
{
    public ?WorkflowTemplate $workflow = null;

    public string $workflowName = '';

    public string $workflowDescription = '';

    public string $triggerType = 'event';

    public bool $isActive = false;

    public array $nodes = [];

    public array $connections = [];

    public ?string $selectedNodeId = null;

    public array $nodeConfig = [];

    protected array $rules = [
        'workflowName' => ['required', 'string', 'max:255'],
        'workflowDescription' => ['nullable', 'string'],
        'triggerType' => ['required', 'string', 'in:event,schedule,manual'],
        'isActive' => ['boolean'],
        'nodes' => ['array'],
        'connections' => ['array'],
    ];

    public function mount(?WorkflowTemplate $workflow = null): void
    {
        if ($workflow && $workflow->exists) {
            $this->workflow = $workflow;
            $this->workflowName = $workflow->name;
            $this->workflowDescription = $workflow->description ?? '';
            $this->triggerType = $workflow->trigger_type;
            $this->isActive = $workflow->is_active;

            $this->loadWorkflowData();
        }
    }

    protected function loadWorkflowData(): void
    {
        if (!$this->workflow) {
            return;
        }

        $visualConfig = $this->workflow->visual_config ?? [];

        if (!empty($visualConfig['nodes'])) {
            $this->nodes = $visualConfig['nodes'];
            $this->connections = $visualConfig['connections'] ?? [];
        } else {
            $this->convertStepsToNodes();
        }
    }

    protected function convertStepsToNodes(): void
    {
        $steps = $this->workflow->steps()->orderBy('order')->get();

        $yOffset = 100;
        foreach ($steps as $index => $step) {
            $nodeId = 'node-' . $step->id;

            $this->nodes[] = [
                'id' => $nodeId,
                'type' => $step->type,
                'label' => $step->name,
                'x' => 100,
                'y' => $yOffset,
                'config' => $step->config ?? [],
            ];

            if ($index > 0) {
                $previousNodeId = 'node-' . $steps[$index - 1]->id;
                $this->connections[] = [
                    'id' => "conn-{$previousNodeId}-{$nodeId}",
                    'from' => $previousNodeId,
                    'to' => $nodeId,
                ];
            }

            $yOffset += 150;
        }
    }

    public function addNode(array $nodeData): void
    {
        $this->nodes[] = $nodeData;
    }

    public function updateNode(string $nodeId, array $updates): void
    {
        foreach ($this->nodes as $index => $node) {
            if ($node['id'] === $nodeId) {
                $this->nodes[$index] = array_merge($node, $updates);
                break;
            }
        }
    }

    public function deleteNode(string $nodeId): void
    {
        $this->nodes = array_values(array_filter($this->nodes, fn($node) => $node['id'] !== $nodeId));

        $this->connections = array_values(array_filter(
            $this->connections,
            fn($conn) => $conn['from'] !== $nodeId && $conn['to'] !== $nodeId
        ));
    }

    public function addConnection(array $connectionData): void
    {
        $existingConnection = collect($this->connections)->first(function ($conn) use ($connectionData) {
            return $conn['from'] === $connectionData['from'] && $conn['to'] === $connectionData['to'];
        });

        if (!$existingConnection) {
            $this->connections[] = $connectionData;
        }
    }

    public function deleteConnection(string $connectionId): void
    {
        $this->connections = array_values(array_filter(
            $this->connections,
            fn($conn) => $conn['id'] !== $connectionId
        ));
    }

    public function selectNode(string $nodeId): void
    {
        $this->selectedNodeId = $nodeId;

        $selectedNode = collect($this->nodes)->firstWhere('id', $nodeId);
        if ($selectedNode) {
            $this->nodeConfig = $selectedNode['config'] ?? [];
        }
    }

    public function updateNodeConfig(array $config): void
    {
        if (!$this->selectedNodeId) {
            return;
        }

        foreach ($this->nodes as $index => $node) {
            if ($node['id'] === $this->selectedNodeId) {
                $this->nodes[$index]['config'] = $config;
                break;
            }
        }

        $this->nodeConfig = $config;
    }

    public function save(): void
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $workflowData = [
                'name' => $this->workflowName,
                'description' => $this->workflowDescription,
                'trigger_type' => $this->triggerType,
                'is_active' => $this->isActive,
                'visual_config' => [
                    'nodes' => $this->nodes,
                    'connections' => $this->connections,
                ],
            ];

            if ($this->workflow && $this->workflow->exists) {
                $this->workflow->update($workflowData);
            } else {
                $this->workflow = WorkflowTemplate::create($workflowData);
            }

            $this->syncWorkflowSteps();

            DB::commit();

            session()->flash('success', 'Workflow saved successfully.');

            $this->redirect(route('workflows.show', $this->workflow), navigate: true);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error saving workflow: ' . $e->getMessage());
        }
    }

    protected function syncWorkflowSteps(): void
    {
        if (!$this->workflow) {
            return;
        }

        $this->workflow->steps()->delete();

        $order = 0;
        $processedNodes = [];

        $startNode = collect($this->nodes)->firstWhere('type', 'trigger');

        if ($startNode) {
            $this->createStepFromNode($startNode, $order++);
            $processedNodes[] = $startNode['id'];

            $currentNodeId = $startNode['id'];

            while ($currentNodeId) {
                $nextConnection = collect($this->connections)
                    ->firstWhere('from', $currentNodeId);

                if (!$nextConnection) {
                    break;
                }

                $nextNode = collect($this->nodes)
                    ->firstWhere('id', $nextConnection['to']);

                if (!$nextNode || in_array($nextNode['id'], $processedNodes)) {
                    break;
                }

                $this->createStepFromNode($nextNode, $order++);
                $processedNodes[] = $nextNode['id'];
                $currentNodeId = $nextNode['id'];
            }
        }

        foreach ($this->nodes as $node) {
            if (!in_array($node['id'], $processedNodes) && $node['type'] !== 'trigger') {
                $this->createStepFromNode($node, $order++);
            }
        }
    }

    protected function createStepFromNode(array $node, int $order): void
    {
        WorkflowStep::create([
            'workflow_template_id' => $this->workflow->id,
            'name' => $node['label'] ?? $node['type'],
            'type' => $node['type'],
            'config' => $node['config'] ?? [],
            'order' => $order,
            'is_active' => true,
        ]);
    }

    public function render()
    {
        return view('livewire.workflow-builder')
            ->layout('layouts.app');
    }
}
