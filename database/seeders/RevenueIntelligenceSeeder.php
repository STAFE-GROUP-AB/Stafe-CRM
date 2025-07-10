<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\PipelineStage;
use App\Models\DealRiskAnalysis;
use App\Models\CompetitiveIntelligence;
use App\Models\PriceOptimization;
use App\Models\TerritoryPerformance;
use App\Models\CommissionTracking;
use App\Models\SalesCoaching;

class RevenueIntelligenceSeeder extends Seeder
{
    public function run(): void
    {
        // Create test users (or use existing ones)
        $user1 = User::firstOrCreate(
            ['email' => 'john@example.com'],
            [
                'name' => 'Sales Rep John',
                'password' => bcrypt('password'),
            ]
        );

        $user2 = User::firstOrCreate(
            ['email' => 'jane@example.com'],
            [
                'name' => 'Sales Rep Jane',
                'password' => bcrypt('password'),
            ]
        );

        // Create test companies (or use existing ones)
        $company1 = Company::firstOrCreate(
            ['name' => 'Acme Corporation'],
            [
                'industry' => 'Technology',
                'employee_count' => 250,
                'annual_revenue' => 5000000,
                'phone' => '555-0123',
                'email' => 'contact@acme.com',
                'website' => 'https://acme.com',
                'owner_id' => $user1->id,
            ]
        );

        $company2 = Company::firstOrCreate(
            ['name' => 'Global Solutions Inc'],
            [
                'industry' => 'Manufacturing',
                'employee_count' => 1000,
                'annual_revenue' => 25000000,
                'phone' => '555-0456',
                'email' => 'info@globalsolutions.com',
                'website' => 'https://globalsolutions.com',
                'owner_id' => $user2->id,
            ]
        );

        // Create test contacts (or use existing ones)
        $contact1 = Contact::firstOrCreate(
            ['email' => 'michael.smith@acme.com'],
            [
                'company_id' => $company1->id,
                'first_name' => 'Michael',
                'last_name' => 'Smith',
                'title' => 'IT Director',
                'phone' => '555-0789',
                'owner_id' => $user1->id,
            ]
        );

        $contact2 = Contact::firstOrCreate(
            ['email' => 'sarah.johnson@globalsolutions.com'],
            [
                'company_id' => $company2->id,
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'title' => 'Chief Technology Officer',
                'phone' => '555-0321',
                'owner_id' => $user2->id,
            ]
        );

        // Create pipeline stages (or use existing ones)
        $stage1 = PipelineStage::firstOrCreate(
            ['name' => 'Qualification'],
            [
                'slug' => 'qualification',
                'default_probability' => 20,
                'order' => 1,
                'color' => '#3B82F6',
            ]
        );

        $stage2 = PipelineStage::firstOrCreate(
            ['name' => 'Proposal'],
            [
                'slug' => 'proposal',
                'default_probability' => 70,
                'order' => 2,
                'color' => '#F59E0B',
            ]
        );

        // Create test deals (or use existing ones)
        $deal1 = Deal::firstOrCreate(
            ['name' => 'Acme CRM Implementation'],
            [
                'slug' => 'acme-crm-implementation',
                'company_id' => $company1->id,
                'contact_id' => $contact1->id,
                'owner_id' => $user1->id,
                'pipeline_stage_id' => $stage1->id,
                'value' => 75000,
                'expected_close_date' => now()->addDays(45),
                'source' => 'Website',
                'type' => 'New Business',
                'description' => 'Implementation of CRM system for Acme Corporation',
            ]
        );

        $deal2 = Deal::firstOrCreate(
            ['name' => 'Global Solutions Enterprise Package'],
            [
                'slug' => 'global-solutions-enterprise-package',
                'company_id' => $company2->id,
                'contact_id' => $contact2->id,
                'owner_id' => $user2->id,
                'pipeline_stage_id' => $stage2->id,
                'value' => 250000,
                'expected_close_date' => now()->addDays(15),
                'source' => 'Referral',
                'type' => 'Expansion',
                'description' => 'Enterprise CRM package for Global Solutions',
            ]
        );

        // Create deal risk analyses
        DealRiskAnalysis::create([
            'deal_id' => $deal1->id,
            'risk_score' => 75,
            'risk_level' => 'high',
            'risk_factors' => [
                'overdue' => [
                    'weight' => 0.3,
                    'value' => 5,
                    'description' => 'Deal is overdue by 5 days'
                ],
                'low_activity' => [
                    'weight' => 0.25,
                    'value' => 1,
                    'description' => 'Low recent activity: 1 task in last 7 days'
                ]
            ],
            'intervention_recommendations' => [
                [
                    'action' => 'Schedule immediate follow-up',
                    'priority' => 'high',
                    'description' => 'Contact prospect to understand delays and set new timeline'
                ]
            ],
            'probability_to_close' => 0.35,
            'predicted_close_date' => now()->addDays(60),
            'confidence_score' => 0.85,
            'model_version' => '1.0',
            'last_analyzed_at' => now(),
        ]);

        DealRiskAnalysis::create([
            'deal_id' => $deal2->id,
            'risk_score' => 25,
            'risk_level' => 'low',
            'risk_factors' => [
                'high_value' => [
                    'weight' => 0.15,
                    'value' => 250000,
                    'description' => 'High-value deal requires extra attention'
                ]
            ],
            'intervention_recommendations' => [],
            'probability_to_close' => 0.85,
            'predicted_close_date' => now()->addDays(20),
            'confidence_score' => 0.92,
            'model_version' => '1.0',
            'last_analyzed_at' => now(),
        ]);

        // Create competitive intelligence
        CompetitiveIntelligence::create([
            'deal_id' => $deal1->id,
            'competitor_name' => 'Salesforce',
            'competitor_strength' => [
                'Brand Recognition' => 'Market leader with strong brand presence',
                'Feature Completeness' => 'Comprehensive platform with extensive features'
            ],
            'competitor_weaknesses' => [
                'Complexity' => 'Steep learning curve and complex implementation',
                'Cost' => 'Expensive, especially for smaller organizations'
            ],
            'win_loss_probability' => 0.45,
            'competitive_factors' => [
                'smb_deal' => [
                    'weight' => 0.3,
                    'advantage' => 'us',
                    'description' => 'Small/medium business deals favor user-friendly solutions'
                ]
            ],
            'battle_card_recommendations' => [
                'counter_complexity' => [
                    'competitor_claim' => 'Comprehensive feature set',
                    'our_response' => 'Our focused feature set reduces complexity and improves user adoption',
                    'supporting_evidence' => ['customer_testimonials', 'feature_comparison']
                ]
            ],
            'pricing_comparison' => [
                'competitor_price_range' => 'Higher than our offering',
                'our_price_advantage' => 'Competitive pricing with better ROI'
            ],
            'feature_comparison' => [
                'unique_features' => ['AI-powered insights', 'Advanced automation'],
                'parity_features' => ['Contact management', 'Deal tracking']
            ],
            'source' => 'ai_analysis',
            'confidence_score' => 0.80,
            'last_updated_at' => now(),
        ]);

        // Create price optimizations
        PriceOptimization::create([
            'deal_id' => $deal1->id,
            'contact_id' => $contact1->id,
            'company_id' => $company1->id,
            'suggested_price' => 68000,
            'confidence_score' => 0.87,
            'price_factors' => [
                'company_size' => [
                    'weight' => 0.25,
                    'value' => 250,
                    'impact' => 'decrease',
                    'description' => 'Company size: 250 employees'
                ],
                'competition' => [
                    'weight' => 0.1,
                    'value' => 2,
                    'impact' => 'competitive_pricing',
                    'description' => 'Competitors in deal: 2'
                ]
            ],
            'market_analysis' => [
                'market_segment' => 'mid_market',
                'price_sensitivity' => 'medium',
                'market_trends' => ['trend_direction' => 'stable']
            ],
            'discount_recommendations' => [
                [
                    'type' => 'competitive_discount',
                    'percentage' => 9.3,
                    'amount' => 7000,
                    'justification' => 'Market analysis suggests competitive pricing needed'
                ]
            ],
            'pricing_strategy' => 'competitive_aggressive',
            'margin_analysis' => [
                'suggested_margin' => 40.5,
                'margin_amount' => 27520
            ],
            'model_version' => '1.0',
            'last_calculated_at' => now(),
        ]);

        // Create territory performance
        TerritoryPerformance::create([
            'user_id' => $user1->id,
            'territory_name' => 'West Coast',
            'territory_description' => 'California, Oregon, Washington',
            'performance_score' => 85.5,
            'revenue_target' => 500000,
            'revenue_actual' => 425000,
            'revenue_percentage' => 85.0,
            'deal_count_target' => 20,
            'deal_count_actual' => 17,
            'activity_score' => 92.3,
            'optimization_recommendations' => [
                'Focus on larger enterprise deals',
                'Increase follow-up frequency with prospects'
            ],
            'market_potential' => [
                'total_addressable_market' => 2500000,
                'penetration_rate' => 0.17
            ],
            'competition_density' => 0.75,
            'territory_balance_score' => 88.2,
            'period_start' => now()->startOfQuarter(),
            'period_end' => now()->endOfQuarter(),
            'last_calculated_at' => now(),
        ]);

        TerritoryPerformance::create([
            'user_id' => $user2->id,
            'territory_name' => 'East Coast',
            'territory_description' => 'New York, New Jersey, Connecticut',
            'performance_score' => 112.3,
            'revenue_target' => 600000,
            'revenue_actual' => 674000,
            'revenue_percentage' => 112.3,
            'deal_count_target' => 15,
            'deal_count_actual' => 18,
            'activity_score' => 95.7,
            'optimization_recommendations' => [
                'Maintain current momentum',
                'Share best practices with other territories'
            ],
            'market_potential' => [
                'total_addressable_market' => 3200000,
                'penetration_rate' => 0.21
            ],
            'competition_density' => 0.85,
            'territory_balance_score' => 95.1,
            'period_start' => now()->startOfQuarter(),
            'period_end' => now()->endOfQuarter(),
            'last_calculated_at' => now(),
        ]);

        // Create commission tracking
        CommissionTracking::create([
            'user_id' => $user1->id,
            'deal_id' => $deal1->id,
            'commission_amount' => 3750,
            'commission_rate' => 0.05,
            'commission_type' => 'percentage',
            'base_amount' => 3750,
            'bonus_amount' => 0,
            'calculation_rules' => [
                'base_rate' => 0.05,
                'deal_size_multiplier' => 1.0
            ],
            'payment_status' => 'pending',
            'payment_period_start' => now()->startOfMonth(),
            'payment_period_end' => now()->endOfMonth(),
            'last_calculated_at' => now(),
        ]);

        CommissionTracking::create([
            'user_id' => $user2->id,
            'deal_id' => $deal2->id,
            'commission_amount' => 15000,
            'commission_rate' => 0.06,
            'commission_type' => 'percentage',
            'base_amount' => 12500,
            'bonus_amount' => 2500,
            'calculation_rules' => [
                'base_rate' => 0.05,
                'bonus_threshold' => 200000,
                'bonus_rate' => 0.01
            ],
            'payment_status' => 'approved',
            'payment_period_start' => now()->startOfMonth(),
            'payment_period_end' => now()->endOfMonth(),
            'approved_by' => $user1->id,
            'approved_at' => now()->subDays(2),
            'last_calculated_at' => now(),
        ]);

        // Create sales coaching
        SalesCoaching::create([
            'user_id' => $user1->id,
            'deal_id' => $deal1->id,
            'coaching_type' => 'deal_strategy',
            'priority_level' => 'high',
            'recommendations' => [
                'Focus on demonstrating ROI and cost savings to justify investment',
                'Schedule a meeting with the decision-maker to address concerns',
                'Prepare competitive comparison showing our advantages over Salesforce'
            ],
            'skill_gaps' => [
                'objection_handling' => 'Needs improvement in handling price objections',
                'value_selling' => 'Could better articulate business value proposition'
            ],
            'performance_metrics' => [
                'deal_velocity' => 'Below average for this deal size',
                'activity_level' => 'Adequate but could be higher'
            ],
            'action_items' => [
                'Complete objection handling training module',
                'Practice value-selling scenarios',
                'Schedule daily check-ins with prospect'
            ],
            'suggested_resources' => [
                'Objection Handling Best Practices Guide',
                'ROI Calculator Tool',
                'Competitive Battle Cards'
            ],
            'coaching_score' => 72.5,
            'implementation_status' => 'pending',
            'follow_up_date' => now()->addDays(7),
            'model_version' => '1.0',
            'last_generated_at' => now(),
        ]);

        SalesCoaching::create([
            'user_id' => $user2->id,
            'deal_id' => $deal2->id,
            'coaching_type' => 'closing',
            'priority_level' => 'medium',
            'recommendations' => [
                'Deal is progressing well, prepare closing materials',
                'Confirm all stakeholders are aligned on the solution',
                'Address any final technical questions promptly'
            ],
            'skill_gaps' => [],
            'performance_metrics' => [
                'deal_velocity' => 'Above average',
                'activity_level' => 'Excellent'
            ],
            'action_items' => [
                'Prepare final proposal and contract',
                'Schedule closing call with all stakeholders',
                'Follow up on legal review process'
            ],
            'suggested_resources' => [
                'Closing Techniques Handbook',
                'Contract Templates',
                'Legal Review Checklist'
            ],
            'coaching_score' => 89.3,
            'implementation_status' => 'in_progress',
            'follow_up_date' => now()->addDays(3),
            'model_version' => '1.0',
            'last_generated_at' => now(),
        ]);
    }
}
