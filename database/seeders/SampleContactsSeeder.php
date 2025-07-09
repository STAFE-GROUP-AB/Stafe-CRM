<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contact;
use App\Models\Company;
use App\Models\User;

class SampleContactsSeeder extends Seeder
{
    public function run(): void
    {
        // Create a default user if none exists
        $user = User::first() ?? User::create([
            'name' => 'Demo User',
            'email' => 'demo@stafe-crm.com',
            'password' => bcrypt('password'),
        ]);

        // Create sample companies first
        $companies = [
            [
                'name' => 'TechCorp Solutions',
                'industry' => 'technology',
                'employee_count' => 150,
                'website' => 'https://techcorp.com',
                'owner_id' => $user->id,
            ],
            [
                'name' => 'HealthFirst Medical',
                'industry' => 'healthcare',
                'employee_count' => 500,
                'website' => 'https://healthfirst.com',
                'owner_id' => $user->id,
            ],
            [
                'name' => 'RetailMax Inc',
                'industry' => 'retail',
                'employee_count' => 2000,
                'website' => 'https://retailmax.com',
                'owner_id' => $user->id,
            ],
            [
                'name' => 'StartupInc',
                'industry' => 'technology',
                'employee_count' => 25,
                'website' => 'https://startupinc.com',
                'owner_id' => $user->id,
            ],
        ];

        foreach ($companies as $companyData) {
            $company = Company::create($companyData);

            // Create 2-3 contacts per company
            $contacts = [
                [
                    'first_name' => 'John',
                    'last_name' => 'Smith',
                    'email' => 'john.smith@' . parse_url($company->website, PHP_URL_HOST),
                    'phone' => '+1-555-0101',
                    'title' => 'CEO',
                    'department' => 'Executive',
                    'source' => 'referral',
                    'owner_id' => $user->id,
                ],
                [
                    'first_name' => 'Sarah',
                    'last_name' => 'Johnson',
                    'email' => 'sarah.johnson@' . parse_url($company->website, PHP_URL_HOST),
                    'phone' => '+1-555-0102',
                    'title' => 'VP of Sales',
                    'department' => 'Sales',
                    'source' => 'organic_search',
                    'owner_id' => $user->id,
                ],
                [
                    'first_name' => 'Michael',
                    'last_name' => 'Brown',
                    'email' => 'michael.brown@' . parse_url($company->website, PHP_URL_HOST),
                    'phone' => '+1-555-0103',
                    'title' => 'IT Director',
                    'department' => 'Technology',
                    'source' => 'paid_search',
                    'owner_id' => $user->id,
                ],
            ];

            foreach ($contacts as $contactData) {
                $contactData['company_id'] = $company->id;
                Contact::create($contactData);
            }
        }

        // Create some contacts without companies
        $standaloneContacts = [
            [
                'first_name' => 'Emily',
                'last_name' => 'Davis',
                'email' => 'emily.davis@freelancer.com',
                'phone' => '+1-555-0201',
                'title' => 'Freelance Consultant',
                'source' => 'direct',
                'owner_id' => $user->id,
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Wilson',
                'email' => 'david.wilson@contractor.com',
                'phone' => '+1-555-0202',
                'title' => 'Independent Contractor',
                'source' => 'social_media',
                'owner_id' => $user->id,
            ],
        ];

        foreach ($standaloneContacts as $contactData) {
            Contact::create($contactData);
        }
    }
}
