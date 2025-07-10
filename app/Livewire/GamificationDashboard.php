<?php

namespace App\Livewire;

use App\Models\Achievement;
use App\Models\UserAchievement;
use App\Models\UserPoint;
use App\Models\Leaderboard;
use App\Models\LeaderboardEntry;
use App\Models\User;
use Livewire\Component;

class GamificationDashboard extends Component
{
    public $selectedPeriod = 'monthly';
    public $selectedLeaderboard = 'points';

    public function render()
    {
        $user = auth()->user();
        
        // Get user points and level info
        $userPoints = UserPoint::firstOrCreate(['user_id' => $user->id]);
        $levelProgress = $userPoints->getLevelProgress();
        
        // Get recent achievements
        $recentAchievements = UserAchievement::with('achievement')
            ->where('user_id', $user->id)
            ->orderBy('earned_at', 'desc')
            ->limit(5)
            ->get();
        
        // Get available achievements (not yet earned)
        $earnedAchievementIds = $recentAchievements->pluck('achievement_id');
        $availableAchievements = Achievement::where('is_active', true)
            ->whereNotIn('id', $earnedAchievementIds)
            ->orderBy('points', 'asc')
            ->limit(6)
            ->get();
        
        // Get leaderboard data
        $leaderboard = $this->getLeaderboardData();
        
        // Get user's rank
        $userRank = $userPoints->getRank();
        
        // Get achievements by category
        $achievementsByCategory = Achievement::where('is_active', true)
            ->get()
            ->groupBy('category');
        
        // Get user's achievement counts by category
        $userAchievementCounts = UserAchievement::where('user_id', $user->id)
            ->join('achievements', 'user_achievements.achievement_id', '=', 'achievements.id')
            ->selectRaw('achievements.category, COUNT(*) as count')
            ->groupBy('achievements.category')
            ->pluck('count', 'category');

        return view('livewire.sales-enablement.gamification-dashboard', [
            'userPoints' => $userPoints,
            'levelProgress' => $levelProgress,
            'recentAchievements' => $recentAchievements,
            'availableAchievements' => $availableAchievements,
            'leaderboard' => $leaderboard,
            'userRank' => $userRank,
            'achievementsByCategory' => $achievementsByCategory,
            'userAchievementCounts' => $userAchievementCounts,
        ]);
    }

    public function getLeaderboardData()
    {
        switch ($this->selectedLeaderboard) {
            case 'points':
                return $this->getPointsLeaderboard();
            case 'deals_closed':
                return $this->getDealsLeaderboard();
            case 'revenue_generated':
                return $this->getRevenueLeaderboard();
            default:
                return $this->getPointsLeaderboard();
        }
    }

    private function getPointsLeaderboard()
    {
        $field = match ($this->selectedPeriod) {
            'daily' => 'total_points', // Simplified for demo
            'weekly' => 'total_points', // Simplified for demo
            'monthly' => 'points_this_month',
            'quarterly' => 'points_this_quarter',
            'yearly' => 'points_this_year',
            'all_time' => 'lifetime_points',
            default => 'points_this_month'
        };

        return UserPoint::with('user')
            ->where($field, '>', 0)
            ->orderBy($field, 'desc')
            ->limit(10)
            ->get()
            ->map(function ($userPoint, $index) use ($field) {
                return [
                    'rank' => $index + 1,
                    'user' => $userPoint->user,
                    'score' => $userPoint->$field,
                    'type' => 'points',
                ];
            });
    }

    private function getDealsLeaderboard()
    {
        $startDate = $this->getPeriodStartDate();
        
        return User::withCount(['deals' => function ($query) use ($startDate) {
                $query->where('status', 'won')
                      ->where('actual_close_date', '>=', $startDate);
            }])
            ->having('deals_count', '>', 0)
            ->orderBy('deals_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($user, $index) {
                return [
                    'rank' => $index + 1,
                    'user' => $user,
                    'score' => $user->deals_count,
                    'type' => 'deals',
                ];
            });
    }

    private function getRevenueLeaderboard()
    {
        $startDate = $this->getPeriodStartDate();
        
        return User::selectRaw('users.*, COALESCE(SUM(deals.value), 0) as total_revenue')
            ->leftJoin('deals', function ($join) use ($startDate) {
                $join->on('users.id', '=', 'deals.owner_id')
                     ->where('deals.status', '=', 'won')
                     ->where('deals.actual_close_date', '>=', $startDate);
            })
            ->groupBy('users.id')
            ->having('total_revenue', '>', 0)
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($user, $index) {
                return [
                    'rank' => $index + 1,
                    'user' => $user,
                    'score' => $user->total_revenue,
                    'type' => 'revenue',
                ];
            });
    }

    private function getPeriodStartDate()
    {
        return match ($this->selectedPeriod) {
            'daily' => now()->startOfDay(),
            'weekly' => now()->startOfWeek(),
            'monthly' => now()->startOfMonth(),
            'quarterly' => now()->startOfQuarter(),
            'yearly' => now()->startOfYear(),
            'all_time' => now()->subYears(10),
            default => now()->startOfMonth()
        };
    }

    public function checkAchievements()
    {
        $user = auth()->user();
        $achievements = Achievement::where('is_active', true)->get();
        $newAchievements = [];

        foreach ($achievements as $achievement) {
            // Skip if user already has this achievement and it's not repeatable
            if (!$achievement->is_repeatable && 
                $user->achievements()->where('achievement_id', $achievement->id)->exists()) {
                continue;
            }

            if ($achievement->checkCriteria($user)) {
                $userAchievement = $achievement->awardToUser($user);
                $newAchievements[] = $userAchievement;
            }
        }

        if (!empty($newAchievements)) {
            session()->flash('new_achievements', $newAchievements);
            $this->dispatch('achievement-earned');
        } else {
            session()->flash('message', 'No new achievements at this time. Keep up the great work!');
        }
    }

    public function updatedSelectedPeriod()
    {
        // Trigger re-render when period changes
    }

    public function updatedSelectedLeaderboard()
    {
        // Trigger re-render when leaderboard type changes
    }
}