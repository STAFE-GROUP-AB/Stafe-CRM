<?php

namespace App\Services\Security;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;
use Illuminate\Support\Facades\Http;

class OidcProvider extends AbstractProvider
{
    protected $scopes = ['openid', 'profile', 'email'];
    
    protected $scopeSeparator = ' ';

    protected $discoveryDocument = null;

    /**
     * Get the authentication URL for the provider.
     */
    protected function getAuthUrl($state)
    {
        $discovery = $this->getDiscoveryDocument();
        
        return $this->buildAuthUrlFromBase(
            $discovery['authorization_endpoint'],
            $state
        );
    }

    /**
     * Get the token URL for the provider.
     */
    protected function getTokenUrl()
    {
        $discovery = $this->getDiscoveryDocument();
        
        return $discovery['token_endpoint'];
    }

    /**
     * Get the user info URL.
     */
    protected function getUserInfoUrl()
    {
        $discovery = $this->getDiscoveryDocument();
        
        return $discovery['userinfo_endpoint'];
    }

    /**
     * Get the raw user for the given access token.
     */
    protected function getUserByToken($token)
    {
        $response = Http::withToken($token)
            ->get($this->getUserInfoUrl());

        return $response->json();
    }

    /**
     * Map the raw user array to a Socialite User instance.
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['sub'] ?? null,
            'nickname' => $user['preferred_username'] ?? $user['nickname'] ?? null,
            'name' => $user['name'] ?? null,
            'email' => $user['email'] ?? null,
            'avatar' => $user['picture'] ?? null,
        ]);
    }

    /**
     * Get OIDC discovery document
     */
    protected function getDiscoveryDocument(): array
    {
        if ($this->discoveryDocument !== null) {
            return $this->discoveryDocument;
        }

        $discoveryUrl = $this->config['discovery_url'] ?? null;
        
        if (!$discoveryUrl) {
            throw new \InvalidArgumentException('OIDC discovery URL not configured');
        }

        try {
            $response = Http::get($discoveryUrl);
            
            if (!$response->successful()) {
                throw new \Exception('Failed to fetch OIDC discovery document');
            }

            $this->discoveryDocument = $response->json();
            
            return $this->discoveryDocument;

        } catch (\Exception $e) {
            throw new \Exception('OIDC discovery failed: ' . $e->getMessage());
        }
    }

    /**
     * Get the POST fields for the token request.
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }

    /**
     * Verify ID token (JWT)
     */
    public function verifyIdToken(string $idToken): array
    {
        // Split JWT into parts
        $parts = explode('.', $idToken);
        
        if (count($parts) !== 3) {
            throw new \Exception('Invalid ID token format');
        }

        [$header, $payload, $signature] = $parts;

        // Decode payload
        $decodedPayload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $payload)), true);

        if (!$decodedPayload) {
            throw new \Exception('Failed to decode ID token payload');
        }

        // Verify claims
        $this->verifyTokenClaims($decodedPayload);

        return $decodedPayload;
    }

    /**
     * Verify token claims
     */
    protected function verifyTokenClaims(array $claims): void
    {
        $now = time();

        // Verify expiration
        if (isset($claims['exp']) && $claims['exp'] < $now) {
            throw new \Exception('ID token has expired');
        }

        // Verify not before
        if (isset($claims['nbf']) && $claims['nbf'] > $now) {
            throw new \Exception('ID token not yet valid');
        }

        // Verify issuer
        $discovery = $this->getDiscoveryDocument();
        if (isset($claims['iss']) && $claims['iss'] !== $discovery['issuer']) {
            throw new \Exception('ID token issuer mismatch');
        }

        // Verify audience
        if (isset($claims['aud']) && $claims['aud'] !== $this->clientId) {
            throw new \Exception('ID token audience mismatch');
        }
    }

    /**
     * Get JWKS (JSON Web Key Set) for token verification
     */
    protected function getJwks(): array
    {
        $discovery = $this->getDiscoveryDocument();
        $jwksUri = $discovery['jwks_uri'] ?? null;

        if (!$jwksUri) {
            throw new \Exception('JWKS URI not found in discovery document');
        }

        $response = Http::get($jwksUri);

        if (!$response->successful()) {
            throw new \Exception('Failed to fetch JWKS');
        }

        return $response->json();
    }

    /**
     * End session (logout)
     */
    public function endSession(string $idToken): string
    {
        $discovery = $this->getDiscoveryDocument();
        $endSessionEndpoint = $discovery['end_session_endpoint'] ?? null;

        if (!$endSessionEndpoint) {
            return route('login');
        }

        $postLogoutRedirect = $this->config['post_logout_redirect_uri'] ?? route('login');

        return $endSessionEndpoint . '?' . http_build_query([
            'id_token_hint' => $idToken,
            'post_logout_redirect_uri' => $postLogoutRedirect,
        ]);
    }
}
