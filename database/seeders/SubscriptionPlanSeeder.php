<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Standard per-user annual plan
        SubscriptionPlan::create([
            'name' => 'Professional',
            'slug' => 'professional',
            'description' => 'Full access to all CRM features for professional teams',
            'price' => 999.00,
            'currency' => 'SEK',
            'billing_cycle' => 'yearly',
            'max_users' => 1000,
            'features' => [
                'crm_core',
                'email_integration',
                'advanced_reporting',
                'team_collaboration',
                'workflow_automation',
                'ai_features',
                'sales_enablement',
                'customer_portal',
                'revenue_intelligence',
                'visual_analytics',
                'security_compliance',
                'api_access',
                'priority_support'
            ],
            'is_active' => true,
            'is_trial' => false,
        ]);

        // Trial plan
        SubscriptionPlan::create([
            'name' => 'Free Trial',
            'slug' => 'trial',
            'description' => '30-day free trial with full access to all features',
            'price' => 0.00,
            'currency' => 'SEK',
            'billing_cycle' => 'monthly',
            'max_users' => 5,
            'features' => [
                'crm_core',
                'email_integration',
                'advanced_reporting',
                'team_collaboration',
                'workflow_automation',
                'ai_features',
                'sales_enablement',
                'customer_portal',
                'revenue_intelligence',
                'visual_analytics',
                'security_compliance',
                'api_access',
                'trial_support'
            ],
            'is_active' => true,
            'is_trial' => true,
            'trial_days' => 30,
        ]);

        // Enterprise plan for large organizations
        SubscriptionPlan::create([
            'name' => 'Enterprise',
            'slug' => 'enterprise',
            'description' => 'For large organizations with custom requirements',
            'price' => 899.00, // Slightly discounted for volume
            'currency' => 'SEK',
            'billing_cycle' => 'yearly',
            'max_users' => 10000,
            'features' => [
                'crm_core',
                'email_integration',
                'advanced_reporting',
                'team_collaboration',
                'workflow_automation',
                'ai_features',
                'sales_enablement',
                'customer_portal',
                'revenue_intelligence',
                'visual_analytics',
                'security_compliance',
                'api_access',
                'priority_support',
                'sso_integration',
                'custom_integrations',
                'dedicated_support',
                'onboarding_assistance'
            ],
            'is_active' => true,
            'is_trial' => false,
        ]);
    }
}
