@extends('landing.layout')

@section('title', 'Pricing - Stafe CRM')
@section('description', 'Simple, transparent pricing for Stafe CRM. 999 SEK per user per year with all features included. Start your free trial today.')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-blue-50 to-purple-50 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
            Simple, Transparent Pricing
        </h1>
        <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
            No hidden fees, no complex tiers. Just powerful CRM features at a price that scales with your team.
        </p>
    </div>
</section>

<!-- Pricing Plans -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($plans as $plan)
                <div class="bg-white rounded-xl shadow-lg p-8 border-2 {{ $plan->slug === 'professional' ? 'border-blue-600 scale-105' : 'border-gray-200' }}">
                    @if($plan->slug === 'professional')
                        <div class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold inline-block mb-4">
                            Most Popular
                        </div>
                    @endif
                    
                    <div class="text-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $plan->name }}</h3>
                        <p class="text-gray-600">{{ $plan->description }}</p>
                    </div>
                    
                    <div class="text-center mb-6">
                        <span class="text-5xl font-bold text-gray-900">{{ number_format($plan->price, 0, ',', ' ') }}</span>
                        <span class="text-xl text-gray-600">{{ $plan->currency }}</span>
                        <div class="text-gray-600">
                            per user per {{ $plan->billing_cycle === 'yearly' ? 'year' : 'month' }}
                        </div>
                        @if($plan->billing_cycle === 'yearly' && $plan->price > 0)
                            <div class="text-sm text-gray-500 mt-1">
                                ({{ number_format($plan->monthly_price, 0, ',', ' ') }} SEK/month)
                            </div>
                        @endif
                    </div>
                    
                    <ul class="space-y-3 mb-8">
                        @php
                            $features = [
                                'crm_core' => 'Complete CRM functionality',
                                'email_integration' => 'Email integration & tracking',
                                'advanced_reporting' => 'Advanced reporting & analytics',
                                'team_collaboration' => 'Team collaboration tools',
                                'workflow_automation' => 'Workflow automation',
                                'ai_features' => 'AI-powered insights',
                                'sales_enablement' => 'Sales enablement suite',
                                'customer_portal' => 'Customer portal',
                                'revenue_intelligence' => 'Revenue intelligence',
                                'visual_analytics' => 'Visual analytics',
                                'security_compliance' => 'Security & compliance',
                                'api_access' => 'API access',
                                'priority_support' => 'Priority support',
                                'sso_integration' => 'SSO integration',
                                'custom_integrations' => 'Custom integrations',
                                'dedicated_support' => 'Dedicated support',
                                'onboarding_assistance' => 'Onboarding assistance',
                                'trial_support' => 'Trial support'
                            ];
                        @endphp
                        
                        @foreach($plan->features as $feature)
                            @if(isset($features[$feature]))
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-gray-700">{{ $features[$feature] }}</span>
                                </li>
                            @endif
                        @endforeach
                        
                        @if($plan->is_trial)
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-700">{{ $plan->trial_days }} days free trial</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-700">Up to {{ $plan->max_users }} users</span>
                            </li>
                        @endif
                    </ul>
                    
                    <a href="{{ route('dashboard') }}" class="w-full {{ $plan->slug === 'professional' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-900 hover:bg-gray-800' }} text-white py-3 rounded-lg font-semibold transition-colors inline-block text-center">
                        {{ $plan->is_trial ? 'Start Free Trial' : 'Get Started' }}
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">
                Frequently Asked Questions
            </h2>
            <p class="text-xl text-gray-600">
                Got questions? We've got answers.
            </p>
        </div>
        
        <div class="space-y-8">
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    Do I need to provide my own AI keys?
                </h3>
                <p class="text-gray-600">
                    Yes, you'll need to provide your own API keys for AI services like OpenAI, Claude, or others. This ensures you have full control over your AI usage and costs, and we don't mark up AI service pricing.
                </p>
            </div>
            
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    Can I cancel my subscription at any time?
                </h3>
                <p class="text-gray-600">
                    Yes, you can cancel your subscription at any time. Your subscription will remain active until the end of your current billing period, and you'll continue to have access to all features until then.
                </p>
            </div>
            
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    What happens during the free trial?
                </h3>
                <p class="text-gray-600">
                    During your 30-day free trial, you'll have access to all features with up to 5 users. No credit card required to start. You can upgrade to a paid plan at any time during or after the trial.
                </p>
            </div>
            
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    How do you handle data security?
                </h3>
                <p class="text-gray-600">
                    We take security seriously with enterprise-grade encryption, GDPR compliance, advanced audit trails, and optional SSO integration. Your data is isolated per tenant and stored securely.
                </p>
            </div>
            
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    Can I add or remove users anytime?
                </h3>
                <p class="text-gray-600">
                    Yes, you can add or remove users from your account at any time. Your billing will be prorated based on the number of active users in your subscription.
                </p>
            </div>
            
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    Do you offer enterprise discounts?
                </h3>
                <p class="text-gray-600">
                    Yes, we offer volume discounts for large organizations. Our Enterprise plan includes additional features and dedicated support. Contact us for custom pricing.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-blue-600 to-purple-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
            Ready to Get Started?
        </h2>
        <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
            Start your free trial today and see why thousands of teams choose Stafe CRM.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('dashboard') }}" class="bg-white text-blue-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition-colors">
                Start Free Trial
            </a>
            <a href="{{ route('landing.contact') }}" class="bg-transparent text-white px-8 py-4 rounded-lg text-lg font-semibold border border-white hover:bg-white hover:text-blue-600 transition-colors">
                Contact Sales
            </a>
        </div>
    </div>
</section>
@endsection