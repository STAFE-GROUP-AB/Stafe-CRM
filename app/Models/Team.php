<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\Team as JetstreamTeam;

class Team extends JetstreamTeam
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Team $team) {
            if (empty($team->slug)) {
                $team->slug = Str::slug($team->name) . '-' . Str::random(6);
            }
        });
    }

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'user_id',
        'personal_team',
        'theme_settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'personal_team' => 'boolean',
        'theme_settings' => 'array',
    ];

    /**
     * The event map for the model.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];

    // Alias owner() to use user_id for Jetstream compatibility
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Keep backward compatibility with old references
    public function getOwnerIdAttribute()
    {
        return $this->user_id;
    }

    public function members(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function activeMembers(): HasMany
    {
        return $this->hasMany(TeamMember::class)->where('is_active', true);
    }

    public function admins(): HasMany
    {
        return $this->hasMany(TeamMember::class)->where('role', 'admin');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if user is a member of this team
     */
    public function hasMember(User $user): bool
    {
        return $this->members()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Check if user is an admin of this team
     */
    public function hasAdmin(User $user): bool
    {
        return $this->members()
            ->where('user_id', $user->id)
            ->whereIn('role', ['owner', 'admin'])
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Add a user to the team
     */
    public function addMember(User $user, string $role = 'member'): TeamMember
    {
        return $this->members()->create([
            'user_id' => $user->id,
            'role' => $role,
            'joined_at' => now(),
        ]);
    }

    /**
     * Remove a user from the team
     */
    public function removeMember(User $user): bool
    {
        return $this->members()
            ->where('user_id', $user->id)
            ->delete();
    }
}