# Event-Driven Triggers

Event-Driven Triggers provide a sophisticated automation engine that responds to real-time events throughout your CRM. This powerful system enables complex, intelligent automation workflows that execute automatically based on data changes, user actions, time-based conditions, and external system events.

## Overview

Event-Driven Triggers allow you to create automated responses to:

- **Model Events**: Contact created, deal updated, company deleted
- **Field Changes**: Specific field modifications with before/after values
- **Time-Based Events**: Scheduled actions and recurring automations
- **External Events**: API calls, webhooks, and third-party integrations
- **User Actions**: Manual triggers and custom business events

## Key Features

### 🎯 Smart Trigger System
- Multiple trigger types with flexible configuration
- Complex condition evaluation with logical operators
- Real-time event processing and execution
- Retry logic with exponential backoff

### ⚡ Action Engine
- Multi-step action sequences
- Dynamic content integration
- Cross-entity automation
- Webhook and API integrations

### 🔄 Rate Limiting & Control
- Configurable rate limiting per trigger
- Execution scheduling and delays
- Retry mechanisms for failed actions
- Performance monitoring and optimization

### 📊 Comprehensive Tracking
- Detailed execution logs
- Performance analytics
- Error monitoring and alerting
- Success rate tracking

## Trigger Types

### 1. Model-Based Triggers

React to standard model lifecycle events:

```php
use App\Models\EventTrigger;

// Trigger when a new contact is created
$newContactTrigger = EventTrigger::create([
    'name' => 'New Contact Welcome Sequence',
    'description' => 'Send welcome email and create follow-up task',
    'trigger_type' => 'model_created',
    'model_type' => 'App\Models\Contact',
    'trigger_conditions' => [
        [
            'field' => 'source',
            'operator' => 'equals',
            'value' => 'website_form'
        ]
    ],
    'action_configuration' => [
        'actions' => [
            [
                'type' => 'send_email',
                'template_id' => 1,
                'recipient' => 'entity_email'
            ],
            [
                'type' => 'create_task',
                'title' => 'Follow up with new contact',
                'due_days' => 1,
                'assigned_to' => 'auto'
            ]
        ]
    ],
    'is_active' => true,
]);
```

### 2. Field Change Triggers

Monitor specific field modifications:

```php
// Trigger when deal stage changes to "Negotiation"
$dealStageTrigger = EventTrigger::create([
    'name' => 'Deal Negotiation Alert',
    'trigger_type' => 'field_changed',
    'model_type' => 'App\Models\Deal',
    'trigger_conditions' => [
        [
            'field' => 'stage',
            'operator' => 'changed_to',
            'value' => 'negotiation'
        ],
        [
            'field' => 'value',
            'operator' => 'greater_than',
            'value' => 50000
        ]
    ],
    'action_configuration' => [
        'actions' => [
            [
                'type' => 'webhook',
                'url' => 'https://slack.com/api/chat.postMessage',
                'method' => 'POST',
                'headers' => ['Authorization' => 'Bearer {slack_token}'],
                'body' => [
                    'channel' => '#sales-alerts',
                    'text' => 'High-value deal {{entity.name}} entered negotiation phase!'
                ]
            ],
            [
                'type' => 'assign_to_user',
                'user_id' => 'sales_manager'
            ]
        ]
    ]
]);
```

### 3. Time-Based Triggers

Schedule automated actions:

```php
// Daily stalled deal reminder
$stalledDealTrigger = EventTrigger::create([
    'name' => 'Stalled Deal Daily Reminder',
    'trigger_type' => 'time_based',
    'trigger_conditions' => [
        [
            'field' => 'updated_at',
            'operator' => 'less_than',
            'value' => '7 days ago'
        ],
        [
            'field' => 'stage',
            'operator' => 'not_in',
            'value' => ['won', 'lost', 'closed']
        ]
    ],
    'action_configuration' => [
        'schedule' => 'daily',
        'actions' => [
            [
                'type' => 'send_email',
                'template_id' => 5,
                'recipient' => 'assigned_user'
            ]
        ]
    ]
]);
```

### 4. External API Triggers

Respond to external system events:

```php
// Process webhook from marketing automation
$marketingWebhookTrigger = EventTrigger::create([
    'name' => 'Marketing Qualified Lead',
    'trigger_type' => 'webhook',
    'trigger_conditions' => [
        [
            'field' => 'score',
            'operator' => 'greater_than',
            'value' => 80
        ],
        [
            'field' => 'source',
            'operator' => 'equals',
            'value' => 'marketing_automation'
        ]
    ],
    'action_configuration' => [
        'actions' => [
            [
                'type' => 'update_field',
                'field' => 'status',
                'value' => 'marketing_qualified'
            ],
            [
                'type' => 'trigger_workflow',
                'workflow_id' => 3
            ]
        ]
    ]
]);
```

## Action Types

### 1. Communication Actions

#### Send Email
```php
[
    'type' => 'send_email',
    'template_id' => 1,                    // Dynamic content template ID
    'recipient' => 'entity_email',         // or 'assigned_user', 'custom'
    'custom_email' => 'manager@company.com', // if recipient is 'custom'
    'subject' => 'Custom subject',         // override template subject
    'priority' => 'high'                   // email priority
]
```

#### Send SMS
```php
[
    'type' => 'send_sms',
    'template_id' => 2,
    'recipient' => 'entity_phone',
    'message' => 'Custom message if no template'
]
```

### 2. Task & Activity Actions

#### Create Task
```php
[
    'type' => 'create_task',
    'title' => 'Follow up on {{entity.name}}',
    'description' => 'Automated follow-up task',
    'due_days' => 3,                       // days from now
    'assigned_to' => 'auto',               // or specific user ID
    'priority' => 'medium'
]
```

#### Create Note
```php
[
    'type' => 'create_note',
    'content' => 'Automated note: {{trigger_event}}',
    'is_private' => false,
    'created_by' => 'system'
]
```

### 3. Data Modification Actions

#### Update Field
```php
[
    'type' => 'update_field',
    'field' => 'status',
    'value' => 'qualified',
    'variable_mappings' => [               // for dynamic values
        'last_contact' => ['source' => 'computed', 'computation' => 'current_date']
    ]
]
```

#### Tag Management
```php
[
    'type' => 'add_tag',
    'tag' => 'high-priority'
],
[
    'type' => 'remove_tag',
    'tag' => 'prospect'
]
```

### 4. Workflow Actions

#### Trigger Another Workflow
```php
[
    'type' => 'trigger_workflow',
    'workflow_id' => 5,
    'context' => [
        'trigger_source' => 'event_trigger',
        'original_event' => '{{trigger_event}}'
    ]
]
```

#### Add to Cadence
```php
[
    'type' => 'add_to_cadence',
    'cadence_id' => 2,
    'start_step' => 1,
    'delay_hours' => 24
]
```

### 5. External Integration Actions

#### Webhook Call
```php
[
    'type' => 'webhook',
    'url' => 'https://api.external-system.com/webhook',
    'method' => 'POST',
    'headers' => [
        'Authorization' => 'Bearer {{api_token}}',
        'Content-Type' => 'application/json'
    ],
    'body' => [
        'entity_type' => '{{entity_type}}',
        'entity_id' => '{{entity_id}}',
        'trigger_event' => '{{trigger_event}}'
    ]
]
```

#### CRM Integration
```php
[
    'type' => 'sync_to_external',
    'integration' => 'salesforce',
    'mapping' => [
        'FirstName' => '{{entity.first_name}}',
        'LastName' => '{{entity.last_name}}',
        'Email' => '{{entity.email}}'
    ]
]
```

## Advanced Configuration

### Rate Limiting

Prevent trigger overexecution:

```php
$trigger->update([
    'rate_limiting' => [
        'limit' => 10,                     // max executions
        'period' => 'hour',               // per hour/day/minute
        'burst_limit' => 3,               // burst allowance
        'reset_strategy' => 'sliding'      // sliding or fixed window
    ]
]);
```

### Retry Configuration

Handle failures gracefully:

```php
$trigger->update([
    'allow_retries' => true,
    'max_retries' => 3,
    'retry_configuration' => [
        'delay_minutes' => 15,             // initial delay
        'backoff_multiplier' => 2,         // exponential backoff
        'max_delay_hours' => 24            // maximum delay
    ]
]);
```

### Conditional Logic

Complex condition evaluation:

```php
'trigger_conditions' => [
    [
        'field' => 'score',
        'operator' => 'greater_than',
        'value' => 75
    ],
    [
        'logic' => 'AND'                   // AND/OR logic
    ],
    [
        'group' => [                       // nested conditions
            [
                'field' => 'source',
                'operator' => 'in',
                'value' => ['website', 'referral']
            ],
            [
                'logic' => 'OR'
            ],
            [
                'field' => 'company.industry',
                'operator' => 'equals',
                'value' => 'technology'
            ]
        ]
    ]
]
```

## Usage Examples

### 1. Lead Qualification Automation

```php
use App\Models\EventTrigger;

$leadQualificationTrigger = EventTrigger::create([
    'name' => 'Automatic Lead Qualification',
    'description' => 'Qualify leads based on engagement and company data',
    'trigger_type' => 'field_changed',
    'model_type' => 'App\Models\Contact',
    'trigger_conditions' => [
        [
            'field' => 'engagement_score',
            'operator' => 'greater_than',
            'value' => 7
        ],
        [
            'field' => 'company.employee_count',
            'operator' => 'greater_than',
            'value' => 50
        ]
    ],
    'action_configuration' => [
        'actions' => [
            [
                'type' => 'update_field',
                'field' => 'status',
                'value' => 'qualified'
            ],
            [
                'type' => 'add_tag',
                'tag' => 'hot-lead'
            ],
            [
                'type' => 'assign_to_user',
                'user_id' => 'senior_sales_rep'
            ],
            [
                'type' => 'send_email',
                'template_id' => 3,
                'recipient' => 'assigned_user'
            ],
            [
                'type' => 'create_task',
                'title' => 'Contact qualified lead {{entity.full_name}}',
                'due_days' => 1,
                'priority' => 'high'
            ]
        ]
    ],
    'delay_minutes' => 5,  // Small delay to ensure all related updates are complete
    'is_active' => true
]);
```

### 2. Deal Risk Monitoring

```php
$dealRiskTrigger = EventTrigger::create([
    'name' => 'Deal Risk Alert System',
    'trigger_type' => 'time_based',
    'trigger_conditions' => [
        [
            'field' => 'stage',
            'operator' => 'in',
            'value' => ['proposal', 'negotiation']
        ],
        [
            'field' => 'last_activity_date',
            'operator' => 'less_than',
            'value' => '5 days ago'
        ]
    ],
    'action_configuration' => [
        'schedule' => 'daily',
        'actions' => [
            [
                'type' => 'update_field',
                'field' => 'risk_level',
                'value' => 'high'
            ],
            [
                'type' => 'webhook',
                'url' => 'https://hooks.slack.com/services/YOUR/SLACK/WEBHOOK',
                'method' => 'POST',
                'body' => [
                    'text' => '🚨 Deal {{entity.name}} at {{entity.company.name}} shows high risk - no activity for 5+ days'
                ]
            ],
            [
                'type' => 'create_task',
                'title' => 'URGENT: Re-engage stalled deal',
                'description' => 'Deal {{entity.name}} has been inactive for over 5 days',
                'assigned_to' => '{{entity.assigned_to}}',
                'priority' => 'urgent',
                'due_days' => 0
            ]
        ]
    ]
]);
```

### 3. Customer Onboarding Automation

```php
$onboardingTrigger = EventTrigger::create([
    'name' => 'Customer Onboarding Sequence',
    'trigger_type' => 'field_changed',
    'model_type' => 'App\Models\Deal',
    'trigger_conditions' => [
        [
            'field' => 'stage',
            'operator' => 'changed_to',
            'value' => 'won'
        ]
    ],
    'action_configuration' => [
        'actions' => [
            [
                'type' => 'send_email',
                'template_id' => 10, // Welcome email template
                'recipient' => 'entity_email'
            ],
            [
                'type' => 'create_task',
                'title' => 'Schedule onboarding call',
                'description' => 'Contact {{entity.contact.name}} to schedule onboarding',
                'assigned_to' => 'customer_success_team',
                'due_days' => 1
            ],
            [
                'type' => 'trigger_workflow',
                'workflow_id' => 7 // Onboarding workflow
            ],
            [
                'type' => 'webhook',
                'url' => 'https://api.billing-system.com/create-customer',
                'method' => 'POST',
                'headers' => ['Authorization' => 'Bearer {{billing_api_key}}'],
                'body' => [
                    'contact_id' => '{{entity.contact.id}}',
                    'plan' => '{{entity.plan_type}}',
                    'value' => '{{entity.value}}'
                ]
            ]
        ]
    ]
]);
```

### 4. Event Chain Automation

```php
// Primary trigger: Contact engagement score increases
$engagementTrigger = EventTrigger::create([
    'name' => 'High Engagement Detection',
    'trigger_type' => 'field_changed',
    'model_type' => 'App\Models\Contact',
    'trigger_conditions' => [
        [
            'field' => 'engagement_score',
            'operator' => 'greater_than',
            'value' => 8
        ]
    ],
    'action_configuration' => [
        'actions' => [
            [
                'type' => 'add_tag',
                'tag' => 'highly-engaged'
            ],
            [
                'type' => 'trigger_workflow',
                'workflow_id' => 4 // Engagement workflow
            ]
        ]
    ]
]);

// Secondary trigger: Tag addition
$tagTrigger = EventTrigger::create([
    'name' => 'Highly Engaged Contact Actions',
    'trigger_type' => 'model_updated',
    'model_type' => 'App\Models\Contact',
    'trigger_conditions' => [
        [
            'field' => 'tags',
            'operator' => 'contains',
            'value' => 'highly-engaged'
        ]
    ],
    'action_configuration' => [
        'actions' => [
            [
                'type' => 'assign_to_user',
                'user_id' => 'senior_account_manager'
            ],
            [
                'type' => 'create_task',
                'title' => 'Personal outreach to highly engaged contact',
                'priority' => 'high',
                'due_days' => 0
            ]
        ]
    ]
]);
```

## Monitoring & Analytics

### Execution Tracking

Monitor trigger performance:

```php
use App\Models\EventTriggerExecution;

// Get recent executions
$recentExecutions = EventTriggerExecution::with(['eventTrigger', 'entity'])
    ->where('created_at', '>=', now()->subDays(7))
    ->orderBy('created_at', 'desc')
    ->get();

// Success rate by trigger
$successRates = EventTrigger::withCount([
    'executions as total_executions',
    'executions as successful_executions' => function ($query) {
        $query->where('status', 'completed');
    }
])->get()->map(function ($trigger) {
    return [
        'name' => $trigger->name,
        'success_rate' => $trigger->total_executions > 0 
            ? ($trigger->successful_executions / $trigger->total_executions) * 100 
            : 0
    ];
});
```

### Performance Optimization

```php
// Find slow-executing triggers
$slowTriggers = EventTriggerExecution::selectRaw('event_trigger_id, AVG(TIMESTAMPDIFF(SECOND, started_at, completed_at)) as avg_duration')
    ->whereNotNull('started_at')
    ->whereNotNull('completed_at')
    ->groupBy('event_trigger_id')
    ->having('avg_duration', '>', 30) // More than 30 seconds
    ->with('eventTrigger')
    ->get();

// Identify error-prone triggers
$errorProneTriggers = EventTrigger::withCount([
    'executions as total_executions',
    'executions as failed_executions' => function ($query) {
        $query->where('status', 'failed');
    }
])->get()->filter(function ($trigger) {
    return $trigger->total_executions > 10 && 
           ($trigger->failed_executions / $trigger->total_executions) > 0.1; // >10% failure rate
});
```

## Best Practices

### 1. Trigger Design
- **Keep conditions simple**: Complex conditions are harder to debug and maintain
- **Use specific model types**: Avoid generic triggers that fire too frequently
- **Test thoroughly**: Use staging environment to test trigger behavior

### 2. Action Sequencing
- **Order matters**: Actions execute sequentially, plan dependencies carefully
- **Use delays strategically**: Add delays between related actions to ensure data consistency
- **Handle failures gracefully**: Design fallback actions for critical processes

### 3. Performance Optimization
- **Monitor execution times**: Regularly review trigger performance metrics
- **Use rate limiting**: Prevent trigger storms from overwhelming the system
- **Optimize conditions**: Use indexed fields in trigger conditions when possible

### 4. Error Handling
- **Enable retries**: Use retry logic for unreliable external integrations
- **Log everything**: Maintain detailed execution logs for debugging
- **Set up alerts**: Monitor failed executions and investigate patterns

### 5. Security & Compliance
- **Validate inputs**: Always validate external webhook data
- **Use secure communications**: HTTPS for all webhook URLs
- **Audit trail**: Maintain comprehensive logs for compliance requirements

## API Reference

### Create Event Trigger
```http
POST /api/event-triggers
Content-Type: application/json

{
    "name": "New Contact Welcome",
    "trigger_type": "model_created",
    "model_type": "App\\Models\\Contact",
    "trigger_conditions": [...],
    "action_configuration": {...},
    "is_active": true
}
```

### Execute Trigger Manually
```http
POST /api/event-triggers/{id}/execute
Content-Type: application/json

{
    "entity_type": "Contact",
    "entity_id": 123,
    "context": {...}
}
```

### Get Execution History
```http
GET /api/event-triggers/{id}/executions?limit=50&status=completed
```

## Troubleshooting

### Common Issues

**Issue**: Trigger not firing
- **Solution**: Check trigger conditions, model type, and active status

**Issue**: Actions failing
- **Solution**: Review action configuration, check external service availability

**Issue**: Rate limiting issues
- **Solution**: Adjust rate limits or optimize trigger conditions

**Issue**: Performance problems
- **Solution**: Add delays, optimize conditions, check external service response times

### Debug Mode

Enable detailed logging:

```php
$trigger->update(['debug_mode' => true]);
// Provides detailed execution logs including condition evaluation and action results
```

---

Event-Driven Triggers create intelligent, responsive automation that adapts to your business processes in real-time. Combined with Dynamic Content Templates and comprehensive analytics, they form the foundation of a truly intelligent CRM that works proactively to support your team's success.