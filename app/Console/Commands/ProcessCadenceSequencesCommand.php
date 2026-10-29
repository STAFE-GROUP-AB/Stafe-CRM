<?php

namespace App\Console\Commands;

use App\Services\CadenceAutomationService;
use Illuminate\Console\Command;

class ProcessCadenceSequencesCommand extends Command
{
    protected $signature = 'cadence:process {--dry-run : Run without executing actions}';

    protected $description = 'Process active cadence sequence enrollments and execute scheduled steps';

    protected CadenceAutomationService $cadenceService;

    public function __construct(CadenceAutomationService $cadenceService)
    {
        parent::__construct();
        $this->cadenceService = $cadenceService;
    }

    public function handle(): int
    {
        $this->info('Processing cadence sequences...');

        if ($this->option('dry-run')) {
            $this->warn('Running in DRY RUN mode - no actions will be executed');
        }

        $results = $this->cadenceService->processEnrollments();

        $this->info("Processed: {$results['processed']}");
        
        if ($results['failed'] > 0) {
            $this->error("Failed: {$results['failed']}");
            
            foreach ($results['errors'] as $error) {
                $this->error("  - Enrollment {$error['enrollment_id']}: {$error['error']}");
            }
        }

        $this->info('Cadence sequence processing complete.');

        return self::SUCCESS;
    }
}
