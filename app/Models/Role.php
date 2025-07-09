<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_system',
        'is_active',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRole::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    public function scopeCustom($query)
    {
        return $query->where('is_system', false);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Check if role is system role
     */
    public function isSystem(): bool
    {
        return $this->is_system;
    }

    /**
     * Check if role has permission
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->permissions()->where('slug', $permissionSlug)->exists();
    }

    /**
     * Grant permission to role
     */
    public function grantPermission(Permission $permission): void
    {
        if (!$this->hasPermission($permission->slug)) {
            $this->permissions()->attach($permission);
        }
    }

    /**
     * Revoke permission from role
     */
    public function revokePermission(Permission $permission): void
    {
        $this->permissions()->detach($permission);
    }

    /**
     * Sync permissions for role
     */
    public function syncPermissions(array $permissionIds): void
    {
        $this->permissions()->sync($permissionIds);
    }

    /**
     * Get all permission slugs for this role
     */
    public function getPermissionSlugs(): array
    {
        return $this->permissions()->pluck('slug')->toArray();
    }
}