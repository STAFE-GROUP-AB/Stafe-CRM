<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RelationshipNetwork extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'nodes',
        'edges',
        'layout_config',
        'visual_config',
        'network_type',
        'user_id',
        'tenant_id',
        'last_updated',
    ];

    protected $casts = [
        'nodes' => 'array',
        'edges' => 'array',
        'layout_config' => 'array',
        'visual_config' => 'array',
        'last_updated' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('network_type', $type);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeRecentlyUpdated($query, $hours = 24)
    {
        return $query->where('last_updated', '>=', now()->subHours($hours));
    }

    public function getNodeCount(): int
    {
        return count($this->nodes);
    }

    public function getEdgeCount(): int
    {
        return count($this->edges);
    }

    public function getNetworkDensity(): float
    {
        $nodeCount = $this->getNodeCount();
        $edgeCount = $this->getEdgeCount();
        
        if ($nodeCount < 2) return 0;
        
        $maxPossibleEdges = $nodeCount * ($nodeCount - 1) / 2;
        return $edgeCount / $maxPossibleEdges;
    }

    public function getCentralNodes($limit = 5): array
    {
        $nodeDegrees = [];
        
        foreach ($this->edges as $edge) {
            $source = $edge['source'] ?? null;
            $target = $edge['target'] ?? null;
            
            if ($source) {
                $nodeDegrees[$source] = ($nodeDegrees[$source] ?? 0) + 1;
            }
            if ($target) {
                $nodeDegrees[$target] = ($nodeDegrees[$target] ?? 0) + 1;
            }
        }
        
        arsort($nodeDegrees);
        return array_slice($nodeDegrees, 0, $limit, true);
    }

    public function needsRefresh(): bool
    {
        return $this->last_updated->addHours(1)->isPast();
    }
}