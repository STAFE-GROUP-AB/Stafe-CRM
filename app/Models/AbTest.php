<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AbTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'test_type',
        'test_configuration',
        'variant_a',
        'variant_b',
        'traffic_split',
        'status',
        'start_date',
        'end_date',
        'minimum_sample_size',
        'confidence_level',
        'success_metrics',
        'results',
        'created_by_user_id',
        'tenant_id',
    ];

    protected $casts = [
        'test_configuration' => 'array',
        'variant_a' => 'array',
        'variant_b' => 'array',
        'success_metrics' => 'array',
        'results' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'traffic_split' => 'decimal:2',
        'confidence_level' => 'decimal:2',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(AbTestParticipant::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('start_date', '<=', now())
                    ->where(function ($q) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', now());
                    });
    }

    public function scopeByType($query, string $testType)
    {
        return $query->where('test_type', $testType);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Assign participant to a variant
     */
    public function assignParticipant($entity, array $context = []): AbTestParticipant
    {
        // Check if participant already exists
        $existingParticipant = $this->participants()
            ->where('participant_type', get_class($entity))
            ->where('participant_id', $entity->id)
            ->first();

        if ($existingParticipant) {
            return $existingParticipant;
        }

        // Determine variant based on traffic split
        $variant = $this->determineVariant($entity);

        return AbTestParticipant::create([
            'ab_test_id' => $this->id,
            'participant_type' => get_class($entity),
            'participant_id' => $entity->id,
            'variant' => $variant,
            'assigned_at' => now(),
            'context' => $context,
        ]);
    }

    /**
     * Determine which variant to assign based on traffic split
     */
    private function determineVariant($entity): string
    {
        // Use entity ID for consistent assignment
        $hash = crc32($entity->id . $this->id);
        $normalized = ($hash % 100) / 100;

        return $normalized < $this->traffic_split ? 'a' : 'b';
    }

    /**
     * Get variant configuration for a participant
     */
    public function getVariantForParticipant($entity): array
    {
        $participant = $this->participants()
            ->where('participant_type', get_class($entity))
            ->where('participant_id', $entity->id)
            ->first();

        if (!$participant) {
            $participant = $this->assignParticipant($entity);
        }

        return $participant->variant === 'a' ? $this->variant_a : $this->variant_b;
    }

    /**
     * Record conversion for participant
     */
    public function recordConversion($entity, string $metricName, $value = 1, array $metadata = []): void
    {
        $participant = $this->participants()
            ->where('participant_type', get_class($entity))
            ->where('participant_id', $entity->id)
            ->first();

        if (!$participant) {
            return; // Can't record conversion without participant
        }

        $participant->recordConversion($metricName, $value, $metadata);
    }

    /**
     * Calculate test results
     */
    public function calculateResults(): array
    {
        $participantsA = $this->participants()->where('variant', 'a')->get();
        $participantsB = $this->participants()->where('variant', 'b')->get();

        $results = [
            'participants' => [
                'total' => $this->participants()->count(),
                'variant_a' => $participantsA->count(),
                'variant_b' => $participantsB->count(),
            ],
            'metrics' => [],
            'statistical_significance' => [],
            'recommendation' => null,
        ];

        foreach ($this->success_metrics as $metric) {
            $metricResults = $this->calculateMetricResults($metric['name'], $participantsA, $participantsB);
            $results['metrics'][$metric['name']] = $metricResults;

            // Calculate statistical significance
            $significance = $this->calculateStatisticalSignificance($metricResults);
            $results['statistical_significance'][$metric['name']] = $significance;
        }

        // Determine recommendation
        $results['recommendation'] = $this->generateRecommendation($results);

        // Update results in database
        $this->update(['results' => $results]);

        return $results;
    }

    /**
     * Calculate results for a specific metric
     */
    private function calculateMetricResults(string $metricName, $participantsA, $participantsB): array
    {
        $conversionsA = $participantsA->filter(function ($p) use ($metricName) {
            return isset($p->conversions[$metricName]);
        });

        $conversionsB = $participantsB->filter(function ($p) use ($metricName) {
            return isset($p->conversions[$metricName]);
        });

        $valueA = $conversionsA->sum(function ($p) use ($metricName) {
            return $p->conversions[$metricName]['value'] ?? 1;
        });

        $valueB = $conversionsB->sum(function ($p) use ($metricName) {
            return $p->conversions[$metricName]['value'] ?? 1;
        });

        $rateA = $participantsA->count() > 0 ? $conversionsA->count() / $participantsA->count() : 0;
        $rateB = $participantsB->count() > 0 ? $conversionsB->count() / $participantsB->count() : 0;

        $avgValueA = $conversionsA->count() > 0 ? $valueA / $conversionsA->count() : 0;
        $avgValueB = $conversionsB->count() > 0 ? $valueB / $conversionsB->count() : 0;

        return [
            'variant_a' => [
                'participants' => $participantsA->count(),
                'conversions' => $conversionsA->count(),
                'total_value' => $valueA,
                'conversion_rate' => $rateA,
                'average_value' => $avgValueA,
            ],
            'variant_b' => [
                'participants' => $participantsB->count(),
                'conversions' => $conversionsB->count(),
                'total_value' => $valueB,
                'conversion_rate' => $rateB,
                'average_value' => $avgValueB,
            ],
            'improvement' => [
                'conversion_rate' => $rateA > 0 ? (($rateB - $rateA) / $rateA * 100) : 0,
                'average_value' => $avgValueA > 0 ? (($avgValueB - $avgValueA) / $avgValueA * 100) : 0,
            ],
        ];
    }

    /**
     * Calculate statistical significance using Z-test
     */
    private function calculateStatisticalSignificance(array $metricResults): array
    {
        $nA = $metricResults['variant_a']['participants'];
        $nB = $metricResults['variant_b']['participants'];
        $xA = $metricResults['variant_a']['conversions'];
        $xB = $metricResults['variant_b']['conversions'];

        if ($nA < 30 || $nB < 30) {
            return [
                'is_significant' => false,
                'p_value' => null,
                'z_score' => null,
                'confidence_interval' => null,
                'note' => 'Insufficient sample size for statistical significance testing',
            ];
        }

        $pA = $xA / $nA;
        $pB = $xB / $nB;
        $pPooled = ($xA + $xB) / ($nA + $nB);

        if ($pPooled == 0 || $pPooled == 1) {
            return [
                'is_significant' => false,
                'p_value' => null,
                'z_score' => null,
                'confidence_interval' => null,
                'note' => 'No variance in conversion rates',
            ];
        }

        $se = sqrt($pPooled * (1 - $pPooled) * (1/$nA + 1/$nB));
        $zScore = ($pB - $pA) / $se;
        $pValue = 2 * (1 - $this->normalCdf(abs($zScore)));

        $criticalValue = 1.96; // For 95% confidence
        $isSignificant = abs($zScore) > $criticalValue;

        // Confidence interval for difference in proportions
        $seDiff = sqrt($pA * (1 - $pA) / $nA + $pB * (1 - $pB) / $nB);
        $diff = $pB - $pA;
        $marginOfError = $criticalValue * $seDiff;

        return [
            'is_significant' => $isSignificant,
            'p_value' => $pValue,
            'z_score' => $zScore,
            'confidence_interval' => [
                'lower' => $diff - $marginOfError,
                'upper' => $diff + $marginOfError,
            ],
            'margin_of_error' => $marginOfError,
        ];
    }

    /**
     * Approximate normal cumulative distribution function
     */
    private function normalCdf($x): float
    {
        return 0.5 * (1 + $this->erf($x / sqrt(2)));
    }

    /**
     * Approximate error function
     */
    private function erf($x): float
    {
        $a1 =  0.254829592;
        $a2 = -0.284496736;
        $a3 =  1.421413741;
        $a4 = -1.453152027;
        $a5 =  1.061405429;
        $p  =  0.3275911;

        $sign = $x < 0 ? -1 : 1;
        $x = abs($x);

        $t = 1.0/(1.0 + $p*$x);
        $y = 1.0 - ((((($a5*$t + $a4)*$t) + $a3)*$t + $a2)*$t + $a1)*$t*exp(-$x*$x);

        return $sign*$y;
    }

    /**
     * Generate recommendation based on results
     */
    private function generateRecommendation(array $results): array
    {
        $recommendation = [
            'action' => 'continue_testing',
            'reason' => 'Insufficient data or significance',
            'confidence' => 'low',
        ];

        // Check if we have enough participants
        if ($results['participants']['total'] < $this->minimum_sample_size) {
            $recommendation['reason'] = 'Sample size too small';
            return $recommendation;
        }

        // Check each metric for significance
        $significantMetrics = [];
        foreach ($results['statistical_significance'] as $metric => $significance) {
            if ($significance['is_significant'] && $significance['p_value'] < (1 - $this->confidence_level)) {
                $significantMetrics[$metric] = $results['metrics'][$metric];
            }
        }

        if (empty($significantMetrics)) {
            $recommendation['reason'] = 'No statistically significant differences found';
            return $recommendation;
        }

        // Determine winning variant
        $variantBWins = 0;
        $variantAWins = 0;

        foreach ($significantMetrics as $metric => $data) {
            if ($data['improvement']['conversion_rate'] > 0) {
                $variantBWins++;
            } else {
                $variantAWins++;
            }
        }

        if ($variantBWins > $variantAWins) {
            $recommendation = [
                'action' => 'implement_variant_b',
                'reason' => 'Variant B shows significant improvement',
                'confidence' => 'high',
                'winning_variant' => 'b',
            ];
        } elseif ($variantAWins > $variantBWins) {
            $recommendation = [
                'action' => 'keep_variant_a',
                'reason' => 'Variant A performs significantly better',
                'confidence' => 'high',
                'winning_variant' => 'a',
            ];
        } else {
            $recommendation = [
                'action' => 'continue_testing',
                'reason' => 'Mixed results across metrics',
                'confidence' => 'medium',
            ];
        }

        return $recommendation;
    }

    /**
     * Check if test is ready to be completed
     */
    public function isReadyToComplete(): bool
    {
        return $this->status === 'active' 
            && $this->participants()->count() >= $this->minimum_sample_size
            && (!$this->end_date || $this->end_date <= now());
    }

    /**
     * Complete the test and generate final results
     */
    public function complete(): array
    {
        $results = $this->calculateResults();
        
        $this->update([
            'status' => 'completed',
            'end_date' => now()->toDateString(),
            'results' => $results,
        ]);

        return $results;
    }

    /**
     * Get test summary
     */
    public function getSummary(): array
    {
        $results = $this->results ?? $this->calculateResults();

        return [
            'name' => $this->name,
            'status' => $this->status,
            'participants' => $results['participants']['total'] ?? 0,
            'duration_days' => $this->start_date->diffInDays($this->end_date ?? now()),
            'has_significant_results' => collect($results['statistical_significance'] ?? [])
                ->some(fn($sig) => $sig['is_significant'] ?? false),
            'recommendation' => $results['recommendation'] ?? null,
        ];
    }
}