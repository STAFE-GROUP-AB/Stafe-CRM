<?php

namespace Tests\Feature;

use App\Livewire\BattleCardAnalytics;
use App\Livewire\BattleCardsManager;
use App\Models\BattleCard;
use App\Models\BattleCardView;
use App\Models\Deal;
use App\Models\PipelineStage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BattleCardAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_battle_cards_page_loads(): void
    {
        $response = $this->get(route('sales-enablement.battle-cards'));
        $response->assertStatus(200);
    }

    public function test_battle_card_analytics_page_loads(): void
    {
        $response = $this->get(route('sales-enablement.battle-card-analytics'));
        $response->assertStatus(200);
    }

    public function test_battle_cards_component_renders(): void
    {
        Livewire::test(BattleCardsManager::class)
            ->assertStatus(200)
            ->assertSee('Battle Cards');
    }

    public function test_battle_card_analytics_component_renders(): void
    {
        Livewire::test(BattleCardAnalytics::class)
            ->assertStatus(200)
            ->assertSee('Battle Card Analytics')
            ->assertSee('Track usage and effectiveness');
    }

    public function test_view_is_recorded_when_viewing_battle_card(): void
    {
        $battleCard = BattleCard::factory()->create([
            'title' => 'Test Battle Card',
            'competitor_name' => 'Test Competitor',
            'status' => 'active',
            'created_by' => $this->user->id,
        ]);

        $this->assertEquals(0, $battleCard->view_count);
        $this->assertEquals(0, BattleCardView::count());

        Livewire::test(BattleCardsManager::class)
            ->call('viewCard', $battleCard->id);

        $battleCard->refresh();
        $this->assertEquals(1, $battleCard->view_count);
        $this->assertEquals(1, BattleCardView::count());

        $view = BattleCardView::first();
        $this->assertEquals($battleCard->id, $view->battle_card_id);
        $this->assertEquals($this->user->id, $view->user_id);
        $this->assertEquals('general_view', $view->context);
    }

    public function test_view_with_deal_context_is_recorded(): void
    {
        $pipelineStage = PipelineStage::factory()->create();
        $deal = Deal::factory()->create([
            'name' => 'Test Deal',
            'status' => 'open',
            'pipeline_stage_id' => $pipelineStage->id,
            'owner_id' => $this->user->id,
        ]);

        $battleCard = BattleCard::factory()->create([
            'title' => 'Test Battle Card',
            'competitor_name' => 'Test Competitor',
            'status' => 'active',
            'created_by' => $this->user->id,
        ]);

        Livewire::test(BattleCardsManager::class)
            ->set('selectedDealId', $deal->id)
            ->call('viewCard', $battleCard->id);

        $view = BattleCardView::first();
        $this->assertEquals($deal->id, $view->deal_id);
        $this->assertEquals('deal_context', $view->context);
    }

    public function test_share_is_recorded(): void
    {
        $battleCard = BattleCard::factory()->create([
            'title' => 'Test Battle Card',
            'competitor_name' => 'Test Competitor',
            'status' => 'active',
            'created_by' => $this->user->id,
        ]);

        Livewire::test(BattleCardsManager::class)
            ->call('shareCard', $battleCard->id, 'copy');

        $battleCard->refresh();
        $this->assertEquals(1, $battleCard->usage_count);

        $view = BattleCardView::first();
        $this->assertTrue($view->was_shared);
        $this->assertEquals('copy', $view->shared_via);
    }

    public function test_most_viewed_analytics(): void
    {
        $battleCard1 = BattleCard::factory()->create([
            'title' => 'Popular Card',
            'competitor_name' => 'Competitor A',
            'status' => 'active',
            'created_by' => $this->user->id,
        ]);

        $battleCard2 = BattleCard::factory()->create([
            'title' => 'Less Popular Card',
            'competitor_name' => 'Competitor B',
            'status' => 'active',
            'created_by' => $this->user->id,
        ]);

        // Create 5 views for card 1
        for ($i = 0; $i < 5; $i++) {
            BattleCardView::create([
                'battle_card_id' => $battleCard1->id,
                'user_id' => $this->user->id,
                'viewed_at' => now(),
                'context' => 'general_view',
            ]);
        }

        // Create 2 views for card 2
        for ($i = 0; $i < 2; $i++) {
            BattleCardView::create([
                'battle_card_id' => $battleCard2->id,
                'user_id' => $this->user->id,
                'viewed_at' => now(),
                'context' => 'general_view',
            ]);
        }

        $mostViewed = BattleCardView::getMostViewed(10, 30);

        $this->assertEquals(2, $mostViewed->count());
        $this->assertEquals($battleCard1->id, $mostViewed->first()->battle_card_id);
        $this->assertEquals(5, $mostViewed->first()->view_count);
    }

    public function test_effectiveness_calculation(): void
    {
        $pipelineStage = PipelineStage::factory()->create();

        $battleCard = BattleCard::factory()->create([
            'title' => 'Test Battle Card',
            'competitor_name' => 'Test Competitor',
            'status' => 'active',
            'created_by' => $this->user->id,
        ]);

        // Create deals with different statuses
        $wonDeal = Deal::factory()->create([
            'name' => 'Won Deal',
            'status' => 'won',
            'pipeline_stage_id' => $pipelineStage->id,
            'owner_id' => $this->user->id,
        ]);

        $lostDeal = Deal::factory()->create([
            'name' => 'Lost Deal',
            'status' => 'lost',
            'pipeline_stage_id' => $pipelineStage->id,
            'owner_id' => $this->user->id,
        ]);

        $openDeal = Deal::factory()->create([
            'name' => 'Open Deal',
            'status' => 'open',
            'pipeline_stage_id' => $pipelineStage->id,
            'owner_id' => $this->user->id,
        ]);

        // Create views with deal context
        BattleCardView::create([
            'battle_card_id' => $battleCard->id,
            'user_id' => $this->user->id,
            'deal_id' => $wonDeal->id,
            'viewed_at' => now(),
            'context' => 'deal_context',
        ]);

        BattleCardView::create([
            'battle_card_id' => $battleCard->id,
            'user_id' => $this->user->id,
            'deal_id' => $lostDeal->id,
            'viewed_at' => now(),
            'context' => 'deal_context',
        ]);

        BattleCardView::create([
            'battle_card_id' => $battleCard->id,
            'user_id' => $this->user->id,
            'deal_id' => $openDeal->id,
            'viewed_at' => now(),
            'context' => 'deal_context',
        ]);

        $effectiveness = BattleCardView::calculateEffectiveness($battleCard);

        $this->assertEquals(3, $effectiveness['total_deal_views']);
        $this->assertEquals(1, $effectiveness['deals_won']);
        $this->assertEquals(1, $effectiveness['deals_lost']);
        $this->assertEquals(1, $effectiveness['deals_open']);
        $this->assertEquals(50.0, $effectiveness['effectiveness_rate']); // 1 won out of 2 closed = 50%
    }

    public function test_usage_by_user_analytics(): void
    {
        $user2 = User::factory()->create();

        $battleCard = BattleCard::factory()->create([
            'title' => 'Test Battle Card',
            'competitor_name' => 'Test Competitor',
            'status' => 'active',
            'created_by' => $this->user->id,
        ]);

        // Create views for user 1
        for ($i = 0; $i < 3; $i++) {
            BattleCardView::create([
                'battle_card_id' => $battleCard->id,
                'user_id' => $this->user->id,
                'viewed_at' => now(),
                'context' => 'general_view',
            ]);
        }

        // Create views for user 2
        for ($i = 0; $i < 2; $i++) {
            BattleCardView::create([
                'battle_card_id' => $battleCard->id,
                'user_id' => $user2->id,
                'viewed_at' => now(),
                'context' => 'general_view',
            ]);
        }

        $usageByUser = BattleCardView::getUsageByUser(30);

        $this->assertEquals(2, $usageByUser->count());
        $this->assertEquals(3, $usageByUser->first()->view_count);
    }

    public function test_days_filter_on_analytics(): void
    {
        $battleCard = BattleCard::factory()->create([
            'title' => 'Test Battle Card',
            'competitor_name' => 'Test Competitor',
            'status' => 'active',
            'created_by' => $this->user->id,
        ]);

        // Create old view (45 days ago)
        BattleCardView::create([
            'battle_card_id' => $battleCard->id,
            'user_id' => $this->user->id,
            'viewed_at' => now()->subDays(45),
            'context' => 'general_view',
        ]);

        // Create recent view (5 days ago)
        BattleCardView::create([
            'battle_card_id' => $battleCard->id,
            'user_id' => $this->user->id,
            'viewed_at' => now()->subDays(5),
            'context' => 'general_view',
        ]);

        // With 30 days filter, should only see 1 view
        Livewire::test(BattleCardAnalytics::class)
            ->set('daysFilter', 30)
            ->assertSee('1'); // Should show 1 view in summary stats

        // With 60 days filter, should see 2 views
        Livewire::test(BattleCardAnalytics::class)
            ->set('daysFilter', 60)
            ->assertSee('2'); // Should show 2 views in summary stats
    }
}
