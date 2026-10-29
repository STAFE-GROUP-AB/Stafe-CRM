<?php

namespace App\Console\Commands;

use App\Services\CustomerHealthService;
use Illuminate\Console\Command;

class RecalculateHealthScoresCommand extends Command
{
    protected $signature = 'health-scores:recalculate
                            {--contact= : Recalculate for a specific contact ID}
                            {--force : Force recalculation even if not due}
                            {--dry-run : Preview what would be recalculated}';

    protected $description = 'Recalculate customer health scores';

    public function handle(CustomerHealthService $healthService): int
    {
        $this->info('Customer Health Score Recalculation');
        $this->newLine();

        if ($contactId = $this->option('contact')) {
            return $this->recalculateSingleContact($healthService, (int) $contactId);
        }

        return $this->recalculateAllContacts($healthService);
    }

    protected function recalculateSingleContact(CustomerHealthService $healthService, int $contactId): int
    {
        $contact = \App\Models\Contact::find($contactId);

        if (!$contact) {
            $this->error("Contact with ID {$contactId} not found.");

            return self::FAILURE;
        }

        if ($this->option('dry-run')) {
            $this->info("Would recalculate health score for: {$contact->full_name} (ID: {$contact->id})");

            return self::SUCCESS;
        }

        $this->info("Recalculating health score for {$contact->full_name}...");

        try {
            $healthScore = $healthService->calculateForContact($contact, 'manual');

            $this->table(
                ['Property', 'Value'],
                [
                    ['Contact', $contact->full_name],
                    ['Overall Score', $healthScore->overall_score],
                    ['Status', ucfirst($healthScore->health_status)],
                    ['Trend', ucfirst($healthScore->trend ?? 'stable')],
                    ['Risk Factors', count($healthScore->risk_factors ?? [])],
                    ['Suggestions', count($healthScore->improvement_suggestions ?? [])],
                ]
            );

            $this->newLine();
            $this->info('Health score recalculated successfully.');

        } catch (\Exception $e) {
            $this->error("Failed to recalculate: {$e->getMessage()}");

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    protected function recalculateAllContacts(CustomerHealthService $healthService): int
    {
        $totalContacts = \App\Models\Contact::count();

        if ($totalContacts === 0) {
            $this->info('No contacts found to process.');

            return self::SUCCESS;
        }

        $this->info("Found {$totalContacts} contacts to process.");

        if ($this->option('dry-run')) {
            $this->info('Dry run mode - no changes will be made.');
            $this->newLine();

            $needsUpdate = \App\Models\CustomerHealthScore::whereNull('last_calculated_at')
                ->orWhere('last_calculated_at', '<', now()->subDays(1))
                ->count();

            $contactsWithoutScore = \App\Models\Contact::whereDoesntHave('customerHealthScore')->count();

            $this->table(
                ['Category', 'Count'],
                [
                    ['Total Contacts', $totalContacts],
                    ['Contacts Without Score', $contactsWithoutScore],
                    ['Scores Needing Update', $needsUpdate],
                    ['Total to Process', $contactsWithoutScore + $needsUpdate],
                ]
            );

            return self::SUCCESS;
        }

        $this->newLine();

        $bar = $this->output->createProgressBar($totalContacts);
        $bar->start();

        $results = [
            'processed' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        \App\Models\Contact::chunk(50, function ($contacts) use ($healthService, $bar, &$results) {
            foreach ($contacts as $contact) {
                try {
                    $healthService->calculateForContact($contact, 'scheduled');
                    $results['processed']++;
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = [
                        'contact_id' => $contact->id,
                        'error' => $e->getMessage(),
                    ];
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);

        $this->info('Recalculation Summary:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Processed', $results['processed']],
                ['Failed', $results['failed']],
            ]
        );

        if ($results['failed'] > 0 && $this->output->isVerbose()) {
            $this->newLine();
            $this->warn('Errors:');
            foreach (array_slice($results['errors'], 0, 10) as $error) {
                $this->line("  - Contact {$error['contact_id']}: {$error['error']}");
            }
            if (count($results['errors']) > 10) {
                $this->line("  ... and " . (count($results['errors']) - 10) . " more errors");
            }
        }

        $stats = $healthService->getStatistics();
        $this->newLine();
        $this->info('Health Score Distribution:');
        $this->table(
            ['Status', 'Count', 'Percentage'],
            [
                ['Excellent', $stats['excellent'], $this->percentage($stats['excellent'], $stats['total_customers'])],
                ['Good', $stats['good'], $this->percentage($stats['good'], $stats['total_customers'])],
                ['At Risk', $stats['at_risk'], $this->percentage($stats['at_risk'], $stats['total_customers'])],
                ['Critical', $stats['critical'], $this->percentage($stats['critical'], $stats['total_customers'])],
            ]
        );

        $this->newLine();
        $this->info("Average Score: {$stats['average_score']}");

        return $results['failed'] > 0 ? self::FAILURE : self::SUCCESS;
    }

    protected function percentage(int $count, int $total): string
    {
        if ($total === 0) {
            return '0%';
        }

        return round(($count / $total) * 100, 1) . '%';
    }
}
