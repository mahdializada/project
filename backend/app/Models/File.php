<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class File extends Model
{
    use HasFactory, UuidTrait, SoftDeletes;

    protected $fillable = ["name", "type", "size", "path", "parent_id", "created_by", "description", "company_id", "sub_system_id", 'mime_type', 'thumbnail_path'];

    public function getPathAttribute($value): string
    {
        if (filter_var($value, FILTER_VALIDATE_URL) || str_contains($value, env("APP_URL"))) {
            return $value;
        }
        return env("APP_URL") . Storage::url($value);
    }

    // public function getThumbnailPathAttribute($value): string
    // {
    //     if (filter_var($value, FILTER_VALIDATE_URL) || str_contains($value, env("APP_URL"))) {
    //         return $value;
    //     }
    //     return env("APP_URL") . Storage::url($value);
    // }

    public function parent()
    {
        return $this->belongsTo(Folder::class, "parent_id");
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function tags()
    {
        return $this->HasMany(FileTag::class);
    }

    public function comments()
    {
        return $this->HasMany(FileComment::class);
    }

    /**
     * Get all of the file's downloads.
     */
    public function downloads()
    {
        return $this->morphMany(FileDownload::class, 'downloadable');
    }

    public function fileShares()
    {
        return $this->morphMany(FileShare::class, 'shareable');
    }

    /**
     * Get all of the file's views.
     */
    public function views()
    {
        return $this->morphMany(FileView::class, 'viewable');
    }

    /**
     * Get all of the file's activities.
     */
    public function activities()
    {
        return $this->morphMany(FileActivity::class, 'activityable');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function subsystem()
    {
        return $this->belongsTo(SubSystem::class);
    }
}
