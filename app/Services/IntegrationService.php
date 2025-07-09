<?php

namespace App\Services;

use App\Models\Integration;
use App\Models\ApiConnection;
use App\Models\User;

class IntegrationService
{
    /**
     * Install integration for user
     */
    public function installIntegration(Integration $integration, User $user, array $config): ApiConnection
    {
        // Validate configuration against schema
        $validation = $integration->validateConfig($config);
        if (!$validation['valid']) {
            throw new \InvalidArgumentException('Invalid configuration: ' . implode(', ', $validation['errors']));
        }

        // Create API connection
        $connection = ApiConnection::create([
            'integration_id' => $integration->id,
            'user_id' => $user->id,
            'name' => $config['name'] ?? $integration->name,
            'config' => $config,
            'credentials' => $config['credentials'] ?? [],
            'status' => 'inactive',
        ]);

        // Test the connection
        $testResult = $connection->testConnection();
        if ($testResult['success']) {
            $integration->incrementInstallCount();
        }

        return $connection;
    }

    /**
     * Uninstall integration for user
     */
    public function uninstallIntegration(ApiConnection $connection): bool
    {
        $connection->delete();
        return true;
    }

    /**
     * Sync data for connection
     */
    public function syncConnection(ApiConnection $connection): array
    {
        if (!$connection->isActive()) {
            return ['success' => false, 'message' => 'Connection is not active'];
        }

        return $connection->sync();
    }

    /**
     * Get marketplace integrations
     */
    public function getMarketplaceIntegrations(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = Integration::active()->with('category');

        if (isset($filters['category_id'])) {
            $query->byCategory($filters['category_id']);
        }

        if (isset($filters['provider'])) {
            $query->byProvider($filters['provider']);
        }

        if (isset($filters['featured']) && $filters['featured']) {
            $query->featured();
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->orderBy('is_featured', 'desc')
                    ->orderBy('install_count', 'desc')
                    ->orderBy('name')
                    ->get();
    }

    /**
     * Get user's installed integrations
     */
    public function getUserIntegrations(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return $user->apiConnections()
                   ->with(['integration.category'])
                   ->orderBy('created_at', 'desc')
                   ->get();
    }

    /**
     * Get integration statistics
     */
    public function getIntegrationStats(Integration $integration): array
    {
        return [
            'total_installations' => $integration->install_count,
            'active_connections' => $integration->connections()->active()->count(),
            'total_connections' => $integration->connections()->count(),
            'error_rate' => $this->calculateErrorRate($integration),
            'recent_activity' => $this->getRecentActivity($integration),
        ];
    }

    /**
     * Calculate integration error rate
     */
    protected function calculateErrorRate(Integration $integration): float
    {
        $totalConnections = $integration->connections()->count();
        if ($totalConnections === 0) {
            return 0;
        }

        $errorConnections = $integration->connections()->where('status', 'error')->count();
        return round(($errorConnections / $totalConnections) * 100, 2);
    }

    /**
     * Get recent integration activity
     */
    protected function getRecentActivity(Integration $integration): array
    {
        return $integration->connections()
                          ->whereNotNull('last_sync_at')
                          ->orderBy('last_sync_at', 'desc')
                          ->limit(10)
                          ->get()
                          ->map(function ($connection) {
                              return [
                                  'user_id' => $connection->user_id,
                                  'status' => $connection->status,
                                  'last_sync' => $connection->last_sync_at,
                                  'sync_stats' => $connection->sync_stats,
                              ];
                          })
                          ->toArray();
    }

    /**
     * Refresh OAuth token for connection
     */
    public function refreshOAuthToken(ApiConnection $connection): array
    {
        if ($connection->integration->auth_type !== 'oauth') {
            return ['success' => false, 'message' => 'Not an OAuth connection'];
        }

        try {
            // Implement OAuth token refresh logic here
            // This would typically involve calling the OAuth provider's token refresh endpoint
            
            $connection->markAsActive();
            return ['success' => true, 'message' => 'Token refreshed successfully'];
        } catch (\Exception $e) {
            $connection->markAsError($e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Process webhook for integration
     */
    public function processWebhook(Integration $integration, string $endpoint, array $payload): array
    {
        if (!$integration->supportsWebhooks()) {
            return ['success' => false, 'message' => 'Integration does not support webhooks'];
        }

        $endpoints = $integration->getWebhookEndpoints();
        if (!isset($endpoints[$endpoint])) {
            return ['success' => false, 'message' => 'Invalid webhook endpoint'];
        }

        try {
            // Process webhook based on integration type and endpoint
            switch ($integration->slug) {
                case 'mailchimp':
                    return $this->processMailchimpWebhook($endpoint, $payload);
                case 'shopify':
                    return $this->processShopifyWebhook($endpoint, $payload);
                default:
                    return $this->processGenericWebhook($integration, $endpoint, $payload);
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Process Mailchimp webhook
     */
    protected function processMailchimpWebhook(string $endpoint, array $payload): array
    {
        // Implement Mailchimp-specific webhook processing
        return ['success' => true, 'message' => 'Mailchimp webhook processed'];
    }

    /**
     * Process Shopify webhook
     */
    protected function processShopifyWebhook(string $endpoint, array $payload): array
    {
        // Implement Shopify-specific webhook processing
        return ['success' => true, 'message' => 'Shopify webhook processed'];
    }

    /**
     * Process generic webhook
     */
    protected function processGenericWebhook(Integration $integration, string $endpoint, array $payload): array
    {
        // Implement generic webhook processing
        return ['success' => true, 'message' => 'Generic webhook processed'];
    }
}