<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BattleCardView extends Model
{
    protected $fillable = [
        'battle_card_id',
        'user_id',
        'deal_id',
        'viewed_at',
        'context',
        'was_shared',
        'shared_via',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
        'was_shared' => 'boolean',
    ];

    public function battleCard(): BelongsTo
    {
        return $this->belongsTo(BattleCard::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    /**
     * Record a view of a battle card
     */
    public static function recordView(
        BattleCard $battleCard,
        User $user,
        ?Deal $deal = null,
        string $context = 'general_view'
    ): self {
        return self::create([
            'battle_card_id' => $battleCard->id,
            'user_id' => $user->id,
            'deal_id' => $deal?->id,
            'viewed_at' => now(),
            'context' => $context,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }

    /**
     * Record a share of a battle card
     */
    public static function recordShare(
        BattleCard $battleCard,
        User $user,
        string $sharedVia,
        ?Deal $deal = null
    ): self {
        return self::create([
            'battle_card_id' => $battleCard->id,
            'user_id' => $user->id,
            'deal_id' => $deal?->id,
            'viewed_at' => now(),
            'context' => 'shared',
            'was_shared' => true,
            'shared_via' => $sharedVia,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }

    /**
     * Get most viewed battle cards
     *
     * @return Collection<int, object>
     */
    public static function getMostViewed(int $limit = 10, ?int $days = 30): Collection
    {
        $query = self::select('battle_card_id', DB::raw('COUNT(*) as view_count'))
            ->with('battleCard')
            ->groupBy('battle_card_id')
            ->orderByDesc('view_count')
            ->limit($limit);

        if ($days !== null) {
            $query->where('viewed_at', '>=', now()->subDays($days));
        }

        return $query->get();
    }

    /**
     * Get view trends over time
     *
     * @return Collection<int, object>
     */
    public static function getViewTrends(int $days = 30): Collection
    {
        return self::select(
            DB::raw('DATE(viewed_at) as date'),
            DB::raw('COUNT(*) as view_count'),
            DB::raw('COUNT(DISTINCT user_id) as unique_users'),
            DB::raw('COUNT(DISTINCT battle_card_id) as unique_cards')
        )
            ->where('viewed_at', '>=', now()->subDays($days))
            ->groupBy(DB::raw('DATE(viewed_at)'))
            ->orderBy('date')
            ->get();
    }

    /**
     * Get usage by team member
     *
     * @return Collection<int, object>
     */
    public static function getUsageByUser(?int $days = 30): Collection
    {
        $query = self::select(
            'user_id',
            DB::raw('COUNT(*) as view_count'),
            DB::raw('COUNT(DISTINCT battle_card_id) as cards_viewed'),
            DB::raw('COUNT(CASE WHEN was_shared = 1 THEN 1 END) as shares_count')
        )
            ->with('user:id,name,email')
            ->groupBy('user_id')
            ->orderByDesc('view_count');

        if ($days !== null) {
            $query->where('viewed_at', '>=', now()->subDays($days));
        }

        return $query->get();
    }

    /**
     * Get recently viewed cards
     *
     * @return Collection<int, self>
     */
    public static function getRecentlyViewed(int $limit = 10, ?int $userId = null): Collection
    {
        $query = self::with(['battleCard', 'user', 'deal'])
            ->orderByDesc('viewed_at')
            ->limit($limit);

        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        return $query->get();
    }

    /**
     * Calculate effectiveness rate for a battle card
     * (Percentage of deals won after viewing the battle card)
     */
    public static function calculateEffectiveness(BattleCard $battleCard): array
    {
        $viewsWithDeals = self::where('battle_card_id', $battleCard->id)
            ->whereNotNull('deal_id')
            ->with('deal')
            ->get();

        if ($viewsWithDeals->isEmpty()) {
            return [
                'total_deal_views' => 0,
                'deals_won' => 0,
                'deals_lost' => 0,
                'deals_open' => 0,
                'effectiveness_rate' => null,
            ];
        }

        $uniqueDeals = $viewsWithDeals->pluck('deal')->unique('id');

        $dealsWon = $uniqueDeals->where('status', 'won')->count();
        $dealsLost = $uniqueDeals->where('status', 'lost')->count();
        $dealsOpen = $uniqueDeals->where('status', 'open')->count();
        $closedDeals = $dealsWon + $dealsLost;

        return [
            'total_deal_views' => $uniqueDeals->count(),
            'deals_won' => $dealsWon,
            'deals_lost' => $dealsLost,
            'deals_open' => $dealsOpen,
            'effectiveness_rate' => $closedDeals > 0
                ? round(($dealsWon / $closedDeals) * 100, 1)
                : null,
        ];
    }

    /**
     * Get overall effectiveness rates for all battle cards
     *
     * @return Collection<int, array<string, mixed>>
     */
    public static function getOverallEffectiveness(): Collection
    {
        $battleCards = BattleCard::where('status', 'active')->get();

        return $battleCards->map(function ($card) {
            $effectiveness = self::calculateEffectiveness($card);

            return [
                'battle_card' => $card,
                'effectiveness' => $effectiveness,
            ];
        })->filter(function ($item) {
            return $item['effectiveness']['total_deal_views'] > 0;
        })->sortByDesc(function ($item) {
            return $item['effectiveness']['effectiveness_rate'] ?? 0;
        })->values();
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('viewed_at', [$startDate, $endDate]);
    }

    /**
     * Scope for filtering views with deal context
     */
    public function scopeWithDealContext($query)
    {
        return $query->whereNotNull('deal_id');
    }

    /**
     * Scope for filtering shared views
     */
    public function scopeShared($query)
    {
        return $query->where('was_shared', true);
    }
}
