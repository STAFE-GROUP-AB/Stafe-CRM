<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'owner_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
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