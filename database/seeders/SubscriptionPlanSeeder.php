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
        $plans = [
            [
                'name' => 'Free Trial',
                'slug' => 'trial',
                'description' => '14-day free trial with full access to all features',
                'price' => 0.00,
                'currency' => 'USD',
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
                'trial_days' => 14,
            ],
            [
                'name' => 'Stafe Cloud',
                'slug' => 'stafe-cloud',
                'description' => 'Bring your own AI keys - Full access to all CRM features',
                'price' => 99.00,
                'currency' => 'USD',
                'billing_cycle' => 'monthly',
                'max_users' => null, // Unlimited
                'features' => [
                    'crm_core',
                    'email_integration',
                    'advanced_reporting',
                    'team_collaboration',
                    'workflow_automation',
                    'ai_features',
                    'bring_own_ai_keys',
                    'sales_enablement',
                    'customer_portal',
                    'revenue_intelligence',
                    'visual_analytics',
                    'security_compliance',
                    'api_access',
                    'priority_support',
                    'automatic_updates',
                    'daily_backups'
                ],
                'is_active' => true,
                'is_trial' => false,
            ],
            [
                'name' => 'Stafe Cloud Pro',
                'slug' => 'stafe-cloud-pro',
                'description' => 'Includes AI credits - No AI setup needed',
                'price' => 299.00,
                'currency' => 'USD',
                'billing_cycle' => 'monthly',
                'max_users' => null, // Unlimited
                'features' => [
                    'crm_core',
                    'email_integration',
                    'advanced_reporting',
                    'team_collaboration',
                    'workflow_automation',
                    'ai_features',
                    'ai_credits_included',
                    'sales_enablement',
                    'customer_portal',
                    'revenue_intelligence',
                    'visual_analytics',
                    'security_compliance',
                    'api_access',
                    'priority_support',
                    'automatic_updates',
                    'daily_backups'
                ],
                'is_active' => true,
                'is_trial' => false,
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'Full access to all CRM features for professional teams (Legacy)',
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
                'is_active' => false, // Mark as inactive legacy plan
                'is_trial' => false,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'For large organizations with custom requirements (Legacy)',
                'price' => 899.00,
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
                'is_active' => false, // Mark as inactive legacy plan
                'is_trial' => false,
            ],
        ];

        foreach ($plans as $plan) {
            if (!SubscriptionPlan::where('slug', $plan['slug'])->exists()) {
                SubscriptionPlan::create($plan);
            }
        }
    }
}
