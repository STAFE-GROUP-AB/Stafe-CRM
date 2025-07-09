# Phase 3 Usage Examples

This document provides practical examples of how to use the Phase 3 features in Stafe CRM.

## Advanced Automation Workflows

### Creating a Welcome Email Workflow

```php
use App\Services\WorkflowService;

$workflowService = new WorkflowService();

$workflow = $workflowService->createWorkflowTemplate([
    'name' => 'Welcome New Contacts',
    'description' => 'Send welcome email when new contact is created',
    'trigger_type' => 'event',
    'trigger_config' => [
        'event' => 'contact.created',
        'conditions' => [
            'email' => ['operator' => 'not_null']
        ]
    ],
    'steps' => [
        [
            'name' => 'Send Welcome Email',
            'type' => 'action',
            'config' => [
                'action_type' => 'send_email',
                'template_id' => 1,
                'delay_minutes' => 0
            ]
        ],
        [
            'name' => 'Create Follow-up Task',
            'type' => 'action',
            'config' => [
                'action_type' => 'create_task',
                'title' => 'Follow up with {{contact.name}}',
                'due_date' => '+3 days',
                'assigned_to' => 'contact.owner_id'
            ]
        ]
    ]
]);
```

### Triggering Workflows

```php
use App\Services\WorkflowService;

// When a contact is created
$contact = Contact::create([...]);

$workflowService = new WorkflowService();
$workflowService->executeWorkflows('event', $contact, [
    'event' => 'contact.created'
]);
```

## Integration Marketplace

### Installing and Configuring Mailchimp Integration

```php
use App\Services\IntegrationService;

$integrationService = new IntegrationService();

// Get Mailchimp integration
$mailchimp = Integration::where('slug', 'mailchimp')->first();

// Install for current user
$connection = $integrationService->installIntegration($mailchimp, auth()->user(), [
    'name' => 'My Mailchimp Account',
    'credentials' => [
        'api_key' => 'your-mailchimp-api-key',
        'server' => 'us1'
    ],
    'config' => [
        'list_id' => 'your-default-list-id',
        'sync_interval' => 60 // minutes
    ]
]);

// Test the connection
$result = $connection->testConnection();
if ($result['success']) {
    echo "Mailchimp integration successful!";
}
```

### Syncing Data

```php
// Sync all active connections for a user
$connections = auth()->user()->apiConnections()->active()->get();

foreach ($connections as $connection) {
    $result = $integrationService->syncConnection($connection);
    if ($result['success']) {
        echo "Synced {$connection->integration->name}: {$result['stats']['records_processed']} records";
    }
}
```

## Advanced Permissions & Roles

### Creating Custom Roles

```php
use App\Models\Role;
use App\Models\Permission;

// Create a custom role
$role = Role::create([
    'name' => 'Sales Manager',
    'slug' => 'sales-manager',
    'description' => 'Can manage sales team and view all deals',
    'is_system' => false
]);

// Assign permissions
$permissions = Permission::whereIn('slug', [
    'deals.view',
    'deals.create', 
    'deals.edit',
    'contacts.view',
    'contacts.create',
    'contacts.edit',
    'reports.view',
    'teams.view',
    'teams.members'
])->get();

$role->permissions()->attach($permissions);
```

### Assigning Roles to Users

```php
use App\Models\User;
use App\Models\Role;
use App\Models\Team;

$user = User::find(1);
$role = Role::where('slug', 'sales-manager')->first();
$team = Team::find(1);

// Assign global role
$user->assignRole($role);

// Assign team-specific role
$user->assignRole($role, $team);

// Check permissions
if ($user->hasPermission('deals.edit')) {
    echo "User can edit deals globally";
}

if ($user->hasPermission('deals.edit', $team)) {
    echo "User can edit deals in this team";
}
```

## Multi-Tenancy Support

### Creating and Managing Tenants

```php
use App\Models\Tenant;
use App\Models\User;

// Create a new tenant
$tenant = Tenant::create([
    'name' => 'Acme Corporation',
    'slug' => 'acme-corp',
    'subdomain' => 'acme',
    'status' => 'active',
    'max_users' => 50,
    'storage_limit' => 5120, // 5GB
    'features' => ['workflows', 'integrations', 'advanced_reporting']
]);

// Add user to tenant
$user = User::find(1);
$tenant->users()->attach($user, [
    'role' => 'owner',
    'is_active' => true,
    'joined_at' => now()
]);

// Set as user's current tenant
$user->update(['current_tenant_id' => $tenant->id]);
```

### Tenant-Aware Queries

```php
// When TenantContext middleware is active, all queries are automatically scoped
$companies = Company::all(); // Only returns companies for current tenant

// Manual tenant scoping
$tenant = Tenant::find(1);
$companies = Company::where('tenant_id', $tenant->id)->get();
```

### Tenant Settings and Features

```php
$tenant = Tenant::find(1);

// Manage settings
$tenant->setSetting('email.from_name', 'Acme Support');
$tenant->setSetting('branding.primary_color', '#007bff');

$fromName = $tenant->getSetting('email.from_name');

// Manage features
$tenant->enableFeature('advanced_workflows');
$tenant->disableFeature('api_access');

if ($tenant->hasFeature('integrations')) {
    echo "Tenant can use integrations";
}

// Check limits
if ($tenant->hasReachedUserLimit()) {
    echo "Cannot add more users to this tenant";
}
```

## Permission Checking in Controllers

```php
use App\Http\Controllers\Controller;

class DealController extends Controller
{
    public function index()
    {
        // Check permission
        if (!auth()->user()->hasPermission('deals.view')) {
            abort(403, 'Unauthorized');
        }

        // Get tenant-scoped deals (automatic with TenantContext middleware)
        $deals = Deal::paginate(20);
        
        return view('deals.index', compact('deals'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasPermission('deals.create')) {
            abort(403, 'Unauthorized');
        }

        $deal = Deal::create(array_merge($request->validated(), [
            'tenant_id' => app('current.tenant')->id ?? auth()->user()->current_tenant_id
        ]));

        // Trigger workflow
        app(WorkflowService::class)->executeWorkflows('event', $deal, [
            'event' => 'deal.created'
        ]);

        return redirect()->route('deals.show', $deal);
    }
}
```

## Webhook Handling

```php
use App\Services\IntegrationService;

class WebhookController extends Controller
{
    public function handle(Request $request, string $integrationSlug, string $endpoint)
    {
        $integration = Integration::where('slug', $integrationSlug)->firstOrFail();
        
        $result = app(IntegrationService::class)->processWebhook(
            $integration,
            $endpoint,
            $request->all()
        );

        return response()->json($result);
    }
}
```

These examples demonstrate the powerful capabilities of Phase 3 features and how they integrate seamlessly with the existing CRM functionality.