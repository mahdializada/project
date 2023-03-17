<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class RequestStorage extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = ['user_id', 'code', 'amount', 'size', 'type', 'status','approved_by'];
    protected static $types = ["pending", "approved", "rejected"];

    public static function getTypes()
    {
        return RequestStorage::$types;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->select('id', 'firstname', 'lastname', 'image');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, "approved_by");
    }
}
