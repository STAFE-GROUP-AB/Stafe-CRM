# REST API Reference

Stafe CRM provides a comprehensive RESTful API that allows you to integrate with external systems, build custom applications, and automate your workflows. The API follows REST conventions and returns JSON responses.

## Authentication

### API Token Authentication

Generate an API token from your user settings and include it in the Authorization header:

```http
Authorization: Bearer your-api-token
```

### Creating API Tokens

```http
POST /api/auth/tokens
Content-Type: application/json

{
    "name": "My Application Token",
    "abilities": ["read", "write"],
    "expires_at": "2024-12-31"
}
```

## Base URL

```
https://your-domain.com/api
```

## Response Format

All API responses follow a consistent format:

### Success Response
```json
{
    "success": true,
    "data": {
        // Response data
    },
    "meta": {
        "pagination": {
            "current_page": 1,
            "per_page": 50,
            "total": 150
        }
    }
}
```

### Error Response
```json
{
    "success": false,
    "error": {
        "code": "VALIDATION_ERROR",
        "message": "The given data was invalid.",
        "details": {
            "email": ["The email field is required."]
        }
    }
}
```

## Core Entities

### Contacts

#### List Contacts
```http
GET /api/contacts
Parameters:
- page (int): Page number (default: 1)
- per_page (int): Items per page (default: 50, max: 100)
- search (string): Search term
- status (string): Filter by status
- tags (array): Filter by tags
- company_id (int): Filter by company
- assigned_to (int): Filter by assigned user
```

#### Get Contact
```http
GET /api/contacts/{id}
```

#### Create Contact
```http
POST /api/contacts
Content-Type: application/json

{
    "first_name": "John",
    "last_name": "Doe",
    "email": "john.doe@example.com",
    "phone": "+1234567890",
    "company_id": 123,
    "status": "lead",
    "source": "website",
    "assigned_to": 456,
    "tags": ["prospects", "enterprise"],
    "custom_fields": {
        "industry": "Technology",
        "budget": 50000
    }
}
```

#### Update Contact
```http
PUT /api/contacts/{id}
PATCH /api/contacts/{id}
Content-Type: application/json

{
    "status": "qualified",
    "notes": "Follow up next week"
}
```

#### Delete Contact
```http
DELETE /api/contacts/{id}
```

### Companies

#### List Companies
```http
GET /api/companies
Parameters:
- page, per_page, search (same as contacts)
- industry (string): Filter by industry
- size (string): Filter by company size
- annual_revenue_min (int): Minimum annual revenue
- annual_revenue_max (int): Maximum annual revenue
```

#### Create Company
```http
POST /api/companies
Content-Type: application/json

{
    "name": "Acme Corporation",
    "domain": "acme.com",
    "industry": "Technology",
    "employee_count": 500,
    "annual_revenue": 10000000,
    "phone": "+1234567890",
    "address": {
        "street": "123 Main St",
        "city": "San Francisco",
        "state": "CA",
        "postal_code": "94105",
        "country": "US"
    },
    "tags": ["enterprise", "saas"]
}
```

### Deals

#### List Deals
```http
GET /api/deals
Parameters:
- stage (string): Filter by pipeline stage
- value_min (float): Minimum deal value
- value_max (float): Maximum deal value
- close_date_from (date): Filter by close date range
- close_date_to (date): Filter by close date range
- assigned_to (int): Filter by assigned user
```

#### Create Deal
```http
POST /api/deals
Content-Type: application/json

{
    "title": "Enterprise Software License",
    "value": 50000,
    "currency": "USD",
    "stage": "proposal",
    "probability": 75,
    "expected_close_date": "2024-03-15",
    "contact_id": 123,
    "company_id": 456,
    "assigned_to": 789,
    "source": "referral",
    "tags": ["enterprise", "annual-contract"]
}
```

#### Update Deal Stage
```http
PATCH /api/deals/{id}/stage
Content-Type: application/json

{
    "stage": "negotiation",
    "notes": "Client requested discount"
}
```

### Tasks

#### List Tasks
```http
GET /api/tasks
Parameters:
- status (string): pending, completed, overdue
- assigned_to (int): Filter by assigned user
- due_date_from (date): Filter by due date range
- due_date_to (date): Filter by due date range
- priority (string): low, medium, high, urgent
- taskable_type (string): Contact, Company, Deal
- taskable_id (int): ID of related entity
```

#### Create Task
```http
POST /api/tasks
Content-Type: application/json

{
    "title": "Follow up call",
    "description": "Discuss pricing options",
    "due_date": "2024-01-20T14:00:00Z",
    "priority": "high",
    "assigned_to": 123,
    "taskable_type": "Contact",
    "taskable_id": 456,
    "estimated_duration": 30
}
```

#### Complete Task
```http
PATCH /api/tasks/{id}/complete
Content-Type: application/json

{
    "notes": "Task completed successfully",
    "actual_duration": 25
}
```

## Advanced Features

### Dynamic Content Templates

#### List Templates
```http
GET /api/dynamic-content-templates
Parameters:
- content_type (string): email_body, email_subject, sms_message
- is_active (boolean): Filter by active status
```

#### Generate Content
```http
POST /api/dynamic-content-templates/{id}/generate
Content-Type: application/json

{
    "entity_type": "Contact",
    "entity_id": 123,
    "context": {
        "sender_name": "John Sales",
        "campaign_name": "Q1 Outreach"
    }
}
```

Response:
```json
{
    "success": true,
    "data": {
        "generated_content": "Hi John, I hope this email finds you well...",
        "variables_used": {
            "first_name": "John",
            "company_name": "Acme Corp",
            "greeting": "Good morning"
        }
    }
}
```

### Event Triggers

#### List Event Triggers
```http
GET /api/event-triggers
Parameters:
- trigger_type (string): model_created, model_updated, field_changed
- is_active (boolean): Filter by active status
```

#### Create Event Trigger
```http
POST /api/event-triggers
Content-Type: application/json

{
    "name": "New Contact Welcome",
    "description": "Send welcome email to new contacts",
    "trigger_type": "model_created",
    "model_type": "App\\Models\\Contact",
    "trigger_conditions": [
        {
            "field": "source",
            "operator": "equals",
            "value": "website"
        }
    ],
    "action_configuration": {
        "actions": [
            {
                "type": "send_email",
                "template_id": 1,
                "recipient": "entity_email"
            }
        ]
    },
    "is_active": true
}
```

#### Execute Trigger Manually
```http
POST /api/event-triggers/{id}/execute
Content-Type: application/json

{
    "entity_type": "Contact",
    "entity_id": 123,
    "context": {
        "manual_trigger": true,
        "triggered_by": "admin"
    }
}
```

### A/B Tests

#### List A/B Tests
```http
GET /api/ab-tests
Parameters:
- status (string): draft, active, completed, cancelled
- test_type (string): email_subject, email_content, cadence_sequence
```

#### Create A/B Test
```http
POST /api/ab-tests
Content-Type: application/json

{
    "name": "Email Subject Line Test",
    "description": "Testing personalized vs generic subject lines",
    "test_type": "email_subject",
    "variant_a": {
        "subject": "Welcome to {{company_name}}!",
        "description": "Generic welcome"
    },
    "variant_b": {
        "subject": "{{first_name}}, your demo is ready!",
        "description": "Personalized with CTA"
    },
    "traffic_split": 0.5,
    "start_date": "2024-01-15",
    "end_date": "2024-01-29",
    "minimum_sample_size": 100,
    "success_metrics": [
        {
            "name": "open_rate",
            "type": "conversion",
            "description": "Email open rate"
        }
    ]
}
```

#### Record Conversion
```http
POST /api/ab-tests/{id}/conversions
Content-Type: application/json

{
    "participant_type": "Contact",
    "participant_id": 123,
    "metric_name": "open_rate",
    "value": 1,
    "metadata": {
        "email_id": 456,
        "open_time": "2024-01-16T10:30:00Z"
    }
}
```

#### Get Test Results
```http
GET /api/ab-tests/{id}/results
```

### Workflow Analytics

#### Record Metric
```http
POST /api/workflow-analytics/metrics
Content-Type: application/json

{
    "workflow_type": "workflow_template",
    "workflow_id": "123",
    "metric_name": "executions_count",
    "metric_value": 1,
    "metric_metadata": {
        "instance_id": 456,
        "success": true
    },
    "dimensions": {
        "template_name": "Lead Qualification",
        "status": "completed"
    }
}
```

#### Get Workflow Metrics
```http
GET /api/workflow-analytics/workflows/{type}/{id}/metrics
Parameters:
- metric (string): Metric name to retrieve
- days (int): Number of days to include (default: 30)
- period (string): Aggregation period (daily, weekly, monthly)
```

#### Generate Report
```http
POST /api/workflow-analytics/reports
Content-Type: application/json

{
    "report_type": "workflow_performance",
    "filters": {
        "workflow_types": ["workflow_template", "event_trigger"],
        "date_range": {
            "start": "2024-01-01",
            "end": "2024-01-31"
        }
    }
}
```

## Bulk Operations

### Bulk Create
```http
POST /api/contacts/bulk
Content-Type: application/json

{
    "contacts": [
        {
            "first_name": "John",
            "last_name": "Doe",
            "email": "john@example.com"
        },
        {
            "first_name": "Jane",
            "last_name": "Smith", 
            "email": "jane@example.com"
        }
    ]
}
```

### Bulk Update
```http
PATCH /api/contacts/bulk
Content-Type: application/json

{
    "filters": {
        "tags": ["prospects"],
        "status": "lead"
    },
    "updates": {
        "status": "qualified",
        "assigned_to": 123
    }
}
```

### Bulk Delete
```http
DELETE /api/contacts/bulk
Content-Type: application/json

{
    "ids": [1, 2, 3, 4, 5]
}
```

## File Uploads

### Upload File
```http
POST /api/files/upload
Content-Type: multipart/form-data

file: (binary)
entity_type: Contact
entity_id: 123
category: profile_image
```

### Get File
```http
GET /api/files/{id}
```

## Search & Filtering

### Global Search
```http
GET /api/search
Parameters:
- q (string): Search query
- types (array): Entity types to search (contacts, companies, deals)
- limit (int): Results per type (default: 10)
```

### Advanced Search
```http
POST /api/search/advanced
Content-Type: application/json

{
    "entity_type": "Contact",
    "conditions": [
        {
            "field": "status",
            "operator": "in",
            "value": ["lead", "qualified"]
        },
        {
            "field": "created_at",
            "operator": "gte",
            "value": "2024-01-01"
        }
    ],
    "sort": [
        {
            "field": "created_at",
            "direction": "desc"
        }
    ]
}
```

## Webhooks

### Register Webhook
```http
POST /api/webhooks
Content-Type: application/json

{
    "url": "https://your-app.com/webhook",
    "events": ["contact.created", "deal.updated", "task.completed"],
    "secret": "your-webhook-secret"
}
```

### List Webhooks
```http
GET /api/webhooks
```

### Test Webhook
```http
POST /api/webhooks/{id}/test
```

## Rate Limiting

API requests are rate limited to prevent abuse:

- **Standard users**: 1000 requests per hour
- **Premium users**: 5000 requests per hour
- **Enterprise users**: 10000 requests per hour

Rate limit headers are included in responses:
```http
X-RateLimit-Limit: 1000
X-RateLimit-Remaining: 999
X-RateLimit-Reset: 1640995200
```

## Error Codes

| Code | Description |
|------|-------------|
| 400 | Bad Request - Invalid syntax |
| 401 | Unauthorized - Invalid or missing token |
| 403 | Forbidden - Insufficient permissions |
| 404 | Not Found - Resource doesn't exist |
| 422 | Unprocessable Entity - Validation errors |
| 429 | Too Many Requests - Rate limit exceeded |
| 500 | Internal Server Error - Server error |

## SDKs & Libraries

### PHP SDK
```bash
composer require stafe/crm-php-sdk
```

```php
use Stafe\CRM\Client;

$client = new Client('your-api-token');
$contacts = $client->contacts()->list();
```

### JavaScript SDK
```bash
npm install @stafe/crm-js-sdk
```

```javascript
import StafeCRM from '@stafe/crm-js-sdk';

const crm = new StafeCRM('your-api-token');
const contacts = await crm.contacts.list();
```

### Python SDK
```bash
pip install stafe-crm-python
```

```python
from stafe_crm import StafeCRM

crm = StafeCRM('your-api-token')
contacts = crm.contacts.list()
```

## Examples

### Complete Integration Example

```javascript
// Initialize client
const crm = new StafeCRM('your-api-token');

// Create contact
const contact = await crm.contacts.create({
    first_name: 'John',
    last_name: 'Doe',
    email: 'john@example.com'
});

// Create deal
const deal = await crm.deals.create({
    title: 'Enterprise License',
    value: 50000,
    contact_id: contact.id,
    stage: 'proposal'
});

// Create follow-up task
const task = await crm.tasks.create({
    title: 'Follow up on proposal',
    due_date: '2024-01-20T14:00:00Z',
    taskable_type: 'Deal',
    taskable_id: deal.id
});

// Set up automation
const trigger = await crm.eventTriggers.create({
    name: 'Deal Stage Change Alert',
    trigger_type: 'field_changed',
    model_type: 'Deal',
    trigger_conditions: [{
        field: 'stage',
        operator: 'changed_to',
        value: 'won'
    }],
    action_configuration: {
        actions: [{
            type: 'send_email',
            template_id: 1,
            recipient: 'assigned_user'
        }]
    }
});
```

---

This API reference provides comprehensive coverage of Stafe CRM's REST API. For additional examples and detailed integration guides, visit our [API documentation website](https://api-docs.stafe-crm.com) or check the `/docs/api` folder for more specific integration guides.