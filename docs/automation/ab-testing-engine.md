# A/B Testing Engine

The A/B Testing Engine provides comprehensive experimentation capabilities directly within Stafe CRM. Test email subjects, content variations, cadence sequences, and workflow paths to optimize your sales and marketing efforts with statistical confidence.

## Overview

The A/B Testing Engine enables you to:

- **Test email campaigns** with different subjects, content, and sending times
- **Optimize cadence sequences** by comparing different messaging approaches
- **Evaluate workflow paths** to find the most effective automation flows
- **Measure template performance** across dynamic content variations
- **Make data-driven decisions** with statistical significance testing

## Key Features

### 🧪 Comprehensive Testing
- Multiple test types (email, content, cadence, workflow)
- Flexible participant assignment with traffic splitting
- Statistical significance calculation with confidence intervals
- Real-time results tracking and analysis

### 📊 Advanced Analytics
- Conversion tracking across multiple metrics
- Statistical significance testing (Z-test with p-values)
- Confidence intervals and margin of error calculations
- Automated recommendations based on results

### 🎯 Smart Targeting
- Automatic participant assignment based on traffic split
- Consistent assignment using entity-based hashing
- Context tracking for detailed analysis
- Engagement scoring and interaction monitoring

### 🔄 Automated Management
- Automatic test completion based on sample size
- Real-time performance monitoring
- Integration with workflow automation
- Export capabilities for external analysis

## Test Types

### 1. Email Subject Line Testing

Test different email subject lines to improve open rates:

```php
use App\Models\AbTest;

$emailSubjectTest = AbTest::create([
    'name' => 'Welcome Email Subject Test',
    'description' => 'Testing personalized vs generic subject lines',
    'test_type' => 'email_subject',
    'test_configuration' => [
        'email_template_id' => 5,
        'target_audience' => 'new_contacts',
        'primary_metric' => 'open_rate'
    ],
    'variant_a' => [
        'subject' => 'Welcome to {{company_name}}!',
        'description' => 'Generic welcome message'
    ],
    'variant_b' => [
        'subject' => '{{first_name}}, your personalized demo is ready!',
        'description' => 'Personalized with action-oriented CTA'
    ],
    'traffic_split' => 0.50, // 50/50 split
    'start_date' => now(),
    'end_date' => now()->addDays(14),
    'minimum_sample_size' => 100,
    'confidence_level' => 0.95,
    'success_metrics' => [
        [
            'name' => 'open_rate',
            'type' => 'conversion',
            'description' => 'Email open rate'
        ],
        [
            'name' => 'click_rate', 
            'type' => 'conversion',
            'description' => 'Email click-through rate'
        ]
    ],
    'status' => 'active'
]);
```

### 2. Email Content Testing

Compare different email content approaches:

```php
$contentTest = AbTest::create([
    'name' => 'Follow-up Email Content Test',
    'test_type' => 'email_content',
    'variant_a' => [
        'template_id' => 10,
        'content_approach' => 'direct_sales',
        'description' => 'Direct sales pitch with pricing'
    ],
    'variant_b' => [
        'template_id' => 11,
        'content_approach' => 'educational',
        'description' => 'Educational content with case studies'
    ],
    'success_metrics' => [
        [
            'name' => 'response_rate',
            'type' => 'conversion',
            'description' => 'Email response rate'
        ],
        [
            'name' => 'meeting_booked',
            'type' => 'conversion', 
            'description' => 'Meeting booking rate'
        ]
    ]
]);
```

### 3. Cadence Sequence Testing

Test different cadence approaches:

```php
$cadenceTest = AbTest::create([
    'name' => 'Outbound Cadence Optimization',
    'test_type' => 'cadence_sequence',
    'variant_a' => [
        'cadence_id' => 3,
        'approach' => 'aggressive',
        'description' => '7-touch sequence over 2 weeks'
    ],
    'variant_b' => [
        'cadence_id' => 4,
        'approach' => 'consultative',
        'description' => '5-touch sequence over 4 weeks'
    ],
    'success_metrics' => [
        [
            'name' => 'reply_rate',
            'type' => 'conversion',
            'description' => 'Positive reply rate'
        ],
        [
            'name' => 'qualified_opportunity',
            'type' => 'conversion',
            'description' => 'Qualified opportunity creation'
        ]
    ]
]);
```

### 4. Workflow Path Testing

Test different automation workflows:

```php
$workflowTest = AbTest::create([
    'name' => 'Lead Nurturing Workflow Test',
    'test_type' => 'workflow_path',
    'variant_a' => [
        'workflow_id' => 8,
        'approach' => 'immediate_contact',
        'description' => 'Immediate sales contact workflow'
    ],
    'variant_b' => [
        'workflow_id' => 9,
        'approach' => 'nurture_sequence',
        'description' => 'Educational nurture sequence'
    ],
    'success_metrics' => [
        [
            'name' => 'conversion_to_sql',
            'type' => 'conversion',
            'description' => 'Sales qualified lead conversion'
        ],
        [
            'name' => 'deal_created',
            'type' => 'conversion',
            'description' => 'Deal creation rate'
        ]
    ]
]);
```

## Participant Management

### Automatic Assignment

Participants are automatically assigned to variants based on consistent hashing:

```php
use App\Models\Contact;

$contact = Contact::find(1);
$test = AbTest::find(1);

// Assign participant to test
$participant = $test->assignParticipant($contact, [
    'source' => 'email_campaign',
    'campaign_id' => 123
]);

echo $participant->variant; // 'a' or 'b'
```

### Variant Configuration Retrieval

Get the appropriate configuration for a participant:

```php
$variantConfig = $test->getVariantForParticipant($contact);

// Use variant configuration
if ($variantConfig['template_id']) {
    $template = DynamicContentTemplate::find($variantConfig['template_id']);
    $emailContent = $template->generateContent($contact);
}
```

### Interaction Tracking

Track participant interactions throughout the test:

```php
// Record email open
$participant->recordInteraction('email_open', [
    'email_id' => 456,
    'timestamp' => now(),
    'user_agent' => request()->userAgent()
]);

// Record click
$participant->recordInteraction('email_click', [
    'email_id' => 456,
    'link_url' => 'https://example.com/demo',
    'click_position' => 'cta_button'
]);

// Record form submission
$participant->recordInteraction('form_submit', [
    'form_type' => 'demo_request',
    'form_id' => 789
]);
```

## Conversion Tracking

### Single Conversion

Record a conversion for a specific metric:

```php
// Record email open conversion
$test->recordConversion($contact, 'open_rate', 1, [
    'email_id' => 456,
    'open_time' => now()
]);

// Record meeting booking conversion
$test->recordConversion($contact, 'meeting_booked', 1, [
    'meeting_id' => 789,
    'meeting_date' => now()->addDays(3)
]);
```

### Multiple Conversions

Record multiple conversions at once:

```php
$participant = $test->participants()
    ->where('participant_type', get_class($contact))
    ->where('participant_id', $contact->id)
    ->first();

$participant->recordConversions([
    'email_open' => 1,
    'email_click' => 1,
    'demo_request' => 1
], [
    'session_id' => session()->getId(),
    'referrer' => request()->header('referer')
]);
```

### Conversion Value Tracking

Track conversions with specific values:

```php
// Track deal value conversion
$test->recordConversion($contact, 'deal_value', 50000, [
    'deal_id' => 123,
    'close_date' => now(),
    'sales_rep' => auth()->user()->id
]);

// Track engagement score improvement
$test->recordConversion($contact, 'engagement_increase', 2.5, [
    'previous_score' => 6.5,
    'new_score' => 9.0
]);
```

## Statistical Analysis

### Real-time Results

Get current test results with statistical significance:

```php
$results = $test->calculateResults();

/*
Returns:
[
    'participants' => [
        'total' => 150,
        'variant_a' => 75,
        'variant_b' => 75
    ],
    'metrics' => [
        'open_rate' => [
            'variant_a' => [
                'participants' => 75,
                'conversions' => 30,
                'conversion_rate' => 0.40,
                'total_value' => 30,
                'average_value' => 1.00
            ],
            'variant_b' => [
                'participants' => 75,
                'conversions' => 38,
                'conversion_rate' => 0.507,
                'total_value' => 38,
                'average_value' => 1.00
            ],
            'improvement' => [
                'conversion_rate' => 26.75, // % improvement
                'average_value' => 0
            ]
        ]
    ],
    'statistical_significance' => [
        'open_rate' => [
            'is_significant' => true,
            'p_value' => 0.032,
            'z_score' => 2.15,
            'confidence_interval' => [
                'lower' => 0.012,
                'upper' => 0.195
            ]
        ]
    ],
    'recommendation' => [
        'action' => 'implement_variant_b',
        'reason' => 'Variant B shows significant improvement',
        'confidence' => 'high',
        'winning_variant' => 'b'
    ]
]
*/
```

### Test Completion

Automatically complete tests when criteria are met:

```php
// Check if test is ready for completion
if ($test->isReadyToComplete()) {
    $finalResults = $test->complete();
    
    // Implement winning variant
    if ($finalResults['recommendation']['action'] === 'implement_variant_b') {
        // Update default templates, workflows, etc.
        $this->implementWinningVariant($test, 'b');
    }
}
```

### Performance Summary

Get a quick test summary:

```php
$summary = $test->getSummary();

/*
Returns:
[
    'name' => 'Welcome Email Subject Test',
    'status' => 'completed',
    'participants' => 150,
    'duration_days' => 14,
    'has_significant_results' => true,
    'recommendation' => [
        'action' => 'implement_variant_b',
        'winning_variant' => 'b'
    ]
]
*/
```

## Integration Examples

### 1. Email Campaign A/B Testing

```php
use App\Models\AbTest;
use App\Models\Contact;
use App\Services\EmailService;

class EmailCampaignService
{
    public function sendTestEmail(Contact $contact, int $campaignId)
    {
        // Find active A/B test for this campaign
        $test = AbTest::active()
            ->where('test_type', 'email_subject')
            ->where('test_configuration->campaign_id', $campaignId)
            ->first();

        if (!$test) {
            // No test running, send default
            return $this->sendDefaultEmail($contact, $campaignId);
        }

        // Assign participant and get variant
        $participant = $test->assignParticipant($contact, [
            'campaign_id' => $campaignId,
            'source' => 'automated_campaign'
        ]);

        $variant = $test->getVariantForParticipant($contact);
        
        // Send email based on variant
        $email = $this->buildEmail($contact, $variant);
        $sent = EmailService::send($email);

        if ($sent) {
            // Track email sent
            $participant->recordInteraction('email_sent', [
                'email_id' => $email->id,
                'subject' => $email->subject,
                'variant' => $participant->variant
            ]);
        }

        return $sent;
    }

    public function trackEmailOpen(int $emailId, Contact $contact)
    {
        // Find associated A/B test
        $participant = AbTestParticipant::where('participant_type', get_class($contact))
            ->where('participant_id', $contact->id)
            ->whereHas('abTest', function ($query) {
                $query->where('status', 'active');
            })
            ->first();

        if ($participant) {
            // Record conversion
            $participant->abTest->recordConversion($contact, 'open_rate', 1, [
                'email_id' => $emailId,
                'open_time' => now()
            ]);
        }
    }
}
```

### 2. Workflow A/B Testing

```php
use App\Models\WorkflowTemplate;

class WorkflowService
{
    public function executeWorkflow(Contact $contact, int $workflowId)
    {
        // Check for active workflow A/B test
        $test = AbTest::active()
            ->where('test_type', 'workflow_path')
            ->where(function ($query) use ($workflowId) {
                $query->whereJsonContains('variant_a->workflow_id', $workflowId)
                      ->orWhereJsonContains('variant_b->workflow_id', $workflowId);
            })
            ->first();

        if ($test) {
            $participant = $test->assignParticipant($contact);
            $variant = $test->getVariantForParticipant($contact);
            $actualWorkflowId = $variant['workflow_id'];
        } else {
            $actualWorkflowId = $workflowId;
        }

        // Execute the workflow
        $workflow = WorkflowTemplate::find($actualWorkflowId);
        $instance = $workflow->execute($contact);

        // Track workflow execution
        if (isset($test)) {
            $participant->recordInteraction('workflow_started', [
                'workflow_id' => $actualWorkflowId,
                'instance_id' => $instance->id,
                'variant' => $participant->variant
            ]);
        }

        return $instance;
    }

    public function trackWorkflowCompletion(WorkflowInstance $instance)
    {
        // Find associated A/B test participant
        $contact = $instance->entity;
        if (!$contact) return;

        $participant = AbTestParticipant::where('participant_type', get_class($contact))
            ->where('participant_id', $contact->id)
            ->whereHas('abTest', function ($query) {
                $query->where('test_type', 'workflow_path')
                      ->where('status', 'active');
            })
            ->first();

        if ($participant && $instance->isCompleted()) {
            // Record workflow completion
            $participant->abTest->recordConversion($contact, 'workflow_completion', 1, [
                'workflow_id' => $instance->workflow_template_id,
                'instance_id' => $instance->id,
                'completion_time' => $instance->completed_at
            ]);
        }
    }
}
```

### 3. Cadence Sequence Testing

```php
class CadenceService
{
    public function enrollInCadence(Contact $contact, int $cadenceId)
    {
        // Check for active cadence A/B test
        $test = AbTest::active()
            ->where('test_type', 'cadence_sequence')
            ->where(function ($query) use ($cadenceId) {
                $query->whereJsonContains('variant_a->cadence_id', $cadenceId)
                      ->orWhereJsonContains('variant_b->cadence_id', $cadenceId);
            })
            ->first();

        if ($test) {
            $participant = $test->assignParticipant($contact);
            $variant = $test->getVariantForParticipant($contact);
            $actualCadenceId = $variant['cadence_id'];
        } else {
            $actualCadenceId = $cadenceId;
        }

        // Enroll in cadence
        $enrollment = CadenceEnrollment::create([
            'cadence_sequence_id' => $actualCadenceId,
            'contact_id' => $contact->id,
            'status' => 'active',
            'current_step' => 1
        ]);

        // Track enrollment
        if (isset($test)) {
            $participant->recordInteraction('cadence_enrolled', [
                'cadence_id' => $actualCadenceId,
                'enrollment_id' => $enrollment->id
            ]);
        }

        return $enrollment;
    }

    public function trackCadenceResponse(Contact $contact, string $responseType)
    {
        $participant = AbTestParticipant::where('participant_type', get_class($contact))
            ->where('participant_id', $contact->id)
            ->whereHas('abTest', function ($query) {
                $query->where('test_type', 'cadence_sequence')
                      ->where('status', 'active');
            })
            ->first();

        if ($participant) {
            // Record response based on type
            switch ($responseType) {
                case 'positive_reply':
                    $participant->abTest->recordConversion($contact, 'reply_rate', 1);
                    break;
                case 'meeting_booked':
                    $participant->abTest->recordConversion($contact, 'meeting_booked', 1);
                    break;
                case 'qualified_opportunity':
                    $participant->abTest->recordConversion($contact, 'qualified_opportunity', 1);
                    break;
            }
        }
    }
}
```

## Advanced Features

### 1. Multi-variant Testing

Test more than two variants:

```php
$multiVariantTest = AbTest::create([
    'name' => 'Email Time Optimization',
    'test_type' => 'email_timing',
    'variant_a' => ['send_time' => '09:00', 'description' => 'Morning'],
    'variant_b' => ['send_time' => '14:00', 'description' => 'Afternoon'],
    'variant_c' => ['send_time' => '18:00', 'description' => 'Evening'], // Custom field
    'traffic_split_config' => [
        'a' => 0.33,
        'b' => 0.33,
        'c' => 0.34
    ]
]);
```

### 2. Sequential Testing

Run tests in sequence based on results:

```php
class SequentialTestManager
{
    public function setupSequentialTest($initialTest)
    {
        $initialTest->update([
            'test_configuration' => array_merge(
                $initialTest->test_configuration,
                ['sequential_test' => true, 'next_test_config' => [...]]
            )
        ]);
    }

    public function checkForNextTest(AbTest $completedTest)
    {
        if ($completedTest->test_configuration['sequential_test'] ?? false) {
            $nextTestConfig = $completedTest->test_configuration['next_test_config'];
            
            // Create follow-up test based on results
            if ($completedTest->results['recommendation']['winning_variant'] === 'b') {
                return $this->createFollowUpTest($nextTestConfig, $completedTest);
            }
        }
        
        return null;
    }
}
```

### 3. Cohort Analysis

Analyze results by cohorts:

```php
class CohortAnalysis
{
    public function analyzeBySegment(AbTest $test, array $segments)
    {
        $results = [];
        
        foreach ($segments as $segment) {
            $participants = $test->participants()
                ->whereJsonContains('context->segment', $segment)
                ->get();
                
            $results[$segment] = $this->calculateSegmentResults($participants);
        }
        
        return $results;
    }

    public function analyzeByTimeframe(AbTest $test, string $interval = 'daily')
    {
        $participants = $test->participants()
            ->selectRaw("DATE(assigned_at) as date, variant, COUNT(*) as count")
            ->groupBy(['date', 'variant'])
            ->get();
            
        return $this->formatTimeframeResults($participants, $interval);
    }
}
```

## API Reference

### Create A/B Test
```http
POST /api/ab-tests
Content-Type: application/json

{
    "name": "Email Subject Test",
    "test_type": "email_subject",
    "variant_a": {...},
    "variant_b": {...},
    "traffic_split": 0.5,
    "minimum_sample_size": 100,
    "success_metrics": [...]
}
```

### Assign Participant
```http
POST /api/ab-tests/{id}/participants
Content-Type: application/json

{
    "participant_type": "Contact",
    "participant_id": 123,
    "context": {...}
}
```

### Record Conversion
```http
POST /api/ab-tests/{id}/conversions
Content-Type: application/json

{
    "participant_type": "Contact", 
    "participant_id": 123,
    "metric_name": "open_rate",
    "value": 1,
    "metadata": {...}
}
```

### Get Test Results
```http
GET /api/ab-tests/{id}/results
```

### Complete Test
```http
POST /api/ab-tests/{id}/complete
```

## Best Practices

### 1. Test Design
- **Single variable testing**: Test one variable at a time for clear attribution
- **Sufficient sample size**: Ensure statistical power with adequate participants
- **Meaningful differences**: Test variations likely to produce measurable differences
- **Clear success metrics**: Define specific, measurable success criteria

### 2. Statistical Rigor
- **Set confidence levels**: Use appropriate confidence levels (typically 95%)
- **Avoid peeking**: Don't stop tests early based on early results
- **Consider practical significance**: Statistical significance doesn't always mean practical importance
- **Multiple comparisons**: Adjust for multiple testing when running many simultaneous tests

### 3. Implementation
- **Consistent assignment**: Ensure participants always get the same variant
- **Proper tracking**: Implement comprehensive conversion tracking
- **Clean data**: Remove outliers and invalid data points
- **Documentation**: Document test hypotheses, setups, and results

### 4. Business Impact
- **Cost-benefit analysis**: Consider test costs vs. potential improvements
- **Rollout planning**: Plan how to implement winning variants
- **Continuous testing**: Establish ongoing testing culture
- **Cross-team alignment**: Ensure all stakeholders understand test goals

## Troubleshooting

### Common Issues

**Issue**: Uneven participant distribution
- **Solution**: Check traffic split configuration and participant assignment logic

**Issue**: Low statistical power
- **Solution**: Increase sample size or test duration, reduce noise in measurements

**Issue**: Inconsistent results
- **Solution**: Check for external factors, seasonal effects, or implementation bugs

**Issue**: No significant results
- **Solution**: Test larger differences, increase sample size, or extend test duration

### Debug Tools

```php
// Enable detailed participant tracking
$test->update(['debug_mode' => true]);

// Export participant data for analysis
$participantData = $test->participants()->get()->map->exportData();

// Validate test statistical power
$powerAnalysis = StatisticalAnalysis::calculatePower($test);
```

---

The A/B Testing Engine enables data-driven optimization across all aspects of your CRM operations. By systematically testing and measuring variations in your communications, workflows, and processes, you can continuously improve performance and achieve better results with statistical confidence.