<?php

namespace App\Events;

use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SendNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $user_id;
    public $notification;
    public function __construct($user_id, $notification_id, $titleArgs, $contentArgs, $meta = [])
    {
        $this->user_id = $user_id;
        $sender = Auth::user();
        if ($this->user_id !== $sender->id) {
            $notification = UserNotification::create([
                'receiver_id' => $this->user_id,
                'sender_id' =>  $sender->id,
                'notification_id' => $notification_id,
                'title_args' => $titleArgs,
                'content_args' => $contentArgs,
                'meta' => $meta
            ]);
            $this->notification = $notification->load(
                [
                    'notification',
                    'receiver:id,firstname,lastname',
                    'sender:id,firstname,lastname'
                ]
            );
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */

    public function broadcastWhen()
    {
        return $this->user_id !== Auth::user()->id;
    }

    public function broadcastOn()
    {
        return new PrivateChannel("notifications.{$this->user_id}");
    }
}
