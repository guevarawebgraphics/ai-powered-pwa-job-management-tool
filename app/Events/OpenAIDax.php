<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OpenAIDax implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public array $message)
    {
    }

    public function broadcastOn()
    {
        return new Channel('openai-dax');
    }

    public function broadcastWith()
    {
        return ['message' => $this->message];
    }

    // This method lets you assign a custom alias to your event.
    public function broadcastAs()
    {
        return 'WebRTCSignalEvent';
    }
}
