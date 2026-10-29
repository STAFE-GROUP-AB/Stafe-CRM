<?php

namespace Database\Factories;

use App\Models\Deal;
use App\Models\PipelineStage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DealFactory extends Factory
{
    protected $model = Deal::class;

    public function definition(): array
    {
        $name = $this->faker->company . ' Deal';

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1, 9999),
            'description' => $this->faker->paragraph,
            'value' => $this->faker->randomFloat(2, 1000, 100000),
            'currency' => 'SEK',
            'probability' => $this->faker->numberBetween(10, 90),
            'expected_close_date' => $this->faker->dateTimeBetween('now', '+6 months'),
            'actual_close_date' => null,
            'status' => 'open',
            'pipeline_stage_id' => PipelineStage::factory(),
            'company_id' => null,
            'contact_id' => null,
            'source' => $this->faker->randomElement(['website', 'referral', 'cold call', 'event']),
            'type' => $this->faker->randomElement(['new business', 'existing business', 'renewal']),
            'close_reason' => null,
            'custom_fields' => null,
            'owner_id' => User::factory(),
        ];
    }

    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'open',
            'actual_close_date' => null,
        ]);
    }

    public function won(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'won',
            'probability' => 100,
            'actual_close_date' => now(),
            'close_reason' => 'Customer chose our solution',
        ]);
    }

    public function lost(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'lost',
            'probability' => 0,
            'actual_close_date' => now(),
            'close_reason' => $this->faker->randomElement([
                'Price too high',
                'Chose competitor',
                'No budget',
                'Project cancelled',
            ]),
        ]);
    }
}
