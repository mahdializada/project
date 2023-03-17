<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'notification_id',
        'seen',
        'seen_at',
        'title_args',
        'content_args',
        'meta'
    ];

    protected $casts = [
        'title_args' => 'array',
        'content_args' => 'array',
        'meta' => 'array'
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }
    public function sender()
    {
        return $this->belongsTo(User::class, "sender_id")->select(['id', 'code', 'firstname', 'lastname', 'image']);
    }
    public function receiver()
    {
        return $this->belongsTo(User::class, "receiver_id")->select(['id', 'code', 'firstname', 'lastname', 'image']);
    }
}
