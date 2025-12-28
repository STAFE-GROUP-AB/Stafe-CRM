<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TeamMember extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'team_user';

    protected $fillable = [
        'team_id',
        'user_id',
        'role',
        'permissions',
        'is_active',
        'joined_at',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
        'joined_at' => 'datetime',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function scopeAdmins($query)
    {
        return $query->whereIn('role', ['owner', 'admin']);
    }

    public function scopeMembers($query)
    {
        return $query->where('role', 'member');
    }

    /**
     * Check if member has permission
     */
    public function hasPermission(string $permission): bool
    {
        // Owner and admin have all permissions
        if (in_array($this->role, ['owner', 'admin'])) {
            return true;
        }

        // Check specific permissions
        return in_array($permission, $this->permissions ?? []);
    }

    /**
     * Grant permission to member
     */
    public function grantPermission(string $permission): void
    {
        $permissions = $this->permissions ?? [];
        
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->update(['permissions' => $permissions]);
        }
    }

    /**
     * Revoke permission from member
     */
    public function revokePermission(string $permission): void
    {
        $permissions = $this->permissions ?? [];
        
        $permissions = array_filter($permissions, fn($p) => $p !== $permission);
        
        $this->update(['permissions' => array_values($permissions)]);
    }

    /**
     * Check if member is owner
     */
    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    /**
     * Check if member is admin
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['owner', 'admin']);
    }
}