<?php

namespace App\Livewire;

use App\Models\BattleCard;
use App\Models\BattleCardView;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class BattleCardAnalytics extends Component
{
    public int $daysFilter = 30;

    public function render()
    {
        return view('livewire.sales-enablement.battle-card-analytics', [
            'mostViewed' => $this->getMostViewed(),
            'viewTrends' => $this->getViewTrends(),
            'usageByUser' => $this->getUsageByUser(),
            'recentlyViewed' => $this->getRecentlyViewed(),
            'effectiveness' => $this->getEffectiveness(),
            'summaryStats' => $this->getSummaryStats(),
        ]);
    }

    public function setDaysFilter(int $days): void
    {
        $this->daysFilter = $days;
    }

    private function getMostViewed(): Collection
    {
        return BattleCardView::getMostViewed(10, $this->daysFilter);
    }

    private function getViewTrends(): Collection
    {
        return BattleCardView::getViewTrends($this->daysFilter);
    }

    private function getUsageByUser(): Collection
    {
        return BattleCardView::getUsageByUser($this->daysFilter);
    }

    private function getRecentlyViewed(): Collection
    {
        return BattleCardView::getRecentlyViewed(10);
    }

    private function getEffectiveness(): Collection
    {
        return BattleCardView::getOverallEffectiveness();
    }

    private function getSummaryStats(): array
    {
        $totalViews = BattleCardView::where('viewed_at', '>=', now()->subDays($this->daysFilter))->count();
        $uniqueUsers = BattleCardView::where('viewed_at', '>=', now()->subDays($this->daysFilter))
            ->distinct('user_id')
            ->count('user_id');
        $totalShares = BattleCardView::where('viewed_at', '>=', now()->subDays($this->daysFilter))
            ->where('was_shared', true)
            ->count();
        $activeCards = BattleCard::where('status', 'active')->count();
        $dealsWithCardViews = BattleCardView::where('viewed_at', '>=', now()->subDays($this->daysFilter))
            ->whereNotNull('deal_id')
            ->distinct('deal_id')
            ->count('deal_id');

        return [
            'total_views' => $totalViews,
            'unique_users' => $uniqueUsers,
            'total_shares' => $totalShares,
            'active_cards' => $activeCards,
            'deals_with_views' => $dealsWithCardViews,
            'avg_views_per_card' => $activeCards > 0 ? round($totalViews / $activeCards, 1) : 0,
        ];
    }
}
