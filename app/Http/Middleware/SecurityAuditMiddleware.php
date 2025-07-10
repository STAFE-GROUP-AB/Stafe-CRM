<?php

namespace App\Http\Middleware;

use App\Services\Security\AuditTrailService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityAuditMiddleware
{
    public function __construct(
        protected AuditTrailService $auditService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Log the request for audit purposes
        $this->logRequest($request, $response);

        return $response;
    }

    protected function logRequest(Request $request, Response $response): void
    {
        // Only log certain types of requests
        if (!$this->shouldLogRequest($request)) {
            return;
        }

        $eventType = $this->determineEventType($request);
        $riskLevel = $this->determineRiskLevel($request, $response);

        $this->auditService->logEvent([
            'event_type' => $eventType,
            'event_category' => 'system',
            'risk_level' => $riskLevel,
            'description' => $this->buildDescription($request, $response),
            'metadata' => [
                'method' => $request->method(),
                'path' => $request->path(),
                'status_code' => $response->getStatusCode(),
                'query_params' => $request->query(),
                'user_agent' => $request->userAgent(),
                'referer' => $request->header('referer'),
            ],
        ]);
    }

    protected function shouldLogRequest(Request $request): bool
    {
        // Skip logging for certain routes/requests
        $skipPaths = [
            'api/health',
            'heartbeat',
            '_debugbar',
            'favicon.ico',
        ];

        $path = $request->path();
        
        foreach ($skipPaths as $skipPath) {
            if (str_starts_with($path, $skipPath)) {
                return false;
            }
        }

        // Log API requests, admin actions, and POST/PUT/DELETE requests
        return $request->is('api/*') || 
               $request->is('admin/*') || 
               in_array($request->method(), ['POST', 'PUT', 'DELETE', 'PATCH']);
    }

    protected function determineEventType(Request $request): string
    {
        if ($request->is('api/*')) {
            return 'api_request';
        }

        if ($request->is('admin/*')) {
            return 'admin_action';
        }

        return match ($request->method()) {
            'POST' => 'data_creation',
            'PUT', 'PATCH' => 'data_modification',
            'DELETE' => 'data_deletion',
            default => 'data_access',
        };
    }

    protected function determineRiskLevel(Request $request, Response $response): string
    {
        // High risk for failed attempts
        if ($response->getStatusCode() >= 400) {
            return 'high';
        }

        // Medium risk for admin actions
        if ($request->is('admin/*')) {
            return 'medium';
        }

        // Medium risk for DELETE operations
        if ($request->method() === 'DELETE') {
            return 'medium';
        }

        return 'low';
    }

    protected function buildDescription(Request $request, Response $response): string
    {
        $method = $request->method();
        $path = $request->path();
        $status = $response->getStatusCode();
        
        return "{$method} request to {$path} returned {$status}";
    }
}