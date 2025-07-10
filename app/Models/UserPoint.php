<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_points',
        'points_this_month',
        'points_this_quarter',
        'points_this_year',
        'lifetime_points',
        'current_level',
        'points_to_next_level',
        'last_activity_at',
    ];

    protected $casts = [
        'total_points' => 'integer',
        'points_this_month' => 'integer',
        'points_this_quarter' => 'integer',
        'points_this_year' => 'integer',
        'lifetime_points' => 'integer',
        'current_level' => 'integer',
        'points_to_next_level' => 'integer',
        'last_activity_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function addPoints(int $points): void
    {
        $this->total_points += $points;
        $this->lifetime_points += $points;
        
        // Add to period-specific points
        $now = now();
        if ($this->last_activity_at?->month !== $now->month || $this->last_activity_at?->year !== $now->year) {
            $this->points_this_month = $points;
        } else {
            $this->points_this_month += $points;
        }
        
        if ($this->last_activity_at?->quarter !== $now->quarter || $this->last_activity_at?->year !== $now->year) {
            $this->points_this_quarter = $points;
        } else {
            $this->points_this_quarter += $points;
        }
        
        if ($this->last_activity_at?->year !== $now->year) {
            $this->points_this_year = $points;
        } else {
            $this->points_this_year += $points;
        }
        
        $this->last_activity_at = $now;
        
        // Check for level up
        $this->checkLevelUp();
        
        $this->save();
    }

    public function checkLevelUp(): void
    {
        $pointsForNextLevel = $this->calculatePointsForLevel($this->current_level + 1);
        
        while ($this->total_points >= $pointsForNextLevel) {
            $this->current_level++;
            $pointsForNextLevel = $this->calculatePointsForLevel($this->current_level + 1);
        }
        
        $this->points_to_next_level = $pointsForNextLevel - $this->total_points;
    }

    public function calculatePointsForLevel(int $level): int
    {
        // Simple exponential formula: level * 100 * 1.5^(level-1)
        // Level 1: 100 points
        // Level 2: 300 points
        // Level 3: 675 points
        // etc.
        return (int) round($level * 100 * pow(1.5, $level - 1));
    }

    public function getLevelProgress(): array
    {
        $currentLevelPoints = $this->calculatePointsForLevel($this->current_level);
        $nextLevelPoints = $this->calculatePointsForLevel($this->current_level + 1);
        $pointsInCurrentLevel = $this->total_points - $currentLevelPoints;
        $pointsNeededForLevel = $nextLevelPoints - $currentLevelPoints;
        
        return [
            'current_level' => $this->current_level,
            'points_in_level' => $pointsInCurrentLevel,
            'points_needed' => $pointsNeededForLevel,
            'progress_percentage' => $pointsNeededForLevel > 0 ? round(($pointsInCurrentLevel / $pointsNeededForLevel) * 100) : 100,
        ];
    }

    public function getRank(): int
    {
        return static::where('total_points', '>', $this->total_points)->count() + 1;
    }
}