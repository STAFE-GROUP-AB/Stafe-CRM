<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'current_tenant_id',
        'current_team_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function ownedTeams(): HasMany
    {
        return $this->hasMany(Team::class, 'owner_id');
    }

    public function teamMemberships(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function activeTeamMemberships(): HasMany
    {
        return $this->hasMany(TeamMember::class)->where('is_active', true);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadNotifications(): HasMany
    {
        return $this->hasMany(Notification::class)->where('is_read', false);
    }

    public function emails(): HasMany
    {
        return $this->hasMany(Email::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function savedSearches(): HasMany
    {
        return $this->hasMany(SavedSearch::class);
    }

    public function importJobs(): HasMany
    {
        return $this->hasMany(ImportJob::class);
    }

    /**
     * Get teams where user is active member
     */
    public function getTeamsAttribute()
    {
        return $this->activeTeamMemberships->map->team;
    }

    /**
     * Check if user belongs to specific team
     */
    public function belongsToTeam(Team $team): bool
    {
        return $this->activeTeamMemberships()
            ->where('team_id', $team->id)
            ->exists();
    }

    /**
     * Check if user has permission in team
     */
    public function hasTeamPermission(Team $team, string $permission): bool
    {
        $membership = $this->activeTeamMemberships()
            ->where('team_id', $team->id)
            ->first();

        return $membership?->hasPermission($permission) ?? false;
    }

    // Phase 3 Relationships

    public function currentTenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'current_tenant_id');
    }

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_users')
                    ->withPivot(['role', 'permissions', 'is_active', 'joined_at'])
                    ->withTimestamps();
    }

    public function tenantUsers(): HasMany
    {
        return $this->hasMany(TenantUser::class);
    }

    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRole::class);
    }

    public function apiConnections(): HasMany
    {
        return $this->hasMany(ApiConnection::class);
    }

    // Phase 3 Permission Methods

    /**
     * Check if user has permission globally or in current tenant
     */
    public function hasPermission(string $permission, $scope = null): bool
    {
        // Check global roles first
        $globalRoles = $this->userRoles()->global()->with('role.permissions')->get();
        foreach ($globalRoles as $userRole) {
            if ($userRole->role->hasPermission($permission)) {
                return true;
            }
        }

        // Check scoped roles if scope is provided
        if ($scope) {
            $scopedRoles = $this->userRoles()
                ->where('scope_type', get_class($scope))
                ->where('scope_id', $scope->id)
                ->with('role.permissions')
                ->get();
            
            foreach ($scopedRoles as $userRole) {
                if ($userRole->role->hasPermission($permission)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if user has role
     */
    public function hasRole(string $roleSlug, $scope = null): bool
    {
        $query = $this->userRoles()->whereHas('role', function ($q) use ($roleSlug) {
            $q->where('slug', $roleSlug);
        });

        if ($scope) {
            $query->where('scope_type', get_class($scope))
                  ->where('scope_id', $scope->id);
        } else {
            $query->global();
        }

        return $query->exists();
    }

    /**
     * Assign role to user
     */
    public function assignRole(Role $role, $scope = null): void
    {
        $this->userRoles()->firstOrCreate([
            'role_id' => $role->id,
            'scope_type' => $scope ? get_class($scope) : null,
            'scope_id' => $scope?->id,
        ]);
    }

    /**
     * Remove role from user
     */
    public function removeRole(Role $role, $scope = null): void
    {
        $query = $this->userRoles()->where('role_id', $role->id);

        if ($scope) {
            $query->where('scope_type', get_class($scope))
                  ->where('scope_id', $scope->id);
        } else {
            $query->global();
        }

        $query->delete();
    }

    /**
     * Get user's permissions for a specific scope
     */
    public function getPermissions($scope = null): array
    {
        $permissions = [];

        // Get global permissions
        $globalRoles = $this->userRoles()->global()->with('role.permissions')->get();
        foreach ($globalRoles as $userRole) {
            $permissions = array_merge($permissions, $userRole->role->getPermissionSlugs());
        }

        // Get scoped permissions if scope is provided
        if ($scope) {
            $scopedRoles = $this->userRoles()
                ->where('scope_type', get_class($scope))
                ->where('scope_id', $scope->id)
                ->with('role.permissions')
                ->get();
            
            foreach ($scopedRoles as $userRole) {
                $permissions = array_merge($permissions, $userRole->role->getPermissionSlugs());
            }
        }

        return array_unique($permissions);
    }

    /**
     * Check if user belongs to tenant
     */
    public function belongsToTenant(Tenant $tenant): bool
    {
        return $this->tenantUsers()
            ->where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Get user's role in tenant
     */
    public function getTenantRole(Tenant $tenant): ?string
    {
        $tenantUser = $this->tenantUsers()
            ->where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->first();

        return $tenantUser?->role;
    }

    /**
     * Check if user has tenant permission
     */
    public function hasTenantPermission(Tenant $tenant, string $permission): bool
    {
        $tenantUser = $this->tenantUsers()
            ->where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->first();

        return $tenantUser?->hasPermission($permission) ?? false;
    }

    // Phase 4.4 Sales Enablement Relationships

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'created_by');
    }

    public function salesContent(): HasMany
    {
        return $this->hasMany(SalesContent::class, 'created_by');
    }

    public function battleCards(): HasMany
    {
        return $this->hasMany(BattleCard::class, 'created_by');
    }

    public function playbooks(): HasMany
    {
        return $this->hasMany(SalesPlaybook::class, 'created_by');
    }

    public function playbookExecutions(): HasMany
    {
        return $this->hasMany(PlaybookExecution::class);
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }

    public function points(): HasOne
    {
        return $this->hasOne(UserPoint::class);
    }

    public function communications(): HasMany
    {
        return $this->hasMany(Communication::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class, 'owner_id');
    }

    public function contentUsageAnalytics(): HasMany
    {
        return $this->hasMany(ContentUsageAnalytic::class);
    }

    // Subscription relationships
    public function userSubscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function activeUserSubscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class)->where('status', 'active');
    }

    public function hasActiveSubscription(): bool
    {
        return $this->activeUserSubscriptions()->exists();
    }

    public function getCurrentSubscription(): ?UserSubscription
    {
        return $this->activeUserSubscriptions()->first();
    }

    public function canAccessFeature(string $feature): bool
    {
        $subscription = $this->getCurrentSubscription();
        
        if (!$subscription) {
            return false;
        }

        return $subscription->subscription->plan->hasFeature($feature);
    }
}
