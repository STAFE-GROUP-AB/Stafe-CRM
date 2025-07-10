<?php

namespace App\Console\Commands;

use App\Models\DataRetentionPolicy;
use App\Services\Security\DataRetentionService;
use Illuminate\Console\Command;

class ExecuteDataRetention extends Command
{
    protected $signature = 'security:data-retention 
                            {--policy= : Execute specific policy by ID}
                            {--dry-run : Preview what would be executed without making changes}
                            {--force : Execute even if not scheduled}';

    protected $description = 'Execute data retention policies';

    public function handle(DataRetentionService $dataRetentionService): int
    {
        $this->info('🗂️ Data Retention Policy Execution');
        $this->newLine();

        if ($policyId = $this->option('policy')) {
            return $this->executeSinglePolicy($dataRetentionService, $policyId);
        }

        return $this->executeAllPolicies($dataRetentionService);
    }

    protected function executeSinglePolicy(DataRetentionService $service, int $policyId): int
    {
        $policy = DataRetentionPolicy::find($policyId);
        
        if (!$policy) {
            $this->error("Policy with ID {$policyId} not found.");
            return self::FAILURE;
        }

        if ($this->option('dry-run')) {
            $this->info("🔍 Previewing policy: {$policy->name}");
            $preview = $service->previewPolicyExecution($policy);
            $this->displayPolicyPreview($preview);
            return self::SUCCESS;
        }

        if (!$policy->is_active && !$this->option('force')) {
            $this->warn("Policy '{$policy->name}' is not active. Use --force to execute anyway.");
            return self::FAILURE;
        }

        $this->task("Executing policy: {$policy->name}", function () use ($service, $policy) {
            $result = $service->executePolicy($policy);
            
            if (!$result['success']) {
                $this->error("Failed: " . $result['error']);
                return false;
            }

            $this->displayExecutionResults($result['results']);
            return true;
        });

        return self::SUCCESS;
    }

    protected function executeAllPolicies(DataRetentionService $service): int
    {
        $policies = $this->option('force') 
            ? DataRetentionPolicy::active()->get()
            : DataRetentionPolicy::dueForExecution()->get();

        if ($policies->isEmpty()) {
            $this->info('No policies due for execution.');
            return self::SUCCESS;
        }

        $this->info("Found {$policies->count()} policies to execute:");
        
        foreach ($policies as $policy) {
            $this->line("• {$policy->name} (Last executed: " . 
                      ($policy->last_executed_at?->diffForHumans() ?? 'Never') . ")");
        }

        $this->newLine();

        if ($this->option('dry-run')) {
            $this->info('🔍 Dry run mode - previewing all policies:');
            
            foreach ($policies as $policy) {
                $this->info("Policy: {$policy->name}");
                $preview = $service->previewPolicyExecution($policy);
                $this->displayPolicyPreview($preview);
                $this->newLine();
            }
            
            return self::SUCCESS;
        }

        if (!$this->confirm('Do you want to execute these policies?')) {
            $this->info('Execution cancelled.');
            return self::SUCCESS;
        }

        $results = $service->executeAllPolicies();
        
        $this->info('📊 Execution Summary:');
        $successful = 0;
        $failed = 0;

        foreach ($results as $policyId => $result) {
            $policy = $policies->find($policyId);
            
            if ($result['success']) {
                $successful++;
                $this->line("✅ {$policy->name}: " . $result['results']['processed_records'] . ' records processed');
            } else {
                $failed++;
                $this->line("❌ {$policy->name}: " . $result['error']);
            }
        }

        $this->newLine();
        $this->info("Completed: {$successful} successful, {$failed} failed");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    protected function displayPolicyPreview(array $preview): void
    {
        if (isset($preview['error'])) {
            $this->error($preview['error']);
            return;
        }

        $this->table(
            ['Property', 'Value'],
            [
                ['Model Type', $preview['model_type']],
                ['Cutoff Date', $preview['cutoff_date']],
                ['Action', $preview['action']],
                ['Affected Records', $preview['affected_records']],
            ]
        );

        if ($preview['affected_records'] > 0 && !empty($preview['sample_records'])) {
            $this->line('Sample records that would be affected:');
            $this->table(
                ['ID', 'Created At', 'Key Fields'],
                collect($preview['sample_records'])->take(5)->map(function ($record) {
                    return [
                        $record['id'] ?? 'N/A',
                        $record['created_at'] ?? 'N/A',
                        $record['email'] ?? $record['name'] ?? 'N/A',
                    ];
                })->toArray()
            );
        }
    }

    protected function displayExecutionResults(array $results): void
    {
        $this->table(
            ['Property', 'Value'],
            [
                ['Executed At', $results['executed_at']],
                ['Action', $results['action']],
                ['Affected Records', $results['affected_records']],
                ['Processed Records', $results['processed_records']],
                ['Cutoff Date', $results['cutoff_date']],
            ]
        );
    }
}