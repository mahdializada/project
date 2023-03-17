<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SystemFile extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = [
        'id', "name",  'type', 'mime_type', "path", "thumbnail",
        'size', "icon", "dimensions", 'description',
        'created_by', "updated_by", "company_id", "sub_system_id", "extension"
    ];

    // protected $appends = ['info'];

    protected $casts = [
        'dimensions' => 'array',
    ];

    public function changedBy()
    {
        return $this->belongsToMany(User::class, "user_id");
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function relateable()
    {
        return $this->morphTo();
    }


    public function getPathAttribute($value)
    {
        if ($value) {
            if (filter_var($value, FILTER_VALIDATE_URL) || str_contains($value, env("APP_URL"))) {
                return $value;
            }
            return env("APP_URL") . Storage::url($value);
        }
        return null;
    }

    public function getThumbnailAttribute($value)
    {
        if ($value) {
            if (filter_var($value, FILTER_VALIDATE_URL) || str_contains($value, env("APP_URL"))) {
                return $value;
            }
            return env("APP_URL") . Storage::url($value);
        }
        return null;
    }

    public function getInfoAttribute()
    {
        $folderFiles = SystemFile::where("type", "file")->sum("size");
        $folderFilesCount = SystemFile::where("type", "file")->count();
        return [
            "files" => (int) $folderFilesCount,
            "size" => (int) $folderFiles
        ];
    }
}
