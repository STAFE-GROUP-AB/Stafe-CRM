<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PipelineStage;

class PipelineStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stages = [
            [
                'name' => 'Lead',
                'slug' => 'lead',
                'description' => 'Initial contact with potential customer',
                'color' => '#6B7280',
                'order' => 1,
                'default_probability' => 10,
                'is_active' => true,
                'is_closed' => false,
                'is_won' => false,
            ],
            [
                'name' => 'Qualified',
                'slug' => 'qualified',
                'description' => 'Lead has been qualified as a potential customer',
                'color' => '#3B82F6',
                'order' => 2,
                'default_probability' => 25,
                'is_active' => true,
                'is_closed' => false,
                'is_won' => false,
            ],
            [
                'name' => 'Proposal',
                'slug' => 'proposal',
                'description' => 'Proposal has been sent to the customer',
                'color' => '#8B5CF6',
                'order' => 3,
                'default_probability' => 50,
                'is_active' => true,
                'is_closed' => false,
                'is_won' => false,
            ],
            [
                'name' => 'Negotiation',
                'slug' => 'negotiation',
                'description' => 'Negotiating terms with the customer',
                'color' => '#F59E0B',
                'order' => 4,
                'default_probability' => 75,
                'is_active' => true,
                'is_closed' => false,
                'is_won' => false,
            ],
            [
                'name' => 'Closed Won',
                'slug' => 'closed-won',
                'description' => 'Deal has been won',
                'color' => '#10B981',
                'order' => 5,
                'default_probability' => 100,
                'is_active' => true,
                'is_closed' => true,
                'is_won' => true,
            ],
            [
                'name' => 'Closed Lost',
                'slug' => 'closed-lost',
                'description' => 'Deal has been lost',
                'color' => '#EF4444',
                'order' => 6,
                'default_probability' => 0,
                'is_active' => true,
                'is_closed' => true,
                'is_won' => false,
            ],
        ];

        foreach ($stages as $stage) {
            if (!PipelineStage::where('slug', $stage['slug'])->exists()) {
                PipelineStage::create($stage);
            }
        }
    }
}
