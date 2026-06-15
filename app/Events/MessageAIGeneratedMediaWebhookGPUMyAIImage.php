<?php
namespace App\Events;
use App\Helper\Helpers;
use App\Models\AIGeneratedMedia;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\Channel;
class MessageAIGeneratedMediaWebhookGPUMyAIImage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $data;
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    public function broadcastOn(): Channel
    {
        return new Channel('message-to-user.' . $this->data['generate_file']->user_id);
    }
}