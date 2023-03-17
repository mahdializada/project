<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PersonalFile extends Model
{
    use HasFactory, UuidTrait,  SoftDeletes;

    protected $fillable = [
        'id', "name", 'type', 'mime_type', 'path', 'thumbnail',
        'size', "icon", "password", "dimensions",
        'description',
        'user_id', "parent_id", "extension", 'sharedBy_id'
    ];

    protected $appends = ['info', 'favorites', 'isSelected', 'is_protected'];
    protected $hidden = ['password', 'favorite'];

    protected $casts = [
        'dimensions' => 'array',
    ];

    public static function fetchableRelations()
    {
        return [
            "favorite",
            "createdBy:id,firstname,lastname",
            'parent:id,name,parent_id',
            'fileShares:id,shareable_id,shareable_type,shared_to',
            'fileShares.sharedTo:id,firstname,lastname,image'
        ];
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, "user_id");
    }


    public function favorite()
    {
        return $this->morphOne(FileActivity::class, 'activityable')->where("action", "favorites");
    }

    public function comments()
    {
        return $this->morphMany(FileComment::class, 'commentable')->where("comment_id", null)->orderBy("created_at", "DESC");
    }

    public function views()
    {
        return $this->morphMany(FileView::class, 'viewable');
    }

    public function downloads()
    {
        return $this->morphMany(FileDownload::class, 'downloadable');
    }

    public function getFavoritesAttribute($value)
    {
        return $this->favorite != null;
    }

    public function getIsSelectedAttribute()
    {
        return false;
    }

    public function getIsProtectedAttribute()
    {
        if (array_key_exists("password", $this->attributes)) {
            return $this->attributes["password"] != null;
        }
        return false;
    }


    public function getThumbnailAttribute($value)
    {
        if ($value) {
            if (filter_var($value, FILTER_VALIDATE_URL) || str_contains($value, env("APP_URL"))) {
                return $value;
            }
            return env("APP_URL") . Storage::url($value);
            return Storage::disk('s3')->temporaryUrl(
                $value,
                \Carbon\Carbon::now()->addHours(2),
            );
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
            return Storage::disk('s3')->temporaryUrl(
                $value,
                \Carbon\Carbon::now()->addHours(2),
            );
        }
        return null;
    }

    public function getInfoAttribute()
    {
        // if ($this->password) return ["files" => 0, "size" => 0];
        if ($this->trashed()) {
            $folderId = $this->id;
            $subFolderIds = PersonalFile::withTrashed()->where("parent_id", $folderId)->where("type", "folder")
                ->select("id")->get()->pluck('id')->toArray();
            $allFolderIds = $subFolderIds;
            $allFolderIds[] = $folderId;
            if ($subFolderIds) {
                do {
                    $subFolderIds = PersonalFile::withTrashed()->whereIn("parent_id", $subFolderIds)->where("type", "folder")
                        ->select("id")->get()->pluck('id')->toArray();
                    if ($subFolderIds) {
                        $allFolderIds = array_merge($subFolderIds, $allFolderIds);
                    }
                } while ($subFolderIds);
            }
            $folderFiles = PersonalFile::withTrashed()->whereIn("parent_id", $allFolderIds)->where("type", "file")->sum("size");
            $folderFilesCount = PersonalFile::withTrashed()->whereIn("parent_id", $allFolderIds)->where("type", "file")->count();
            return [
                "files" => (int) $folderFilesCount,
                "size" => (int) $folderFiles
            ];
        } else {
            $folderId = $this->id;
            $subFolderIds = PersonalFile::where("parent_id", $folderId)->where("type", "folder")
                ->select("id")->get()->pluck('id')->toArray();
            $allFolderIds = $subFolderIds;
            $allFolderIds[] = $folderId;
            if ($subFolderIds) {
                do {
                    $subFolderIds = PersonalFile::whereIn("parent_id", $subFolderIds)->where("type", "folder")
                        ->select("id")->get()->pluck('id')->toArray();
                    if ($subFolderIds) {
                        $allFolderIds = array_merge($subFolderIds, $allFolderIds);
                    }
                } while ($subFolderIds);
            }
            $folderFiles = PersonalFile::whereIn("parent_id", $allFolderIds)->where("type", "file")->sum("size");
            $folderFilesCount = PersonalFile::whereIn("parent_id", $allFolderIds)->where("type", "file")->count();
            return [
                "files" => (int) $folderFilesCount,
                "size" => (int) $folderFiles
            ];
        }
    }

    public function isParentDeleted()
    {
        $parent_id = $this->parent_id;
        do {
            if ($parent_id) {
                $conditions = [["id", "=", $parent_id], ["type", "=", "folder"]];
                $parent = PersonalFile::withTrashed()->where($conditions)->first();
                if ($parent->deleted_at) return true;
                $parent_id = $parent->parent_id;
            } else return false;
        } while ($parent_id);
        return false;
    }


    public function isParentInFavorites()
    {
        $parent_id = $this->parent_id;
        do {
            if ($parent_id) {
                $conditions = [["id", "=", $parent_id], ["type", "=", "folder"]];
                $parent = PersonalFile::where($conditions)->first();
                if ($parent) {
                    if ($parent->favorites) return true;
                    $parent_id = $parent->parent_id;
                } else
                    return false;
            } else return false;
        } while ($parent_id);
        return false;
    }

    public function isParentInRecent()
    {
        $parent_id = $this->parent_id;
        do {
            if ($parent_id) {
                $conditions = [["id", "=", $parent_id], ["type", "=", "folder"]];
                $parent = PersonalFile::with("views")->where($conditions)->first();
                if ($parent) {
                    if ($parent->views->isNotEmpty()) return true;
                    $parent_id = $parent->parent_id;
                } else
                    return false;
            } else return false;
        } while ($parent_id);
        return false;
    }


    /**
     * Get the user that owns the PersonalFile
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(PersonalFile::class, 'parent_id');
    }

    /**
     * Get all of the comments for the PersonalFile
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(PersonalFile::class, 'parent_id');
    }

    public function fileShares()
    {
        return $this->morphMany(FileShare::class, 'shareable');
    }
}
