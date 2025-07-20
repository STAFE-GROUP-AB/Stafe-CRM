<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Contact;
use App\Models\Company;
use App\Models\Deal;
use App\Models\Task;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        $this->command->info('Seeding demo data...');

        // Ensure pipeline stages exist before creating deals
        $this->call(PipelineStageSeeder::class);

        // Create demo users
        $this->createDemoUsers();
        
        // Create demo companies
        $this->createDemoCompanies();
        
        // Create demo contacts
        $this->createDemoContacts();
        
        // Create demo deals
        $this->createDemoDeals();
        
        // Create demo tasks
        $this->createDemoTasks();

        $this->command->info('Demo data seeded successfully!');
    }

    /**
     * Create demo users.
     */
    private function createDemoUsers(): void
    {
        $demoUsers = [
            [
                'name' => 'Sales Manager',
                'email' => 'sales@stafecrm.demo',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Marketing Lead',
                'email' => 'marketing@stafecrm.demo',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Customer Success',
                'email' => 'success@stafecrm.demo',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($demoUsers as $userData) {
            if (!User::where('email', $userData['email'])->exists()) {
                User::create($userData);
                $this->command->info("✓ Created demo user: {$userData['email']}");
            }
        }
    }

    /**
     * Create demo companies.
     */
    private function createDemoCompanies(): void
    {
        // Get all available users to assign as company owners
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->warn('No users found, skipping company creation');
            return;
        }

        $demoCompanies = [
            [
                'name' => 'Tech Solutions Inc.',
                'email' => 'contact@techsolutions.demo',
                'phone' => '+1-555-0101',
                'website' => 'https://techsolutions.demo',
                'industry' => 'Technology',
                'employee_count' => 150,
                'address' => '123 Tech Street, Silicon Valley, CA 94000',
            ],
            [
                'name' => 'Global Marketing Agency',
                'email' => 'hello@globalmarketing.demo',
                'phone' => '+1-555-0102',
                'website' => 'https://globalmarketing.demo',
                'industry' => 'Marketing',
                'employee_count' => 25,
                'address' => '456 Marketing Ave, New York, NY 10001',
            ],
            [
                'name' => 'Enterprise Corp',
                'email' => 'info@enterprise.demo',
                'phone' => '+1-555-0103',
                'website' => 'https://enterprise.demo',
                'industry' => 'Manufacturing',
                'employee_count' => 750,
                'address' => '789 Enterprise Blvd, Chicago, IL 60601',
            ],
        ];

        foreach ($demoCompanies as $index => $companyData) {
            if (!Company::where('email', $companyData['email'])->exists()) {
                // Assign a user as the company owner (cycle through available users)
                $companyData['owner_id'] = $users->get($index % $users->count())->id;
                
                Company::create($companyData);
                $this->command->info("✓ Created demo company: {$companyData['name']}");
            }
        }
    }

    /**
     * Create demo contacts.
     */
    private function createDemoContacts(): void
    {
        $companies = Company::all();
        $users = User::all();
        
        if ($companies->isEmpty()) {
            $this->command->warn('No companies found, skipping contact creation');
            return;
        }

        if ($users->isEmpty()) {
            $this->command->warn('No users found, skipping contact creation');
            return;
        }

        $demoContacts = [
            [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'john.smith@techsolutions.demo',
                'phone' => '+1-555-0201',
                'title' => 'CEO',
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah.johnson@globalmarketing.demo',
                'phone' => '+1-555-0202',
                'title' => 'Marketing Director',
            ],
            [
                'first_name' => 'Michael',
                'last_name' => 'Brown',
                'email' => 'michael.brown@enterprise.demo',
                'phone' => '+1-555-0203',
                'title' => 'CTO',
            ],
            [
                'first_name' => 'Emily',
                'last_name' => 'Davis',
                'email' => 'emily.davis@techsolutions.demo',
                'phone' => '+1-555-0204',
                'title' => 'Head of Sales',
            ],
        ];

        foreach ($demoContacts as $index => $contactData) {
            if (!Contact::where('email', $contactData['email'])->exists()) {
                $contactData['company_id'] = $companies->get($index % $companies->count())->id;
                // Assign a user as the contact owner (cycle through available users)
                $contactData['owner_id'] = $users->get($index % $users->count())->id;
                
                Contact::create($contactData);
                $this->command->info("✓ Created demo contact: {$contactData['first_name']} {$contactData['last_name']}");
            }
        }
    }

    /**
     * Create demo deals.
     */
    private function createDemoDeals(): void
    {
        $contacts = Contact::all();
        $users = User::all();
        $pipelineStages = \App\Models\PipelineStage::all();
        
        if ($contacts->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No contacts or users found, skipping deal creation');
            return;
        }

        if ($pipelineStages->isEmpty()) {
            $this->command->warn('No pipeline stages found, skipping deal creation');
            return;
        }

        $demoDeals = [
            [
                'name' => 'CRM Implementation Project',
                'value' => 25000.00,
                'status' => 'open',
                'probability' => 25,
                'expected_close_date' => now()->addDays(30),
                'description' => 'Implementation of CRM system for enterprise client',
            ],
            [
                'name' => 'Marketing Automation Setup',
                'value' => 15000.00,
                'status' => 'open',
                'probability' => 50,
                'expected_close_date' => now()->addDays(45),
                'description' => 'Setup and configuration of marketing automation tools',
            ],
            [
                'name' => 'Consulting Services',
                'value' => 50000.00,
                'status' => 'open',
                'probability' => 75,
                'expected_close_date' => now()->addDays(15),
                'description' => 'Strategic consulting for digital transformation',
            ],
        ];

        foreach ($demoDeals as $index => $dealData) {
            $dealData['contact_id'] = $contacts->get($index % $contacts->count())->id;
            $dealData['owner_id'] = $users->get($index % $users->count())->id;
            
            // Assign a pipeline stage based on probability
            if ($dealData['probability'] >= 75) {
                $stage = $pipelineStages->where('slug', 'negotiation')->first();
            } elseif ($dealData['probability'] >= 50) {
                $stage = $pipelineStages->where('slug', 'proposal')->first();
            } elseif ($dealData['probability'] >= 25) {
                $stage = $pipelineStages->where('slug', 'qualified')->first();
            } else {
                $stage = $pipelineStages->where('slug', 'lead')->first();
            }
            
            $dealData['pipeline_stage_id'] = $stage ? $stage->id : $pipelineStages->first()->id;
            
            // Generate slug from name
            $dealData['slug'] = \Illuminate\Support\Str::slug($dealData['name']);
            
            Deal::create($dealData);
            $this->command->info("✓ Created demo deal: {$dealData['name']}");
        }
    }

    /**
     * Create demo tasks.
     */
    private function createDemoTasks(): void
    {
        $users = User::all();
        $contacts = Contact::all();
        
        if ($users->isEmpty()) {
            $this->command->warn('No users found, skipping task creation');
            return;
        }

        $demoTasks = [
            [
                'title' => 'Follow up with Tech Solutions Inc.',
                'description' => 'Schedule a demo call to showcase the CRM features',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => now()->addDays(2),
            ],
            [
                'title' => 'Prepare proposal for Marketing Agency',
                'description' => 'Create detailed proposal including timeline and pricing',
                'status' => 'in_progress',
                'priority' => 'medium',
                'due_date' => now()->addDays(5),
            ],
            [
                'title' => 'Send contract to Enterprise Corp',
                'description' => 'Review and send final contract for signature',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => now()->addDays(1),
            ],
            [
                'title' => 'Weekly team meeting',
                'description' => 'Discuss progress on current deals and upcoming activities',
                'status' => 'completed',
                'priority' => 'low',
                'due_date' => now()->subDays(1),
            ],
        ];

        foreach ($demoTasks as $index => $taskData) {
            $taskData['assigned_to'] = $users->get($index % $users->count())->id;
            $taskData['created_by'] = $users->first()->id;
            
            if ($contacts->isNotEmpty()) {
                // Use polymorphic relationship
                $taskData['taskable_type'] = Contact::class;
                $taskData['taskable_id'] = $contacts->get($index % $contacts->count())->id;
            }
            
            Task::create($taskData);
            $this->command->info("✓ Created demo task: {$taskData['title']}");
        }
    }
}