<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'role_id',
        'scope_type',
        'scope_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function scope(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForRole($query, int $roleId)
    {
        return $query->where('role_id', $roleId);
    }

    public function scopeGlobal($query)
    {
        return $query->whereNull('scope_type')->whereNull('scope_id');
    }

    public function scopeScoped($query)
    {
        return $query->whereNotNull('scope_type')->whereNotNull('scope_id');
    }

    /**
     * Check if this is a global role assignment
     */
    public function isGlobal(): bool
    {
        return is_null($this->scope_type) && is_null($this->scope_id);
    }

    /**
     * Check if this is a scoped role assignment
     */
    public function isScoped(): bool
    {
        return !$this->isGlobal();
    }
}