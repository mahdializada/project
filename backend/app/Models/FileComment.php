<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FileComment extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = ["commentable_id", "commentable_type", "comment", "user_id", "comment_id"];
    protected $appends = ['can_delete', "can_edit", "creator"];
    protected $hidden = ['commentable_id', 'commentable_type', "user_id", "user", "commentable"];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id', 'firstname', 'lastname', 'image');
    }

    protected  function getCanDeleteAttribute()
    {
        $auth = auth()->id();
        $can_delete = $this->user_id ==  $auth;
        if ($can_delete) return $can_delete;
        if ($this->commentable_type == PersonalFile::class) return $this->commentable->user_id == $auth;
        return $this->commentable->created_by == $auth;
    }

    protected  function getCanEditAttribute()
    {
        $auth = auth()->id();
        return $this->user_id ==  $auth;
    }

    protected  function getCreatorAttribute()
    {
        return [
            "name" => $this->user->firstname . " " . $this->user->lastname,
            "image" => $this->user->image,
            "id" => $this->user->id,
        ];
    }

    /**
     * Get the parent commentable model
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    public function replies()
    {
        return $this->hasMany(FileComment::class, "comment_id");
    }

    public function parent()
    {
        return $this->belongsTo(FileComment::class, "comment_id");
    }
}
