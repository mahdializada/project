<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FileActivity extends Model
{
    use HasFactory, UuidTrait;

    protected $table = 'file_activities';

    protected $fillable = ["activityable_id", "activityable_type", "user_id",  "action", "created_at"];

    public static $types = ["favorites", "pin", "update"];

    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
    /**
     * Get the parent activityable model
     */
    public function activityable()
    {
        return $this->morphTo();
    }
}
