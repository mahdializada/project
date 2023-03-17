<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LibraryFile extends Model
{
    use HasFactory, UuidTrait, SoftDeletes;

    protected $fillable = [
        'id', "name", 'type', 'mime_type', "path", "thumbnail",
        'size', "icon", "password", 'read_only', 'status', "upload_type", "dimensions",
        'description',
        'created_by', "updated_by", "company_id", "parent_id", "extension", "design_request_id",
    ];

    protected $casts = [
        'dimensions' => 'array',
    ];

    protected $appends = ['isSelected'];

    public function getIsSelectedAttribute()
    {
        return false;
    }


    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, "updated_by");
    }

    public function downloads()
    {
        return $this->morphMany(FileDownload::class, 'downloadable');
    }

    public function comments()
    {
        return $this->morphMany(FileComment::class, 'commentable')->orderBy("created_at", "DESC");
    }


    public function favorite()
    {
        return $this->morphOne(FileActivity::class, 'activityable')->where("action", "favorites");
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

    public function getInfoAttribute()
    {
        $folderId = $this->id;
        $subFolderIds = LibraryFile::where("parent_id", $folderId)->where("type", "folder")
            ->select("id")->get()->pluck('id')->toArray();
        $allFolderIds = $subFolderIds;
        $allFolderIds[] = $folderId;
        if ($subFolderIds) {
            do {
                $subFolderIds = LibraryFile::whereIn("parent_id", $subFolderIds)->where("type", "folder")
                    ->select("id")->get()->pluck('id')->toArray();
                if ($subFolderIds) {
                    $allFolderIds = array_merge($subFolderIds, $allFolderIds);
                }
            } while ($subFolderIds);
        }
        $folderFiles = LibraryFile::whereIn("parent_id", $allFolderIds)->where("type", "file")->sum("size");
        $folderFilesCount = LibraryFile::whereIn("parent_id", $allFolderIds)->where("type", "file")->count();
        return [
            "files" => (int) $folderFilesCount,
            "size" => (int) $folderFiles
        ];
    }

    /**
     * Get the user that owns the LibraryFile
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(LibraryFile::class, 'parent_id');
    }

    /**
     * Get all of the comments for the LibraryFile
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(LibraryFile::class, 'parent_id');
    }

    public function fileShares()
    {
        return $this->morphMany(FileShare::class, 'shareable');
    }

    /**
     * Get the Design Request that owns the LibraryFile
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function designRequest(): BelongsTo
    {
        return $this->belongsTo(DesignRequest::class);
    }
}
