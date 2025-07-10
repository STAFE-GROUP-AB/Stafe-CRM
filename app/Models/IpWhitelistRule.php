<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class IpWhitelistRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'ip_address',
        'description',
        'rule_type',
        'allowed_actions',
        'restricted_paths',
        'is_active',
        'created_by',
        'expires_at',
        'priority',
        'metadata',
    ];

    protected $casts = [
        'allowed_actions' => 'array',
        'restricted_paths' => 'array',
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function matchesIp(string $ip): bool
    {
        // Handle CIDR notation
        if (str_contains($this->ip_address, '/')) {
            return $this->ipInCidr($ip, $this->ip_address);
        }
        
        // Handle wildcard matching
        if (str_contains($this->ip_address, '*')) {
            return $this->ipMatchesWildcard($ip, $this->ip_address);
        }
        
        // Exact match
        return $ip === $this->ip_address;
    }

    protected function ipInCidr(string $ip, string $cidr): bool
    {
        [$subnet, $mask] = explode('/', $cidr);
        
        $ip_long = ip2long($ip);
        $subnet_long = ip2long($subnet);
        $mask_long = -1 << (32 - (int)$mask);
        
        return ($ip_long & $mask_long) === ($subnet_long & $mask_long);
    }

    protected function ipMatchesWildcard(string $ip, string $pattern): bool
    {
        $pattern = str_replace('*', '.*', preg_quote($pattern, '/'));
        return preg_match('/^' . $pattern . '$/', $ip);
    }

    public static function isIpAllowed(string $ip, ?int $tenantId = null, ?string $path = null): bool
    {
        $cacheKey = "ip_whitelist_{$ip}_{$tenantId}_{$path}";
        
        return Cache::remember($cacheKey, 300, function () use ($ip, $tenantId, $path) {
            $rules = static::active()
                          ->when($tenantId, fn($q) => $q->forTenant($tenantId))
                          ->byPriority()
                          ->get();

            foreach ($rules as $rule) {
                if ($rule->matchesIp($ip)) {
                    // Check if path is restricted
                    if ($path && $rule->restricted_paths) {
                        $pathMatches = collect($rule->restricted_paths)
                            ->some(fn($pattern) => fnmatch($pattern, $path));
                        
                        if (!$pathMatches) {
                            continue;
                        }
                    }

                    return $rule->rule_type === 'allow';
                }
            }

            // Default: allow if no rules match (unless there are deny rules)
            return !static::active()
                         ->when($tenantId, fn($q) => $q->forTenant($tenantId))
                         ->where('rule_type', 'deny')
                         ->exists();
        });
    }
}