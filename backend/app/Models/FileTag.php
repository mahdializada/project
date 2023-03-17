<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileTag extends Model
{
    use HasFactory;

    protected $fillable = ["taggable_id", "taggable_type", "tag_id"];

    public $timestamps = false;

    public function tag()
    {
        return $this->belongsTo(Tag::class, 'tag_id');
    }

    /**
     * Get the parent taggable model
     */
    public function taggable()
    {
        return $this->morphTo();
    }
}
