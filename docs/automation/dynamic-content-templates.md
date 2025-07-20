# Dynamic Content Templates

Dynamic Content Templates enable personalized, behavior-based content generation across all communication channels in Stafe CRM. This powerful automation feature creates contextual, relevant messaging that adapts to each contact's unique profile and interaction history.

## Overview

Dynamic Content Templates allow you to create smart, adaptive content that:

- **Personalizes automatically** based on contact data, behavior, and context
- **Adapts in real-time** using conditional logic and computed variables
- **Scales efficiently** across email, SMS, tasks, and social media
- **Tracks performance** with built-in usage analytics
- **Integrates seamlessly** with workflows and automation triggers

## Key Features

### 🎯 Personalization Engine
- Variable substitution from contact, company, and deal data
- Behavioral data integration (engagement scores, interaction history)
- Real-time computed variables (time-based greetings, dynamic metrics)
- Multi-source data mapping for comprehensive personalization

### 🔄 Conditional Logic
- If-then content rules based on contact attributes
- Dynamic content blocks that show/hide based on conditions
- Fallback content for missing data scenarios
- Complex condition evaluation with multiple operators

### 📊 Performance Tracking
- Usage statistics and analytics
- Content effectiveness measurement
- A/B testing integration
- Performance optimization recommendations

### 🔌 Multi-Channel Support
- Email subjects and bodies
- SMS messages
- Task descriptions
- Social media posts
- Custom content types

## Setup Guide

### 1. Creating a Dynamic Content Template

```php
use App\Models\DynamicContentTemplate;

$template = DynamicContentTemplate::create([
    'name' => 'Personalized Follow-up Email',
    'description' => 'Tailored follow-up based on engagement and deal stage',
    'content_type' => 'email_body',
    'base_template' => '
        {{greeting}} {{first_name}},

        I hope this email finds you well. I wanted to follow up on our conversation about {{company_name}}\'s {{business_need}}.

        {{#if high_engagement}}
        Based on your recent activity and interest, I believe we can move forward with the next steps.
        {{else}}
        I understand you might be busy, but I wanted to ensure you have all the information you need.
        {{/if}}

        {{call_to_action}}

        Best regards,
        {{user_name}}
    ',
    'personalization_rules' => [
        'high_engagement' => ['source' => 'computed', 'computation' => 'engagement_score'],
        'business_need' => ['source' => 'entity', 'field' => 'tags.primary_interest'],
    ],
    'variable_mappings' => [
        'first_name' => ['source' => 'entity', 'field' => 'first_name'],
        'company_name' => ['source' => 'entity', 'field' => 'company.name'],
        'greeting' => ['source' => 'computed', 'computation' => 'time_of_day_greeting'],
        'user_name' => ['source' => 'context', 'field' => 'sender_name'],
        'call_to_action' => ['source' => 'computed', 'computation' => 'next_best_action'],
    ],
    'conditional_content' => [
        [
            'placeholder' => '{{#if high_engagement}}...{{else}}...{{/if}}',
            'condition' => ['field' => 'engagement_score', 'operator' => 'greater_than', 'value' => 7],
            'content' => 'Based on your recent activity and interest, I believe we can move forward with the next steps.',
            'fallback' => 'I understand you might be busy, but I wanted to ensure you have all the information you need.',
        ],
    ],
    'is_active' => true,
]);
```

### 2. Variable Mapping Configuration

Dynamic Content Templates support three types of variable sources:

#### Entity Variables
Map directly to contact, company, or deal fields:
```php
'variable_mappings' => [
    'first_name' => ['source' => 'entity', 'field' => 'first_name'],
    'company_name' => ['source' => 'entity', 'field' => 'company.name'],
    'deal_value' => ['source' => 'entity', 'field' => 'deals.0.value'],
    'last_contact_date' => ['source' => 'entity', 'field' => 'last_contacted_at'],
]
```

#### Context Variables
Use runtime context data:
```php
'variable_mappings' => [
    'sender_name' => ['source' => 'context', 'field' => 'sender_name'],
    'campaign_name' => ['source' => 'context', 'field' => 'campaign_name'],
    'trigger_event' => ['source' => 'context', 'field' => 'trigger_event'],
]
```

#### Computed Variables
Dynamic calculations and smart content:
```php
'variable_mappings' => [
    'greeting' => ['source' => 'computed', 'computation' => 'time_of_day_greeting'],
    'days_since_contact' => ['source' => 'computed', 'computation' => 'days_since_last_contact'],
    'engagement_score' => ['source' => 'computed', 'computation' => 'engagement_score'],
    'total_deal_value' => ['source' => 'computed', 'computation' => 'total_deal_value'],
]
```

### 3. Conditional Content Rules

Create dynamic content blocks that adapt based on conditions:

```php
'conditional_content' => [
    [
        'placeholder' => '{{priority_message}}',
        'condition' => ['field' => 'deal_stage', 'operator' => 'equals', 'value' => 'closing'],
        'content' => 'I wanted to personally reach out as we approach the final decision phase.',
        'fallback' => 'I hope you\'re finding our solution valuable for your needs.',
    ],
    [
        'placeholder' => '{{urgency_indicator}}',
        'condition' => ['field' => 'days_since_contact', 'operator' => 'greater_than', 'value' => 14],
        'content' => '⚡ Important: ',
        'fallback' => '',
    ],
]
```

## Usage Examples

### 1. Generate Personalized Email Content

```php
use App\Models\DynamicContentTemplate;
use App\Models\Contact;

$template = DynamicContentTemplate::find(1);
$contact = Contact::with('company', 'deals')->find(1);

$personalizedContent = $template->generateContent($contact, [
    'sender_name' => auth()->user()->name,
    'campaign_name' => 'Q4 Follow-up Campaign',
    'trigger_event' => 'deal_stage_changed',
]);

echo $personalizedContent;
// Output: "Good morning John, I hope this email finds you well..."
```

### 2. Workflow Integration

```php
// In a workflow step or trigger action
$emailTemplate = DynamicContentTemplate::where('content_type', 'email_body')->first();
$subjectTemplate = DynamicContentTemplate::where('content_type', 'email_subject')->first();

$emailBody = $emailTemplate->generateContent($contact, $workflowContext);
$emailSubject = $subjectTemplate->generateContent($contact, $workflowContext);

Mail::to($contact->email)->send(new PersonalizedEmail($emailSubject, $emailBody));
```

### 3. SMS Personalization

```php
$smsTemplate = DynamicContentTemplate::create([
    'name' => 'Appointment Reminder SMS',
    'content_type' => 'sms_message',
    'base_template' => 'Hi {{first_name}}! Reminder: {{meeting_type}} tomorrow at {{meeting_time}}. {{location_info}} Reply CONFIRM or call {{phone_number}}.',
    'variable_mappings' => [
        'first_name' => ['source' => 'entity', 'field' => 'first_name'],
        'meeting_type' => ['source' => 'context', 'field' => 'meeting_type'],
        'meeting_time' => ['source' => 'context', 'field' => 'meeting_time'],
        'location_info' => ['source' => 'computed', 'computation' => 'meeting_location'],
        'phone_number' => ['source' => 'context', 'field' => 'company_phone'],
    ],
]);

$smsContent = $smsTemplate->generateContent($contact, [
    'meeting_type' => 'Demo call',
    'meeting_time' => '2:00 PM',
    'company_phone' => '+1-555-0123',
]);
```

### 4. Task Description Automation

```php
$taskTemplate = DynamicContentTemplate::create([
    'name' => 'Follow-up Task Template',
    'content_type' => 'task_description',
    'base_template' => 'Follow up with {{full_name}} at {{company_name}}. {{context_info}} {{priority_note}}',
    'conditional_content' => [
        [
            'placeholder' => '{{priority_note}}',
            'condition' => ['field' => 'deal_value', 'operator' => 'greater_than', 'value' => 50000],
            'content' => 'HIGH VALUE DEAL - Priority follow-up required.',
            'fallback' => 'Standard follow-up process.',
        ],
    ],
]);
```

## Available Computed Variables

### Time-Based Variables
- `time_of_day_greeting`: "Good morning", "Good afternoon", "Good evening"
- `days_since_last_contact`: Number of days since last interaction
- `business_days_since_contact`: Excluding weekends and holidays

### Engagement Variables
- `engagement_score`: Calculated engagement score (0-10)
- `interaction_frequency`: How often contact interacts
- `preferred_channel`: Email, phone, social media based on history

### Business Variables
- `total_deal_value`: Sum of all deal values for contact
- `deal_stage_duration`: Days in current deal stage
- `next_best_action`: AI-suggested next action

### Relationship Variables
- `relationship_strength`: Weak, moderate, strong based on interactions
- `influence_level`: Contact's influence in their organization
- `decision_maker_status`: Boolean indicating decision-making authority

## Performance Analytics

### Usage Tracking

Every template automatically tracks usage statistics:

```php
$template = DynamicContentTemplate::find(1);

// Get usage stats for last 30 days
$usageStats = $template->getUsageStats(30);
// Returns: ['2024-01-01' => 5, '2024-01-02' => 8, ...]

// Get total usage count
$totalUsage = $template->getTotalUsage();
```

### Performance Optimization

Monitor template performance and optimize based on data:

```php
// Get templates by usage frequency
$topTemplates = DynamicContentTemplate::active()
    ->get()
    ->sortByDesc(fn($t) => $t->getTotalUsage())
    ->take(10);

// Identify underperforming templates
$lowUsageTemplates = DynamicContentTemplate::active()
    ->get()
    ->filter(fn($t) => $t->getTotalUsage() < 10);
```

## Best Practices

### 1. Template Organization
- Use descriptive names that indicate purpose and channel
- Group related templates with consistent naming conventions
- Create template categories for different use cases

### 2. Variable Design
- Always provide fallback values for optional variables
- Test templates with contacts that have missing data
- Use default values in variable mappings when appropriate

### 3. Conditional Logic
- Keep conditions simple and logical
- Test all condition paths thoroughly
- Provide meaningful fallback content

### 4. Performance Optimization
- Monitor usage statistics regularly
- Update templates based on performance data
- Remove or consolidate unused templates

### 5. Content Quality
- Write natural, conversational content
- Avoid over-personalization that feels artificial
- Test templates with real contact data

## Advanced Features

### 1. Multi-Language Support

```php
$template = DynamicContentTemplate::create([
    'name' => 'Multi-Language Welcome',
    'base_template' => '{{localized_greeting}} {{first_name}}, {{localized_content}}',
    'variable_mappings' => [
        'localized_greeting' => ['source' => 'computed', 'computation' => 'localized_greeting'],
        'localized_content' => ['source' => 'computed', 'computation' => 'localized_content'],
    ],
]);
```

### 2. A/B Testing Integration

```php
// Templates can be used in A/B tests
$abTest = AbTest::create([
    'name' => 'Email Subject Line Test',
    'test_type' => 'email_subject',
    'variant_a' => ['template_id' => $template1->id],
    'variant_b' => ['template_id' => $template2->id],
]);
```

### 3. Machine Learning Enhancement

```php
// Use AI to optimize template variables
$optimizedTemplate = AiOptimizer::optimizeTemplate($template, [
    'goal' => 'increase_open_rate',
    'historical_data_days' => 90,
    'minimum_sample_size' => 100,
]);
```

## API Reference

### Create Template
```http
POST /api/dynamic-content-templates
Content-Type: application/json

{
    "name": "Welcome Email Template",
    "content_type": "email_body",
    "base_template": "Welcome {{first_name}}!",
    "variable_mappings": {
        "first_name": {"source": "entity", "field": "first_name"}
    }
}
```

### Generate Content
```http
POST /api/dynamic-content-templates/{id}/generate
Content-Type: application/json

{
    "entity_type": "Contact",
    "entity_id": 123,
    "context": {
        "sender_name": "John Doe"
    }
}
```

### Get Usage Analytics
```http
GET /api/dynamic-content-templates/{id}/analytics?days=30
```

## Troubleshooting

### Common Issues

**Issue**: Variables not being replaced
- **Solution**: Check variable mapping configuration and ensure entity has the required fields

**Issue**: Conditional content not working
- **Solution**: Verify condition syntax and test with different entity data

**Issue**: Performance issues with complex templates
- **Solution**: Simplify conditions, optimize variable mappings, use caching for computed variables

**Issue**: Templates not tracking usage
- **Solution**: Ensure `incrementUsageStatistics()` is called after content generation

### Debug Mode

Enable debug mode for template testing:

```php
$template->generateContent($contact, $context, ['debug' => true]);
// Returns detailed information about variable resolution and condition evaluation
```

---

Dynamic Content Templates transform static content into intelligent, adaptive messaging that scales personalization across your entire CRM operation. Combined with event triggers and workflow automation, they create a powerful foundation for customer engagement that adapts and improves over time.