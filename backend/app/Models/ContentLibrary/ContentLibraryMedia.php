<?php

namespace App\Models\ContentLibrary;

use App\Models\Favorite;
use App\Models\FileHistory;
use App\Models\FileTag;
use App\Models\Label;
use App\Models\Reason;
use App\Models\Remark;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ContentLibraryMedia extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['project_url', 'media_size','used_in_advertisement', 'content_library_id'];
    protected $appends = ['size', 'media_url'];

    protected static $types = ["rejected", 'publish','not publish', 'removed'];

    public static function getTypes()
    {
        return ContentLibraryMedia::$types;
    }

    public function contentLibrary()
    {
        return $this->belongsTo(ContentLibrary::class);
    }


    public function getSizeAttribute($value)
    {

        $size = explode(" ", $this->attributes['media_size']);
        return  $size[count($size) - 1];
    }

    public function getMediaUrlAttribute()
    {
        $value = $this->project_url;
        return strpos($value, 'https://www.canva.com') !== false ? substr($value, 0, 41) . 'view?embed' :
            $value;
    }

    public function reasonables()
    {
        return $this->morphToMany(Reason::class, 'reasonable')->withPivot('status');
    }
    public function remarks()
    {
        return $this->morphMany(Remark::class, 'remarkable');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function labels()
    {
        return $this->morphToMany(
            Label::class,
            'labelable'
        );
        # code...
    }

    // public function favorable()
    // {
    //     return $this->morphTo();
    // }
    public function favorite()
    {
        return $this->morphMany(Favorite::class, 'favorable');
    }


    public function isFavorite()
    {
        return $this->morphMany(Favorite::class, 'favorable')->where('user_id', Auth::user()->id);
    }

    public function history()
    {
        return $this->hasMany(FileHistory::class, 'media_id')->where('user_id', Auth::user()->id);
    }
}
