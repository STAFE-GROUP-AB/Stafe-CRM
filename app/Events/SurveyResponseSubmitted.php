<?php

namespace App\Events;

use App\Models\SurveyResponse;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SurveyResponseSubmitted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public SurveyResponse $response
    ) {}
}
