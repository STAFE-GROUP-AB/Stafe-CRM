<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant;
use Symfony\Component\HttpFoundation\Response;

class TenantContext
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->resolveTenant($request);
        
        if ($tenant) {
            // Set tenant in the application context
            app()->instance('current.tenant', $tenant);
            
            // Add global scope for tenant-aware models
            $this->addGlobalTenantScope($tenant);
        }

        return $next($request);
    }

    /**
     * Resolve tenant from request
     */
    protected function resolveTenant(Request $request): ?Tenant
    {
        // Try to resolve from subdomain
        $host = $request->getHost();
        $subdomain = $this->extractSubdomain($host);
        
        if ($subdomain) {
            $tenant = Tenant::where('subdomain', $subdomain)->active()->first();
            if ($tenant) {
                return $tenant;
            }
        }

        // Try to resolve from custom domain
        $tenant = Tenant::where('domain', $host)->active()->first();
        if ($tenant) {
            return $tenant;
        }

        // Try to resolve from authenticated user's current tenant
        if (auth()->check() && auth()->user()->current_tenant_id) {
            return Tenant::find(auth()->user()->current_tenant_id);
        }

        return null;
    }

    /**
     * Extract subdomain from host
     */
    protected function extractSubdomain(string $host): ?string
    {
        $appDomain = config('app.domain');
        
        if (str_ends_with($host, '.' . $appDomain)) {
            $subdomain = str_replace('.' . $appDomain, '', $host);
            
            // Ignore www subdomain
            if ($subdomain !== 'www' && !empty($subdomain)) {
                return $subdomain;
            }
        }

        return null;
    }

    /**
     * Add global tenant scope to tenant-aware models
     */
    protected function addGlobalTenantScope(Tenant $tenant): void
    {
        $tenantAwareModels = [
            'App\Models\Company',
            'App\Models\Contact',
            'App\Models\Deal',
            'App\Models\Task',
            'App\Models\Note',
            'App\Models\CustomField',
            'App\Models\EmailTemplate',
            'App\Models\Email',
            'App\Models\Report',
            'App\Models\ActivityLog',
            'App\Models\ImportJob',
            'App\Models\SavedSearch',
            'App\Models\Team',
            'App\Models\Comment',
            'App\Models\Notification',
            'App\Models\WorkflowTemplate',
            'App\Models\WorkflowInstance',
            'App\Models\ApiConnection',
        ];

        foreach ($tenantAwareModels as $model) {
            if (class_exists($model)) {
                $model::addGlobalScope('tenant', function ($builder) use ($tenant) {
                    $builder->where('tenant_id', $tenant->id);
                });
            }
        }
    }
}