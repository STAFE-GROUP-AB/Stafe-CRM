<?php

namespace App\Events;

use App\Models\Deal;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DealStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Deal $deal,
        public string $previousStatus,
        public string $newStatus
    ) {}
}
