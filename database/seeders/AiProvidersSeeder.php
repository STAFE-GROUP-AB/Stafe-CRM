<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AiProvider;
use App\Models\AiModel;
use App\Models\ScoringFactor;

class AiProvidersSeeder extends Seeder
{
    public function run(): void
    {
        // Create AI Providers
        $openai = AiProvider::create([
            'name' => 'OpenAI',
            'slug' => 'openai',
            'description' => 'Leading AI platform with GPT models for text generation, analysis, and conversation intelligence.',
            'api_base_url' => 'https://api.openai.com/v1',
            'supported_features' => [
                'text_generation',
                'conversation_intelligence', 
                'lead_scoring',
                'sentiment_analysis',
                'content_analysis'
            ],
            'authentication_methods' => ['api_key'],
            'configuration_schema' => [
                'api_key' => [
                    'type' => 'string',
                    'required' => true,
                    'description' => 'OpenAI API Key'
                ],
                'organization' => [
                    'type' => 'string',
                    'required' => false,
                    'description' => 'Organization ID (optional)'
                ]
            ],
            'status' => 'active',
            'logo_url' => 'https://openai.com/favicon.ico',
            'rate_limits' => [
                'requests_per_minute' => 3000,
                'tokens_per_minute' => 250000
            ]
        ]);

        $anthropic = AiProvider::create([
            'name' => 'Anthropic',
            'slug' => 'anthropic',
            'description' => 'AI safety company with Claude models for advanced reasoning and analysis.',
            'api_base_url' => 'https://api.anthropic.com/v1',
            'supported_features' => [
                'text_generation',
                'conversation_intelligence',
                'lead_scoring',
                'sentiment_analysis',
                'content_analysis'
            ],
            'authentication_methods' => ['api_key'],
            'configuration_schema' => [
                'api_key' => [
                    'type' => 'string',
                    'required' => true,
                    'description' => 'Anthropic API Key'
                ]
            ],
            'status' => 'active',
            'logo_url' => 'https://www.anthropic.com/favicon.ico',
            'rate_limits' => [
                'requests_per_minute' => 1000,
                'tokens_per_minute' => 100000
            ]
        ]);

        $google = AiProvider::create([
            'name' => 'Google AI',
            'slug' => 'google',
            'description' => 'Google\'s AI platform with Gemini models for multimodal intelligence.',
            'api_base_url' => 'https://generativelanguage.googleapis.com/v1beta',
            'supported_features' => [
                'text_generation',
                'conversation_intelligence',
                'lead_scoring',
                'sentiment_analysis'
            ],
            'authentication_methods' => ['api_key'],
            'configuration_schema' => [
                'api_key' => [
                    'type' => 'string',
                    'required' => true,
                    'description' => 'Google AI API Key'
                ]
            ],
            'status' => 'active',
            'logo_url' => 'https://ai.google/favicon.ico',
            'rate_limits' => [
                'requests_per_minute' => 1500,
                'tokens_per_minute' => 150000
            ]
        ]);

        // Create AI Models for OpenAI
        AiModel::create([
            'ai_provider_id' => $openai->id,
            'name' => 'GPT-4o',
            'model_id' => 'gpt-4o',
            'description' => 'Most advanced GPT-4 model with improved reasoning and multimodal capabilities.',
            'capabilities' => ['text_generation', 'code_generation', 'analysis', 'conversation'],
            'max_tokens' => 4096,
            'context_length' => 128000,
            'supports_streaming' => true,
            'supports_function_calling' => true,
            'status' => 'active',
            'pricing_info' => [
                'input_cost_per_1k_tokens' => 0.005,
                'output_cost_per_1k_tokens' => 0.015
            ]
        ]);

        AiModel::create([
            'ai_provider_id' => $openai->id,
            'name' => 'GPT-4o Mini',
            'model_id' => 'gpt-4o-mini',
            'description' => 'Faster and more cost-effective version of GPT-4o for most tasks.',
            'capabilities' => ['text_generation', 'analysis', 'conversation'],
            'max_tokens' => 4096,
            'context_length' => 128000,
            'supports_streaming' => true,
            'supports_function_calling' => true,
            'status' => 'active',
            'pricing_info' => [
                'input_cost_per_1k_tokens' => 0.00015,
                'output_cost_per_1k_tokens' => 0.0006
            ]
        ]);

        // Create AI Models for Anthropic
        AiModel::create([
            'ai_provider_id' => $anthropic->id,
            'name' => 'Claude 3.5 Sonnet',
            'model_id' => 'claude-3-5-sonnet-20241022',
            'description' => 'Most intelligent Claude model for complex reasoning tasks.',
            'capabilities' => ['text_generation', 'analysis', 'conversation', 'reasoning'],
            'max_tokens' => 4096,
            'context_length' => 200000,
            'supports_streaming' => true,
            'supports_function_calling' => true,
            'status' => 'active',
            'pricing_info' => [
                'input_cost_per_1k_tokens' => 0.003,
                'output_cost_per_1k_tokens' => 0.015
            ]
        ]);

        AiModel::create([
            'ai_provider_id' => $anthropic->id,
            'name' => 'Claude 3 Haiku',
            'model_id' => 'claude-3-haiku-20240307',
            'description' => 'Fast and cost-effective Claude model for quick tasks.',
            'capabilities' => ['text_generation', 'analysis', 'conversation'],
            'max_tokens' => 4096,
            'context_length' => 200000,
            'supports_streaming' => true,
            'supports_function_calling' => false,
            'status' => 'active',
            'pricing_info' => [
                'input_cost_per_1k_tokens' => 0.00025,
                'output_cost_per_1k_tokens' => 0.00125
            ]
        ]);

        // Create AI Models for Google
        AiModel::create([
            'ai_provider_id' => $google->id,
            'name' => 'Gemini 1.5 Pro',
            'model_id' => 'gemini-1.5-pro',
            'description' => 'Advanced Gemini model with multimodal capabilities.',
            'capabilities' => ['text_generation', 'analysis', 'conversation', 'multimodal'],
            'max_tokens' => 8192,
            'context_length' => 1000000,
            'supports_streaming' => true,
            'supports_function_calling' => true,
            'status' => 'active',
            'pricing_info' => [
                'input_cost_per_1k_tokens' => 0.00125,
                'output_cost_per_1k_tokens' => 0.005
            ]
        ]);

        // Create Default Scoring Factors
        $factors = [
            [
                'name' => 'email_engagement',
                'display_name' => 'Email Engagement Score',
                'description' => 'Based on email open rates, click-through rates, and response rates',
                'category' => 'engagement',
                'weight' => 0.25,
                'calculation_method' => 'rule_based',
                'configuration' => [
                    'metrics' => ['open_rate', 'click_rate', 'response_rate'],
                    'weights' => [0.3, 0.4, 0.3]
                ],
                'data_source' => 'email_system'
            ],
            [
                'name' => 'company_size',
                'display_name' => 'Company Size',
                'description' => 'Company size based on employee count',
                'category' => 'firmographic',
                'weight' => 0.20,
                'calculation_method' => 'rule_based',
                'configuration' => [
                    'size_ranges' => [
                        '1-10' => 20,
                        '11-50' => 40,
                        '51-200' => 60,
                        '201-1000' => 80,
                        '1000+' => 100
                    ]
                ],
                'data_source' => 'company_data'
            ],
            [
                'name' => 'industry_match',
                'display_name' => 'Industry Match',
                'description' => 'How well the prospect\'s industry matches our target industries',
                'category' => 'firmographic',
                'weight' => 0.15,
                'calculation_method' => 'rule_based',
                'configuration' => [
                    'target_industries' => ['technology', 'finance', 'healthcare', 'retail'],
                    'scores' => [100, 80, 60, 40]
                ],
                'data_source' => 'company_data'
            ],
            [
                'name' => 'website_activity',
                'display_name' => 'Website Activity',
                'description' => 'Website visits, page views, and time spent',
                'category' => 'behavioral',
                'weight' => 0.20,
                'calculation_method' => 'rule_based',
                'configuration' => [
                    'metrics' => ['visits', 'page_views', 'time_spent'],
                    'weights' => [0.4, 0.3, 0.3]
                ],
                'data_source' => 'analytics'
            ],
            [
                'name' => 'lead_source_quality',
                'display_name' => 'Lead Source Quality',
                'description' => 'Quality score based on lead source',
                'category' => 'demographic',
                'weight' => 0.20,
                'calculation_method' => 'rule_based',
                'configuration' => [
                    'source_scores' => [
                        'referral' => 100,
                        'direct' => 80,
                        'organic_search' => 70,
                        'paid_search' => 60,
                        'social_media' => 50,
                        'cold_outreach' => 30
                    ]
                ],
                'data_source' => 'lead_system'
            ]
        ];

        foreach ($factors as $factor) {
            ScoringFactor::create($factor);
        }
    }
}
