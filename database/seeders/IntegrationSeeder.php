<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IntegrationCategory;
use App\Models\Integration;

class IntegrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create integration categories
        $categories = [
            [
                'name' => 'Email Marketing',
                'slug' => 'email-marketing',
                'description' => 'Email marketing and automation platforms',
                'icon' => 'mail',
            ],
            [
                'name' => 'Communication',
                'slug' => 'communication',
                'description' => 'Communication and messaging tools',
                'icon' => 'chat',
            ],
            [
                'name' => 'E-commerce',
                'slug' => 'ecommerce',
                'description' => 'E-commerce and online store platforms',
                'icon' => 'shopping-cart',
            ],
            [
                'name' => 'Analytics',
                'slug' => 'analytics',
                'description' => 'Analytics and tracking tools',
                'icon' => 'chart-bar',
            ],
            [
                'name' => 'Productivity',
                'slug' => 'productivity',
                'description' => 'Productivity and project management tools',
                'icon' => 'briefcase',
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = IntegrationCategory::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );

            // Create sample integrations for each category
            $this->createIntegrationsForCategory($category);
        }
    }

    private function createIntegrationsForCategory(IntegrationCategory $category): void
    {
        $integrations = [];

        switch ($category->slug) {
            case 'email-marketing':
                $integrations = [
                    [
                        'name' => 'Mailchimp',
                        'slug' => 'mailchimp',
                        'description' => 'Sync contacts with Mailchimp email marketing platform',
                        'provider' => 'mailchimp',
                        'auth_type' => 'oauth',
                        'is_featured' => true,
                    ],
                    [
                        'name' => 'ConvertKit',
                        'slug' => 'convertkit',
                        'description' => 'Integrate with ConvertKit email marketing',
                        'provider' => 'convertkit',
                        'auth_type' => 'api_key',
                    ],
                ];
                break;

            case 'communication':
                $integrations = [
                    [
                        'name' => 'Slack',
                        'slug' => 'slack',
                        'description' => 'Send notifications and updates to Slack channels',
                        'provider' => 'slack',
                        'auth_type' => 'oauth',
                        'is_featured' => true,
                    ],
                    [
                        'name' => 'Microsoft Teams',
                        'slug' => 'microsoft-teams',
                        'description' => 'Integrate with Microsoft Teams',
                        'provider' => 'microsoft',
                        'auth_type' => 'oauth',
                    ],
                ];
                break;

            case 'ecommerce':
                $integrations = [
                    [
                        'name' => 'Shopify',
                        'slug' => 'shopify',
                        'description' => 'Sync customers and orders from Shopify',
                        'provider' => 'shopify',
                        'auth_type' => 'oauth',
                        'is_featured' => true,
                    ],
                    [
                        'name' => 'WooCommerce',
                        'slug' => 'woocommerce',
                        'description' => 'Connect with WooCommerce stores',
                        'provider' => 'woocommerce',
                        'auth_type' => 'api_key',
                    ],
                ];
                break;

            case 'analytics':
                $integrations = [
                    [
                        'name' => 'Google Analytics',
                        'slug' => 'google-analytics',
                        'description' => 'Track website visitors and conversions',
                        'provider' => 'google',
                        'auth_type' => 'oauth',
                    ],
                ];
                break;

            case 'productivity':
                $integrations = [
                    [
                        'name' => 'Trello',
                        'slug' => 'trello',
                        'description' => 'Create cards and manage projects in Trello',
                        'provider' => 'trello',
                        'auth_type' => 'oauth',
                    ],
                ];
                break;
        }

        foreach ($integrations as $integrationData) {
            Integration::firstOrCreate(
                ['slug' => $integrationData['slug']],
                array_merge($integrationData, [
                    'category_id' => $category->id,
                    'config_schema' => $this->getDefaultConfigSchema(),
                    'webhook_endpoints' => $this->getDefaultWebhookEndpoints(),
                    'api_endpoints' => $this->getDefaultApiEndpoints(),
                    'auth_config' => $this->getDefaultAuthConfig($integrationData['auth_type']),
                ])
            );
        }
    }

    private function getDefaultConfigSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'api_url' => ['type' => 'string', 'description' => 'API endpoint URL'],
                'sync_interval' => ['type' => 'integer', 'description' => 'Sync interval in minutes'],
            ],
        ];
    }

    private function getDefaultWebhookEndpoints(): array
    {
        return [
            'contact_created' => '/webhook/contact-created',
            'contact_updated' => '/webhook/contact-updated',
        ];
    }

    private function getDefaultApiEndpoints(): array
    {
        return [
            'contacts' => '/api/contacts',
            'sync' => '/api/sync',
        ];
    }

    private function getDefaultAuthConfig(string $authType): array
    {
        switch ($authType) {
            case 'oauth':
                return [
                    'client_id' => null,
                    'client_secret' => null,
                    'redirect_uri' => null,
                    'scopes' => [],
                ];
            case 'api_key':
                return [
                    'key_name' => 'api_key',
                    'key_location' => 'header', // header, query, body
                ];
            default:
                return [];
        }
    }
}