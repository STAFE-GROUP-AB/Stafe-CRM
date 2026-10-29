<?php

namespace Tests\Feature\Livewire;

use App\Livewire\WorkflowBuilderEnhanced;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class WorkflowBuilderEnhancedTest extends TestCase
{
    public function test_renders_successfully()
    {
        Livewire::test(WorkflowBuilderEnhanced::class)
            ->assertStatus(200);
    }
}
