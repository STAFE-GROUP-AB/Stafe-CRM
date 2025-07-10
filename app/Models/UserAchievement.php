<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAchievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'achievement_id',
        'criteria_met',
        'points_earned',
        'earned_at',
    ];

    protected $casts = [
        'criteria_met' => 'array',
        'points_earned' => 'integer',
        'earned_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($userAchievement) {
            // Award points to user when achievement is earned
            $userPoints = UserPoint::firstOrCreate(['user_id' => $userAchievement->user_id]);
            $userPoints->addPoints($userAchievement->points_earned);
        });
    }
}