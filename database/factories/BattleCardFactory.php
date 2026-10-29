<?php

namespace Database\Factories;

use App\Models\BattleCard;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BattleCardFactory extends Factory
{
    protected $model = BattleCard::class;

    public function definition(): array
    {
        $title = $this->faker->company . ' Battle Card';

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'competitor_name' => $this->faker->company,
            'competitor_logo' => null,
            'overview' => $this->faker->paragraph(3),
            'our_strengths' => $this->faker->randomElements([
                'Better pricing',
                'Superior support',
                'More features',
                'Easier integration',
                'Stronger security',
            ], 3),
            'our_weaknesses' => $this->faker->randomElements([
                'Smaller market presence',
                'Fewer integrations',
                'Less brand recognition',
            ], 2),
            'competitor_strengths' => $this->faker->randomElements([
                'Large customer base',
                'Established brand',
                'More sales reps',
            ], 2),
            'competitor_weaknesses' => $this->faker->randomElements([
                'Higher pricing',
                'Slower support',
                'Complex implementation',
            ], 2),
            'key_differentiators' => $this->faker->randomElements([
                'AI-powered analytics',
                'Real-time collaboration',
                'Mobile-first design',
            ], 2),
            'pricing_comparison' => null,
            'feature_comparison' => null,
            'objection_handling' => [
                [
                    'objection' => 'Your product is too expensive',
                    'response' => 'Our total cost of ownership is actually lower when you factor in...',
                ],
                [
                    'objection' => 'We already use a competitor',
                    'response' => 'Many of our customers switched from that solution because...',
                ],
            ],
            'winning_strategies' => [
                'Focus on ROI calculation',
                'Offer extended trial period',
                'Demonstrate integration capabilities',
            ],
            'recent_wins' => null,
            'recent_losses' => null,
            'win_rate' => $this->faker->randomFloat(2, 30, 80),
            'sales_notes' => $this->faker->paragraph,
            'resources' => null,
            'threat_level' => $this->faker->randomElement(['low', 'medium', 'high', 'critical']),
            'status' => $this->faker->randomElement(['draft', 'active', 'archived']),
            'created_by' => User::factory(),
            'updated_by' => null,
            'last_updated_at' => now(),
            'view_count' => 0,
            'usage_count' => 0,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'archived',
        ]);
    }

    public function criticalThreat(): static
    {
        return $this->state(fn (array $attributes) => [
            'threat_level' => 'critical',
        ]);
    }

    public function highThreat(): static
    {
        return $this->state(fn (array $attributes) => [
            'threat_level' => 'high',
        ]);
    }
}
