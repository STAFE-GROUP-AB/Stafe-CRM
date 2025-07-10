<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dashboard extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'layout_config',
        'widgets',
        'is_default',
        'is_public',
        'type',
        'user_id',
        'tenant_id',
    ];

    protected $casts = [
        'layout_config' => 'array',
        'widgets' => 'array',
        'is_default' => 'boolean',
        'is_public' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function dashboardWidgets(): HasMany
    {
        return $this->hasMany(DashboardWidget::class);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopePersonal($query)
    {
        return $query->where('type', 'personal');
    }

    public function scopeTeam($query)
    {
        return $query->where('type', 'team');
    }

    public function scopeCompany($query)
    {
        return $query->where('type', 'company');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function isAccessibleBy(User $user): bool
    {
        return $this->user_id === $user->id || 
               $this->is_public || 
               ($this->type === 'team' && $user->teams()->exists()) ||
               ($this->type === 'company' && $this->tenant_id === $user->tenant_id);
    }
}