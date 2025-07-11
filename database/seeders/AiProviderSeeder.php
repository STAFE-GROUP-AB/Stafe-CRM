<?php

namespace Database\Seeders;

use App\Models\AiProvider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AiProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AiProvider::create([
            'name' => 'OpenAI',
            'slug' => 'openai',
            'description' => 'OpenAI GPT models for advanced AI capabilities',
            'api_base_url' => 'https://api.openai.com/v1',
            'supported_features' => ['text_generation', 'embeddings', 'image_analysis'],
            'authentication_methods' => ['api_key', 'bearer_token'],
            'configuration_schema' => [
                'api_key' => ['type' => 'string', 'required' => true],
                'models' => [
                    'gpt-4' => 'GPT-4',
                    'gpt-4-turbo' => 'GPT-4 Turbo',
                    'gpt-3.5-turbo' => 'GPT-3.5 Turbo'
                ]
            ],
            'status' => 'active',
        ]);

        AiProvider::create([
            'name' => 'Anthropic Claude',
            'slug' => 'anthropic',
            'description' => 'Anthropic Claude models for advanced reasoning',
            'api_base_url' => 'https://api.anthropic.com/v1',
            'supported_features' => ['text_generation', 'reasoning'],
            'authentication_methods' => ['api_key'],
            'configuration_schema' => [
                'api_key' => ['type' => 'string', 'required' => true],
                'models' => [
                    'claude-3-opus' => 'Claude 3 Opus',
                    'claude-3-sonnet' => 'Claude 3 Sonnet',
                    'claude-3-haiku' => 'Claude 3 Haiku'
                ]
            ],
            'status' => 'active',
        ]);

        AiProvider::create([
            'name' => 'Google Gemini',
            'slug' => 'google',
            'description' => 'Google Gemini models for multimodal AI',
            'api_base_url' => 'https://generativelanguage.googleapis.com/v1',
            'supported_features' => ['text_generation', 'image_analysis', 'multimodal'],
            'authentication_methods' => ['api_key'],
            'configuration_schema' => [
                'api_key' => ['type' => 'string', 'required' => true],
                'models' => [
                    'gemini-pro' => 'Gemini Pro',
                    'gemini-pro-vision' => 'Gemini Pro Vision'
                ]
            ],
            'status' => 'active',
        ]);

        AiProvider::create([
            'name' => 'Azure OpenAI',
            'slug' => 'azure-openai',
            'description' => 'Azure OpenAI Service for enterprise AI',
            'api_base_url' => 'https://your-resource.openai.azure.com',
            'supported_features' => ['text_generation', 'embeddings', 'image_analysis'],
            'authentication_methods' => ['api_key'],
            'configuration_schema' => [
                'api_key' => ['type' => 'string', 'required' => true],
                'endpoint' => ['type' => 'string', 'required' => true],
                'models' => [
                    'gpt-4' => 'GPT-4',
                    'gpt-35-turbo' => 'GPT-3.5 Turbo'
                ]
            ],
            'status' => 'active',
        ]);
    }
}
