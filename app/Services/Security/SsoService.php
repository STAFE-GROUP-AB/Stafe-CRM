<?php

namespace App\Services\Security;

use App\Models\SsoProvider;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SsoService
{
    public function getAvailableProviders(?int $tenantId = null): array
    {
        return SsoProvider::active()
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->get()
            ->toArray();
    }

    public function redirectToProvider(SsoProvider $provider): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $config = $this->buildProviderConfig($provider);
        
        return Socialite::buildProvider($config['class'], $config['config'])
            ->redirect();
    }

    public function handleProviderCallback(SsoProvider $provider): array
    {
        $config = $this->buildProviderConfig($provider);
        
        try {
            $socialiteUser = Socialite::buildProvider($config['class'], $config['config'])
                ->user();

            return $this->processProviderUser($provider, $socialiteUser);

        } catch (\Exception $e) {
            logger()->error('SSO callback failed', [
                'provider' => $provider->name,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Authentication failed: ' . $e->getMessage(),
            ];
        }
    }

    protected function processProviderUser(SsoProvider $provider, $socialiteUser): array
    {
        // Map provider attributes to user attributes
        $mappedAttributes = $provider->mapAttributes($socialiteUser->getRaw());
        
        // Try to find existing user
        $user = $this->findOrCreateUser($provider, $socialiteUser, $mappedAttributes);
        
        if (!$user) {
            return [
                'success' => false,
                'error' => 'User creation failed or not allowed',
            ];
        }

        // Log the user in
        Auth::login($user, true);

        return [
            'success' => true,
            'user' => $user,
            'provider' => $provider->name,
        ];
    }

    protected function findOrCreateUser(SsoProvider $provider, $socialiteUser, array $mappedAttributes): ?User
    {
        // Try to find user by email first
        $email = $mappedAttributes['email'] ?? $socialiteUser->getEmail();
        
        if (!$email) {
            logger()->warning('SSO user has no email', [
                'provider' => $provider->name,
                'provider_id' => $socialiteUser->getId(),
            ]);
            return null;
        }

        $user = User::where('email', $email)
            ->where('tenant_id', $provider->tenant_id)
            ->first();

        if ($user) {
            // Update user with latest provider information
            $this->updateUserFromProvider($user, $provider, $mappedAttributes);
            return $user;
        }

        // Create new user if auto-provisioning is enabled
        if ($provider->auto_provision) {
            return $this->createUserFromProvider($provider, $socialiteUser, $mappedAttributes);
        }

        return null;
    }

    protected function updateUserFromProvider(User $user, SsoProvider $provider, array $mappedAttributes): User
    {
        $updates = [];
        
        foreach ($mappedAttributes as $field => $value) {
            if (!empty($value) && $user->isFillable($field)) {
                $updates[$field] = $value;
            }
        }

        if (!empty($updates)) {
            $user->update($updates);
        }

        return $user;
    }

    protected function createUserFromProvider(SsoProvider $provider, $socialiteUser, array $mappedAttributes): User
    {
        $userData = [
            'tenant_id' => $provider->tenant_id,
            'email' => $mappedAttributes['email'] ?? $socialiteUser->getEmail(),
            'name' => $mappedAttributes['name'] ?? $socialiteUser->getName() ?? 'SSO User',
            'email_verified_at' => now(),
            'password' => bcrypt(Str::random(32)), // Random password since they'll use SSO
        ];

        // Add other mapped attributes
        foreach ($mappedAttributes as $field => $value) {
            if (!empty($value) && !isset($userData[$field])) {
                $userData[$field] = $value;
            }
        }

        $user = User::create($userData);

        // Assign default role if specified
        if ($provider->default_role) {
            $user->assignRole($provider->default_role);
        }

        return $user;
    }

    protected function buildProviderConfig(SsoProvider $provider): array
    {
        $baseConfig = [
            'client_id' => $provider->client_id,
            'client_secret' => $provider->client_secret,
            'redirect' => $provider->getRedirectUrl(),
        ];

        switch ($provider->provider_type) {
            case 'oauth':
                return $this->buildOAuthConfig($provider, $baseConfig);
            case 'saml':
                return $this->buildSamlConfig($provider, $baseConfig);
            case 'oidc':
                return $this->buildOidcConfig($provider, $baseConfig);
            default:
                throw new \InvalidArgumentException("Unsupported provider type: {$provider->provider_type}");
        }
    }

    protected function buildOAuthConfig(SsoProvider $provider, array $baseConfig): array
    {
        $providerClass = match (strtolower($provider->name)) {
            'google' => \Laravel\Socialite\Two\GoogleProvider::class,
            'microsoft' => \Laravel\Socialite\Two\MicrosoftProvider::class,
            'github' => \Laravel\Socialite\Two\GithubProvider::class,
            'linkedin' => \Laravel\Socialite\Two\LinkedInProvider::class,
            default => throw new \InvalidArgumentException("Unsupported OAuth provider: {$provider->name}"),
        };

        $config = $baseConfig;
        
        if ($provider->scopes) {
            $config['scopes'] = $provider->scopes;
        }

        return [
            'class' => $providerClass,
            'config' => $config,
        ];
    }

    protected function buildSamlConfig(SsoProvider $provider, array $baseConfig): array
    {
        // For SAML, we would typically use a different package like aacotroneo/laravel-saml2
        // This is a placeholder for SAML configuration
        return [
            'class' => \App\Services\Security\SamlProvider::class, // Custom SAML provider
            'config' => array_merge($baseConfig, [
                'entity_id' => $provider->entity_id,
                'sso_url' => $provider->sso_url,
                'sls_url' => $provider->sls_url,
                'certificate' => $provider->certificate,
            ]),
        ];
    }

    protected function buildOidcConfig(SsoProvider $provider, array $baseConfig): array
    {
        // For OIDC, we would use an OIDC-specific provider
        // This is a placeholder for OIDC configuration
        return [
            'class' => \App\Services\Security\OidcProvider::class, // Custom OIDC provider
            'config' => array_merge($baseConfig, [
                'discovery_url' => $provider->metadata['discovery_url'] ?? null,
                'scopes' => $provider->scopes ?? ['openid', 'profile', 'email'],
            ]),
        ];
    }

    public function createProvider(array $data): SsoProvider
    {
        return SsoProvider::create(array_merge($data, [
            'tenant_id' => auth()->user()?->tenant_id ?? 1,
        ]));
    }

    public function updateProvider(SsoProvider $provider, array $data): SsoProvider
    {
        $provider->update($data);
        return $provider;
    }

    public function testProviderConnection(SsoProvider $provider): array
    {
        try {
            $config = $this->buildProviderConfig($provider);
            
            // Basic validation
            if (empty($config['config']['client_id']) || empty($config['config']['client_secret'])) {
                return [
                    'success' => false,
                    'error' => 'Missing client credentials',
                ];
            }

            // For OAuth providers, we can test by building the authorization URL
            if ($provider->provider_type === 'oauth') {
                $authUrl = Socialite::buildProvider($config['class'], $config['config'])
                    ->stateless()
                    ->getTargetUrl();

                return [
                    'success' => true,
                    'message' => 'Provider configuration appears valid',
                    'auth_url' => $authUrl,
                ];
            }

            return [
                'success' => true,
                'message' => 'Provider configuration saved successfully',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Configuration test failed: ' . $e->getMessage(),
            ];
        }
    }

    public function getLoginUrl(SsoProvider $provider): string
    {
        return route('sso.redirect', ['provider' => $provider->id]);
    }
}