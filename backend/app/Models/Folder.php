<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Folder extends Model
{
    use HasFactory, UuidTrait, SoftDeletes;

    protected $fillable = ["name", "parent_id", "created_by", "description", "company_id", "sub_system_id"];

    protected $appends = ['info', "type"];

    public function getTypeAttribute()
    {
        return "folder";
    }
    public function getInfoAttribute()
    {
        $folderId = $this->id;
        $subFolderIds = Folder::where("parent_id", $folderId)->select("id")->get()->pluck('id')->toArray();
        $allFolderIds = $subFolderIds;
        $allFolderIds[] = $folderId;
        if ($subFolderIds) {
            do {
                $subFolderIds = Folder::whereIn("parent_id", $subFolderIds)
                    ->select("id")->get()->pluck('id')->toArray();
                if ($subFolderIds) {
                    $allFolderIds = array_merge($subFolderIds, $allFolderIds);
                }
            } while ($subFolderIds);
        }
        $folderFiles = File::whereIn("parent_id", $allFolderIds)->sum("size");
        $folderFilesCount = File::whereIn("parent_id", $allFolderIds)->count();
        return [
            "files" => (int) $folderFilesCount,
            "size" => (int) $folderFiles
        ];
    }


    public function parent()
    {
        return $this->belongsTo(Folder::class, "parent_id");
    }






    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }
    /**
     * Get all of the folder's downloads.
     */
    public function downloads()
    {
        return $this->morphMany(FileDownload::class, 'downloadable');
    }
    /**
     * Get all of the folder's views.
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

    public function comments()
    {
        return $this->HasMany(FileComment::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function subsystem()
    {
        return $this->belongsTo(SubSystem::class);
    }

    public function files()
    {
        return $this->HasMany(File::class, 'parent_id');
    }

    public function folders()
    {
        return $this->HasMany(Folder::class, 'parent_id');
    }

    public function fileShares()
    {
        return $this->morphMany(FileShare::class, 'shareable');
    }
}
