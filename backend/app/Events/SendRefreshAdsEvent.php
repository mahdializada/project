<?php

namespace App\Events;

use App\Repositories\UserRepository;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class SendRefreshAdsEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
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
            return new PrivateChannel("refresh-ads.$id");
        })->toArray();
        return $channels;
    }
}
