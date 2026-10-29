<?php

namespace App\Jobs;

use App\Models\Contact;
use App\Services\LeadScoringService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RecalculateLeadScoresJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 3600; // 1 hour timeout

    public int $tries = 3;

    private bool $forceRecalculate;

    private int $staleHoursThreshold;

    private ?int $contactId;

    /**
     * Create a new job instance.
     *
     * @param  bool  $forceRecalculate  Force recalculation even for fresh scores
     * @param  int  $staleHoursThreshold  Hours after which a score is considered stale
     * @param  int|null  $contactId  Specific contact ID to recalculate (null for all)
     */
    public function __construct(
        bool $forceRecalculate = false,
        int $staleHoursThreshold = 24,
        ?int $contactId = null
    ) {
        $this->forceRecalculate = $forceRecalculate;
        $this->staleHoursThreshold = $staleHoursThreshold;
        $this->contactId = $contactId;
    }

    /**
     * Execute the job.
     */
    public function handle(LeadScoringService $scoringService): void
    {
        $startTime = now();

        Log::info('Lead score recalculation job started', [
            'force_recalculate' => $this->forceRecalculate,
            'stale_threshold_hours' => $this->staleHoursThreshold,
            'specific_contact' => $this->contactId,
        ]);

        $stats = [
            'processed' => 0,
            'skipped' => 0,
            'errors' => 0,
            'score_changes' => [],
        ];

        try {
            $query = Contact::with(['company', 'leadScore', 'emails', 'communications', 'deals']);

            // Filter to specific contact if provided
            if ($this->contactId) {
                $query->where('id', $this->contactId);
            }

            // Process in chunks for memory efficiency
            $query->chunk(100, function ($contacts) use ($scoringService, &$stats) {
                foreach ($contacts as $contact) {
                    $this->processContact($contact, $scoringService, $stats);
                }
            });

        } catch (\Exception $e) {
            Log::error('Lead score recalculation job failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }

        $duration = $startTime->diffInSeconds(now());

        Log::info('Lead score recalculation job completed', [
            'duration_seconds' => $duration,
            'processed' => $stats['processed'],
            'skipped' => $stats['skipped'],
            'errors' => $stats['errors'],
            'significant_changes' => count(array_filter($stats['score_changes'], fn ($change) => abs($change) >= 10)),
        ]);
    }

    /**
     * Process a single contact
     */
    private function processContact(Contact $contact, LeadScoringService $scoringService, array &$stats): void
    {
        try {
            // Check if we should skip this contact
            if (! $this->forceRecalculate && $this->shouldSkip($contact)) {
                $stats['skipped']++;

                return;
            }

            $previousScore = $contact->leadScore?->score;

            // Calculate new score
            $leadScore = $scoringService->calculateScore($contact);

            $stats['processed']++;

            // Track score change
            if ($previousScore !== null) {
                $change = $leadScore->score - $previousScore;
                $stats['score_changes'][] = $change;

                // Log significant changes
                if (abs($change) >= 15) {
                    Log::info('Significant lead score change detected', [
                        'contact_id' => $contact->id,
                        'contact_name' => $contact->full_name,
                        'previous_score' => $previousScore,
                        'new_score' => $leadScore->score,
                        'change' => $change,
                    ]);
                }
            }

        } catch (\Exception $e) {
            $stats['errors']++;

            Log::warning('Failed to recalculate lead score for contact', [
                'contact_id' => $contact->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Determine if contact should be skipped
     */
    private function shouldSkip(Contact $contact): bool
    {
        $leadScore = $contact->leadScore;

        // Always process contacts without a score
        if (! $leadScore) {
            return false;
        }

        // Check if score is stale
        if (! $leadScore->isStale($this->staleHoursThreshold)) {
            return true;
        }

        return false;
    }

    /**
     * Handle a job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Lead score recalculation job failed permanently', [
            'error' => $exception->getMessage(),
            'force_recalculate' => $this->forceRecalculate,
            'specific_contact' => $this->contactId,
        ]);
    }
}
