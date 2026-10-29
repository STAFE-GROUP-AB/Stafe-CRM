<?php

namespace Database\Factories;

use App\Models\PipelineStage;
use Illuminate\Database\Eloquent\Factories\Factory;

class PipelineStageFactory extends Factory
{
    protected $model = PipelineStage::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Lead', 'Qualified', 'Proposal', 'Negotiation', 'Won', 'Lost']),
            'slug' => $this->faker->slug(2),
            'description' => $this->faker->sentence,
            'order' => $this->faker->numberBetween(1, 10),
            'default_probability' => $this->faker->numberBetween(10, 100),
            'color' => $this->faker->hexColor,
            'is_active' => true,
            'is_closed' => false,
            'is_won' => false,
        ];
    }
}
