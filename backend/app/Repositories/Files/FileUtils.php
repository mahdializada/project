<?php

namespace App\Repositories\Files;

use FuncInfo;
use Exception;
use App\Models\FileView;
use App\Models\FileShare;
use App\Models\FileActivity;
use App\Models\FileDownload;
use App\Models\PersonalFile;
use Illuminate\Support\Carbon;

use App\Events\PersonalFileChange;
use App\Models\FileLimitionForUsers;

class FileUtils
{

    static public function storeFileView($userId, $viewable)
    {
        try {
            $viewableType = get_class($viewable);

            $fileView = new FileView();
            $fileView->viewable_id = $viewable->id;
            $fileView->viewable_type = $viewableType;
            $fileView->user_id = $userId;
            $fileView->created_at = Carbon::now();
            $fileShare = FileShare::where('shared_to', $userId)->where('shareable_id', $viewable->id)->first();
            if ($fileShare) {
                $fileShare->seen = true;
                $fileShare->save();
            }
            return $fileView->save();
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }
    }

    static public function insertFileDownload($userId, $downloadable)
    {
        try {
            $downloadableType = get_class($downloadable);

            $fileDownload = new FileDownload();
            $fileDownload->downloadable_type = $downloadableType;
            $fileDownload->downloadable_id = $downloadable->id;
            $fileDownload->user_id  = $userId;
            return $fileDownload->save();
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }
    }


    static public function getFolderBreadcrumbs($currentFolder, $parentFolder, $class, $homePath)
    {
        try {
            $breadcrumbs = [
                self::setBreadcrumbs($currentFolder, $homePath)
            ];
            if ($parentFolder) {
                $breadcrumbs[] = self::setBreadcrumbs($parentFolder, $homePath);
                $parentId = $parentFolder->parent_id;
                while ($parentId) {
                    $parentFolder =   $class::where("id", $parentId)->where("type", "folder")
                        ->select(["id", "name", "parent_id"])->first();
                    if ($parentFolder) {
                        $breadcrumbs[] = self::setBreadcrumbs($parentFolder, $homePath);
                        $parentId = $parentFolder->parent_id;
                    } else {
                        break;
                    }
                }
            }
            $breadcrumbs[] =  [
                "to"    => "/files/personal/$homePath",
                "exact" => false,
                "text"  => $homePath,
            ];
            return array_reverse($breadcrumbs);
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage(), 1);
        }
    }
    static public function getFavoriteFolderBreadcrumbs($currentFolder, $parentFolder, $class, $homePath)
    {
        try {
            $breadcrumbs = [
                self::setBreadcrumbs($currentFolder, $homePath)
            ];
            if ($parentFolder) {
                $breadcrumbs[] = self::setBreadcrumbs($parentFolder, $homePath);
                $parentId = $parentFolder->parent_id;
                while ($parentId) {

                    $parentFolder =   $class::where("id", $parentId)->where("type", "folder")
                        ->select(["id", "name", "parent_id"])->first();
                    if ($parentFolder) {
                        $conditions = [
                            ["action", "=", "favorites"],
                            ["activityable_id", "=", $parentId],
                            ["activityable_type", "=", $class],
                        ];
                        $is_inFavorites = FileActivity::where($conditions)->exists();
                        if ($is_inFavorites) {
                            $breadcrumbs[] = self::setBreadcrumbs($parentFolder, $homePath);
                        }
                        $parentId = $parentFolder->parent_id;
                    } else {
                        break;
                    }
                }
            }
            $breadcrumbs[] =  [
                "to"    => "/files/personal/$homePath",
                "exact" => false,
                "text"  => $homePath,
            ];
            return array_reverse($breadcrumbs);
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage(), 1);
        }
    }

    static  private function setBreadcrumbs($folder, $homePath)
    {
        return [
            "to" => "/files/personal/$homePath/$folder->id",
            "exact" => false,
            "text" => $folder->name
        ];
    }

    static public function checkIfParentisShared($parent_id, $user_id, $class)
    {
        $fileShare = FileShare::where('shareable_id', $parent_id)->where('shared_to', $user_id)->with('shareable')->first();
        if ($fileShare) {
            return ['result' => true, 'permission' => $fileShare->permission, 'fileShare' => $fileShare];
        } else {
            $file = $class::where('id', $parent_id)->first();
            if ($file) {
                if ($file->parent_id == null) {
                    return ['result' => false];
                } else {
                    return self::checkIfParentisShared($file->parent_id, $user_id, $class);
                }
            } else {
                return ['result' => false];
            }
        }
    }

    static public  function checkIfIsSharedBy($file_id, $user_id, $class)
    {
        $fileShare = FileShare::where('shareable_id', $file_id)->where('shared_by', $user_id)->first();
        if ($fileShare) {
            return true;
        } else {
            $file = $class::where('id', $file_id)->first();
            if ($file) {
                if ($file->parent_id == null) {
                    return false;
                } else {
                    return self::checkIfIsSharedBy($file->parent_id, $user_id, $class);
                }
            } else {
                return false;
            }
        }
    }


    static public function getParentShares($parent_id, $class, $temp)
    {
        $fileShare = FileShare::where('shareable_id', $parent_id)->get();
        if ($fileShare->count() > 0) {
            $temp = $temp->merge($fileShare);
        }
        if ($parent_id == null) {
            $col = $fileShare->merge($temp);
            return ['result' => $col->count() > 0, 'fileShares' => $col];
        } else {
            $file = $class::where('id', $parent_id)->first();
            if ($file) {
                return self::getParentShares($file->parent_id, $class, $temp);
            } else {
                return ['result' => false];
            }
        }
    }

    static public function sendDeleteRealTime($items, $user_id)
    {
        foreach ($items as $item) {
            $fileShares = FileUtils::getParentShares($item['id'], PersonalFile::class, collect());
            if ($fileShares['result']) {
                foreach ($fileShares['fileShares'] as $key => $fileShare) {
                    if ($fileShare->shared_to !== $user_id) {
                        PersonalFileChange::dispatch(
                            $fileShare->shared_to,
                            [
                                'item_id' => $item['id'],
                                'parent_id' => $item['parent_id'],
                                'type' => $item['type'],
                            ],
                            "delete-item"
                        );
                    }
                    if ($key == 0 && $fileShare->shared_by !== $user_id) {
                        PersonalFileChange::dispatch(
                            $fileShare->shared_by,
                            [
                                'item_id' => $item['id'],
                                'parent_id' => $item['parent_id'],
                                'type' => $item['type'],
                            ],
                            "delete-item"
                        );
                    }
                }
            }
        }
    }


    static public function sendCreateRenameRealTime($item, $action, $user_id, $old_parent = null)
    {
        $fileShares = FileUtils::getParentShares($item->id, PersonalFile::class, collect());
        if ($fileShares['result']) {
            foreach ($fileShares['fileShares'] as $key => $fileShare) {
                if ($fileShare->shared_to !== $user_id) {
                    PersonalFileChange::dispatch(
                        $fileShare->shared_to,
                        [
                            'item_id' => $item->id,
                            'name' =>  $item->name,
                            'updated_at' => $item->updated_at,
                            'parent_id' => $item->parent_id,
                            'type' => $item->type,
                            'old_parent' => $old_parent
                        ],
                        "$action-item"
                    );
                }
                if ($key == 0 && $fileShare->shared_by !== $user_id) {
                    PersonalFileChange::dispatch(
                        $fileShare->shared_by,
                        [
                            'item_id' => $item->id,
                            'name' =>  $item->name,
                            'updated_at' => $item->updated_at,
                            'parent_id' => $item->parent_id,
                            'type' => $item->type,
                            'old_parent' => $old_parent
                        ],
                        "$action-item"
                    );
                }
            }
        }
    }


    static public function sendUpdateRealTime($parent_id, $user_id)
    {
        $personal = new Personal();
        $parents = collect();
        $personal->getItemParents2($parent_id, function ($item) use ($parents) {
            if ($item) {
                $parents[] = $item;
            }
        });
        $fileShares = FileUtils::getParentShares($parent_id, PersonalFile::class, collect());

        foreach ($parents as $parent) {
            if ($fileShares['result']) {
                foreach ($fileShares['fileShares'] as $key => $fileShare) {
                    self::dispatchUpdate($parent, $fileShare->shared_to);
                    if ($key == 0) {
                        self::dispatchUpdate($parent, $fileShare->shared_by);
                    }
                }
            } else {
                self::dispatchUpdate($parent, $user_id);
            }
        }
    }

    static public function dispatchUpdate($item, $user_id)
    {
        PersonalFileChange::dispatch(
            $user_id,
            [
                "id"                => $item->id,
                "parent_id"         => $item->parent_id,
                "type"              => $item->type,
                "info"              => $item->info,
                "comments_count"    => $item->comments_count,
                "views_count"       => $item->views_count,
                "downloads_count"   => $item->downloads_count,
                "description"       => $item->description,
            ],
            "update-item"
        );
    }

    static public function fileSizeCalculate($file, $LimitQurey, $user_id, $delete = false)
    {

        if ($delete) {
            $fileSize =  $LimitQurey->used_size -  $file->size;
        } else {
            $fileSize =  $LimitQurey->used_size +  $file->size;
        }
        $imageSize = $LimitQurey->images_used;
        $videoSize = $LimitQurey->videos_used;
        $audiosSize = $LimitQurey->audios_used;
        $documentSize = $LimitQurey->documents_used;
        $fileShareSize = $LimitQurey->share_files_used;


        if ($file->sharedBy_id != null && $file->sharedBy_id != $file->user_id) {
            if ($delete) {
                $fileShareSize = $fileShareSize - $file->size;
            } else {
                $fileShareSize = $fileShareSize + $file->size;
            }
        } else {
            if (str_contains($file->mime_type, "image")) {
                if ($delete) {
                    $imageSize = $imageSize - $file->size;
                } else {
                    $imageSize = $imageSize + $file->size;
                }
            } elseif (str_contains($file->mime_type, "video")) {
                if ($delete) {
                    $videoSize = $videoSize - $file->size;
                } else {
                    $videoSize = $videoSize + $file->size;
                }
            } elseif (str_contains($file->mime_type, "audio")) {
                if ($delete) {
                    $audiosSize = $audiosSize - $file->size;
                } else {
                    $audiosSize = $audiosSize + $file->size;
                }
            } elseif (str_contains($file->mime_type, "application")) {
                if ($delete) {
                    $documentSize = $documentSize - $file->size;
                } else {
                    $documentSize = $documentSize + $file->size;
                }
            }
        }

        FileLimitionForUsers::where('user_id', $user_id)->first()->update([
            "used_size"          => $fileSize,
            'videos_used'        => $videoSize,
            'images_used'        => $imageSize,
            'audios_used'        => $audiosSize,
            'documents_used'     => $documentSize,
            'share_files_used'    => $fileShareSize
        ]);
    }
}
