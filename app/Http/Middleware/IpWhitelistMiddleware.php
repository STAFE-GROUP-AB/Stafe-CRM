<?php

namespace App\Http\Middleware;

use App\Models\IpWhitelistRule;
use App\Services\Security\AuditTrailService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IpWhitelistMiddleware
{
    public function __construct(
        protected AuditTrailService $auditService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $clientIp = $request->ip();
        $path = $request->path();
        $tenantId = $this->getTenantId($request);

        // Check if IP is allowed
        if (!IpWhitelistRule::isIpAllowed($clientIp, $tenantId, $path)) {
            // Log the blocked attempt
            $this->auditService->logSecurityEvent(
                'ip_access_denied',
                "Access denied for IP: {$clientIp} to path: {$path}",
                [
                    'ip_address' => $clientIp,
                    'path' => $path,
                    'user_agent' => $request->userAgent(),
                    'tenant_id' => $tenantId,
                ]
            );

            return response()->json([
                'error' => 'Access denied',
                'message' => 'Your IP address is not authorized to access this resource.',
            ], 403);
        }

        return $next($request);
    }

    protected function getTenantId(Request $request): ?int
    {
        // Try to get tenant ID from authenticated user
        if ($user = $request->user()) {
            return $user->tenant_id;
        }

        // Try to get from subdomain or other tenant identification methods
        $subdomain = $this->extractSubdomain($request);
        if ($subdomain) {
            // Look up tenant by subdomain
            // This would depend on your tenant identification strategy
            return $this->getTenantBySubdomain($subdomain);
        }

        return null;
    }

    protected function extractSubdomain(Request $request): ?string
    {
        $host = $request->getHost();
        $parts = explode('.', $host);
        
        // If we have more than 2 parts (e.g., tenant.example.com)
        if (count($parts) > 2) {
            return $parts[0];
        }

        return null;
    }

    protected function getTenantBySubdomain(string $subdomain): ?int
    {
        // This is a placeholder - implement based on your tenant model
        // Example: return Tenant::where('subdomain', $subdomain)->value('id');
        return null;
    }
}