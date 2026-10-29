<?php

namespace Database\Seeders;

use App\Models\ScoringFactor;
use Illuminate\Database\Seeder;

class ScoringFactorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $factors = [
            // Engagement Factors
            [
                'name' => 'email_engagement',
                'display_name' => 'Email Engagement',
                'description' => 'Measures email open rates, click rates, and response patterns.',
                'category' => 'engagement',
                'weight' => 1.0,
                'calculation_method' => 'rule_based',
                'configuration' => [
                    'lookback_days' => 90,
                    'open_rate_weight' => 0.4,
                    'click_rate_weight' => 0.6,
                ],
                'is_active' => true,
                'sort_order' => 1,
                'data_source' => 'emails',
            ],
            [
                'name' => 'recency',
                'display_name' => 'Interaction Recency',
                'description' => 'How recently the contact has been engaged with.',
                'category' => 'engagement',
                'weight' => 0.8,
                'calculation_method' => 'rule_based',
                'configuration' => [
                    'hot_threshold_days' => 7,
                    'warm_threshold_days' => 30,
                    'cold_threshold_days' => 90,
                ],
                'is_active' => true,
                'sort_order' => 2,
                'data_source' => 'communications',
            ],
            [
                'name' => 'communication_frequency',
                'display_name' => 'Communication Frequency',
                'description' => 'Frequency of communications with the contact.',
                'category' => 'engagement',
                'weight' => 0.7,
                'calculation_method' => 'rule_based',
                'configuration' => [
                    'lookback_days' => 90,
                    'ideal_frequency' => 5,
                ],
                'is_active' => true,
                'sort_order' => 3,
                'data_source' => 'communications',
            ],

            // Firmographic Factors
            [
                'name' => 'company_size',
                'display_name' => 'Company Size',
                'description' => 'Score based on company employee count and alignment with ICP.',
                'category' => 'firmographic',
                'weight' => 1.0,
                'calculation_method' => 'rule_based',
                'configuration' => [
                    'size_ranges' => [
                        '1-10' => 40,
                        '11-50' => 60,
                        '51-200' => 80,
                        '201-1000' => 100,
                        '1001-5000' => 90,
                        '5000+' => 85,
                    ],
                ],
                'is_active' => true,
                'sort_order' => 4,
                'data_source' => 'companies',
            ],
            [
                'name' => 'industry_match',
                'display_name' => 'Industry Match',
                'description' => 'How well the company industry matches target industries.',
                'category' => 'firmographic',
                'weight' => 1.0,
                'calculation_method' => 'rule_based',
                'configuration' => [
                    'target_industries' => [
                        'technology' => 100,
                        'software' => 100,
                        'saas' => 100,
                        'fintech' => 95,
                        'finance' => 90,
                        'banking' => 85,
                        'healthcare' => 80,
                        'e-commerce' => 80,
                        'retail' => 70,
                        'manufacturing' => 65,
                        'consulting' => 75,
                        'marketing' => 70,
                        'education' => 60,
                    ],
                ],
                'is_active' => true,
                'sort_order' => 5,
                'data_source' => 'companies',
            ],

            // Behavioral Factors
            [
                'name' => 'lead_source_quality',
                'display_name' => 'Lead Source Quality',
                'description' => 'Quality score based on how the lead was acquired.',
                'category' => 'behavioral',
                'weight' => 0.9,
                'calculation_method' => 'rule_based',
                'configuration' => [
                    'source_scores' => [
                        'demo_request' => 100,
                        'free_trial' => 95,
                        'contact_form' => 85,
                        'webinar' => 80,
                        'content_download' => 70,
                        'referral' => 90,
                        'partner' => 85,
                        'paid_ad' => 60,
                        'organic' => 50,
                        'social_media' => 45,
                        'cold_outreach' => 30,
                        'purchased_list' => 20,
                    ],
                ],
                'is_active' => true,
                'sort_order' => 6,
                'data_source' => 'contacts',
            ],
            [
                'name' => 'website_activity',
                'display_name' => 'Website Activity',
                'description' => 'Engagement with website content and key pages.',
                'category' => 'behavioral',
                'weight' => 0.8,
                'calculation_method' => 'rule_based',
                'configuration' => [
                    'high_intent_pages' => ['pricing', 'demo', 'contact', 'trial'],
                    'lookback_days' => 30,
                ],
                'is_active' => true,
                'sort_order' => 7,
                'data_source' => 'activity_logs',
            ],
            [
                'name' => 'deal_history',
                'display_name' => 'Deal History',
                'description' => 'Historical deal activity and outcomes.',
                'category' => 'behavioral',
                'weight' => 1.0,
                'calculation_method' => 'rule_based',
                'configuration' => [
                    'won_deal_bonus' => 15,
                    'open_deal_bonus' => 10,
                    'lost_deal_penalty' => 5,
                ],
                'is_active' => true,
                'sort_order' => 8,
                'data_source' => 'deals',
            ],

            // Demographic Factors
            [
                'name' => 'title_seniority',
                'display_name' => 'Title Seniority',
                'description' => 'Decision-making authority based on job title.',
                'category' => 'demographic',
                'weight' => 0.9,
                'calculation_method' => 'rule_based',
                'configuration' => [
                    'seniority_scores' => [
                        'c-level' => 100,
                        'vp' => 90,
                        'director' => 80,
                        'manager' => 70,
                        'senior' => 60,
                        'individual' => 40,
                    ],
                    'decision_departments' => ['executive', 'sales', 'marketing', 'procurement'],
                ],
                'is_active' => true,
                'sort_order' => 9,
                'data_source' => 'contacts',
            ],
            [
                'name' => 'data_completeness',
                'display_name' => 'Data Completeness',
                'description' => 'How complete the contact profile is.',
                'category' => 'demographic',
                'weight' => 0.5,
                'calculation_method' => 'rule_based',
                'configuration' => [
                    'required_fields' => [
                        'email' => 15,
                        'phone' => 10,
                        'title' => 15,
                        'company_id' => 20,
                    ],
                    'optional_fields' => [
                        'department' => 10,
                        'city' => 5,
                        'country' => 5,
                        'source' => 10,
                        'first_name' => 5,
                        'last_name' => 5,
                    ],
                ],
                'is_active' => true,
                'sort_order' => 10,
                'data_source' => 'contacts',
            ],
        ];

        foreach ($factors as $factor) {
            ScoringFactor::updateOrCreate(
                ['name' => $factor['name']],
                $factor
            );
        }

        $this->command->info('Scoring factors seeded successfully!');
    }
}
