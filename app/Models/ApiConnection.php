<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ApiConnection extends Model
{
    use HasFactory;

    protected $fillable = [
        'integration_id',
        'user_id',
        'name',
        'config',
        'credentials',
        'status',
        'last_sync_at',
        'last_error',
        'sync_stats',
    ];

    protected $casts = [
        'config' => 'array',
        'credentials' => 'encrypted:array',
        'sync_stats' => 'array',
        'last_sync_at' => 'datetime',
    ];

    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integration::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Check if connection is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if connection has errors
     */
    public function hasErrors(): bool
    {
        return $this->status === 'error' || !empty($this->last_error);
    }

    /**
     * Mark connection as active
     */
    public function markAsActive(): void
    {
        $this->update([
            'status' => 'active',
            'last_error' => null,
        ]);
    }

    /**
     * Mark connection as inactive
     */
    public function markAsInactive(): void
    {
        $this->update(['status' => 'inactive']);
    }

    /**
     * Mark connection as error
     */
    public function markAsError(string $error): void
    {
        $this->update([
            'status' => 'error',
            'last_error' => $error,
        ]);
    }

    /**
     * Update sync statistics
     */
    public function updateSyncStats(array $stats): void
    {
        $this->update([
            'sync_stats' => array_merge($this->sync_stats ?? [], $stats),
            'last_sync_at' => now(),
        ]);
    }

    /**
     * Test the connection
     */
    public function testConnection(): array
    {
        try {
            // Implement connection testing logic
            $this->markAsActive();
            return ['success' => true, 'message' => 'Connection successful'];
        } catch (\Exception $e) {
            $this->markAsError($e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Perform sync operation
     */
    public function sync(): array
    {
        try {
            // Implement sync logic based on integration type
            $stats = [
                'synced_at' => now()->toISOString(),
                'records_processed' => 0,
                'records_created' => 0,
                'records_updated' => 0,
                'errors' => 0,
            ];

            $this->updateSyncStats($stats);
            return ['success' => true, 'stats' => $stats];
        } catch (\Exception $e) {
            $this->markAsError($e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}