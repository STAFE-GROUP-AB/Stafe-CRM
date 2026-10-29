<?php

namespace App\Events;

use App\Models\Communication;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommunicationLogged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Communication $communication
    ) {}
}
