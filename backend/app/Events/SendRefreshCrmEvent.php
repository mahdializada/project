<?php

namespace App\Events;

use App\Repositories\UserRepository;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendRefreshCrmEvent  implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */

    public function broadcastOn()
    {

        $users = UserRepository::getUsersByPermissions("adv", "c");
        $channels = $users->map(function ($item) {
            $id = $item["id"];
            return new PrivateChannel("refresh-crm.$id");
        })->toArray();

        return $channels;
    }
}
