<?php


namespace App\Repositories\Files;

use Exception;
use App\Models\FileShare;
use App\Traits\FileTrait;
use Illuminate\Support\Str;
use App\Models\FileActivity;
use App\Models\PersonalFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use STS\ZipStream\ZipStreamFacade;
use App\Models\FileLimitionForUsers;
use Illuminate\Support\Facades\Hash;
use App\Models\RequestStorage;

class Personal
{

    use FileTrait;

    public function updateDescriptions($item_id, $description)
    {
        $user_id = auth()->id();
        $shared_result = FileUtils::checkIfParentisShared($item_id, $user_id, PersonalFile::class);
        $query = PersonalFile::where("id", $item_id);
        if (!$shared_result["result"]) {
            $query = $query->where("user_id", $user_id);
        }

        $item = $query->first();
        if ($item) {
            $description = is_null($description) ? null : $description;
            $item->update(["description" => $description]);
            FileUtils::sendUpdateRealTime($item->id, $user_id);
            return response()->json(["result" => true]);
        }
        return response()->json(["not_found" => true], 401);
    }

    public function insertActivities(array $items, $action)
    {
        try {
            DB::beginTransaction();
            $updatedItems = [];
            $auth = auth()->user()->id;
            foreach ($items as $item) {
                $type           = $item['type'];
                $item_id        = $item["id"];
                $updatedItem    =  $this->insertActivity($type, $item_id, $action, $auth);
                if ($updatedItem) {
                    $updatedItems[] = $updatedItem;
                } else {
                    throw new Exception("unauthorized", 403);
                }
            }
            DB::commit();
            return $updatedItems;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new Exception($th->getMessage(), $th->getCode());
        }
    }


    public function insertActivity($type, $item_id, $action, $user_id)
    {
        $selectedItem = PersonalFile::where("type", $type)->where("id", $item_id)
            ->where("user_id", $user_id)->first();

        if ($selectedItem) {
            $fileActivity =   FileActivity::where("user_id", $user_id)
                ->where("activityable_type", PersonalFile::class)
                ->where("activityable_id", $selectedItem->id)
                ->where("action", $action)->first();

            if ($fileActivity) {
                $fileActivity->delete();
            } else {
                FileActivity::create([
                    "activityable_type" => PersonalFile::class,
                    "activityable_id" => $selectedItem->id,
                    "action" => $action,
                    "user_id" => $user_id,
                ]);
            }
            $selectedItem = $selectedItem->refresh();
            $result = [
                "id" => $selectedItem->id,
                "type" => $selectedItem->type,
                "name" => $selectedItem->name,
                "favorites" => $selectedItem->favorites,
                "updated" => $selectedItem->updated_at,
            ];
            return $result;
        }
        return null;
    }

    public function softDeleteItems(array $items, $auth)
    {
        $item_ids = array_map(fn ($item) => $item["id"], $items);
        foreach ($item_ids as $item_id) {
            $record = PersonalFile::findOrFail($item_id);
            if ($record->user_id == auth()->id()) {
                $record->fileShares()->delete();
                $record->delete();
            } else {
                $is_shared = FileUtils::checkIfIsSharedBy($record->id, auth()->id(), PersonalFile::class);
                if ($is_shared) {
                    $record->fileShares()->delete();
                    $record->delete();
                    return;
                }
                throw new Exception("your are unauthorized to do this action", 401);
            }
        }
    }

    public function childrenRecursive($children, \Closure $callback, $withTrashed)
    {
        foreach ($children as $child) {
            if ($withTrashed) {
                $child->load(['children' => fn ($query) => $query->withTrashed()]);
            } else {
                $child->load('children')->withTrashed();
            }
            if ($child->children) {
                $callback($child);
                $this->childrenRecursive($child->children, $callback, $withTrashed);
            }
        }
        return null;
    }


    private function childItems($folder_id, $withTrashed = false)
    {
        $children = collect();
        $query = PersonalFile::with("children")->where("id", $folder_id);
        if ($withTrashed) {
            $query = $query->withTrashed();
        }
        $folder = $query->first();
        if ($folder->children) {
            $this->childrenRecursive($folder->children, fn ($child) => $children[] = $child, $withTrashed);
        }
        return $children;
    }

    // public function parentRecursive($folder, \Closure $callback, $withTrashed)
    // {
    //     if ($withTrashed) $folder_item = $folder->load(['parent' => fn ($query) => $query->withTrashed()]);
    //     else $folder_item = $folder->load('parent');

    //     if ($folder_item->parent) {
    //         $callback($folder_item);
    //         $this->parentRecursive($folder_item->parent, $callback, $withTrashed);
    //     }
    //     return null;
    // }


    // private function parentItems($folder_id, $withTrashed = false)
    // {
    //     $parents = collect();
    //     $query = PersonalFile::with("parent")->where("id", $folder_id);
    //     if ($withTrashed) $query = $query->withTrashed();
    //     $current_folder = $query->first();
    //     if ($current_folder->parent_id) {
    //         $this->parentRecursive($current_folder, fn ($parent) => $parents[] = $parent, $withTrashed);
    //     }
    //     return $parents;
    // }



    private function checkDownloadPermissions($item_id)
    {
        $is_owner = PersonalFile::where("user_id", auth()->id())
            ->where("id", $item_id)->exists();
        if ($is_owner) return true;
        $is_item_shared = FileShare::where('shareable_id', $item_id)
            ->where('shared_to', auth()->id())->exists();
        if ($is_item_shared) return true;
        $is_parent_shared = FileUtils::checkIfParentisShared($item_id, auth()->id(), PersonalFile::class);
        if ($is_parent_shared["result"]) return true;
        $shared_result = FileUtils::checkIfIsSharedBy($item_id, auth()->id(), PersonalFile::class);
        if ($shared_result) return true;
        throw new \Exception("unauthorized", 403);
        return false;
    }
    public function streamSingleFolder($folder_id)
    {
        $current_folder = PersonalFile::where("type", "folder")->where("id", $folder_id)->first();
        if ($current_folder) {
            $this->checkDownloadPermissions($current_folder->id);
            FileUtils::insertFileDownload(auth()->id(), $current_folder);
            $folder_childs = $this->childItems($folder_id);
            $file_paths = [];
            $folder_name = $current_folder->name;
            foreach ($folder_childs as $folder_child) {
                if ($folder_child->type != 'folder') {
                    $this->checkDownloadPermissions($folder_child->id);
                    FileUtils::insertFileDownload(auth()->id(), $folder_child);
                    $file_path = $folder_child->getRawOriginal("path");
                    $fileName = Str::after($file_path, $folder_name);
                    $file_path = $this->getFullPath($file_path);
                    $file_paths[$file_path] = $fileName;
                }
            }
            return ZipStreamFacade::create("$folder_name.zip", $file_paths);
        }
        return response()->json(["not_found" => true]);
    }


    public function streamSingleFile($file_id)
    {
        $personal_file = PersonalFile::withTrashed()
            ->where("type", "file")->where("id", $file_id)->first();
        if ($personal_file) {
            $this->checkDownloadPermissions($personal_file->id);
            $file_path = $personal_file->getRawOriginal("path");
            if (!$this->existsFile($file_path)) {
                return response()->json(["not_found" => true]);
            }

            $header = [
                'Cache-Control'                 => 'must-revalidate, post-check=0, pre-check=0',
                'Content-Type'                  => 'application/octet-stream',
                'Content-Length'                => $personal_file->size,
                'Content-Disposition'           => 'attachment; filename="' . $personal_file->name . '"',
                'Pragma'                        => 'public',
            ];

            $callback = function () use ($file_path) {
                $fileStream = $this->readFileAsStream($file_path);
                fpassthru($fileStream);
                if (is_resource($fileStream)) {
                    fclose($fileStream);
                }
            };
            FileUtils::insertFileDownload(auth()->id(), $personal_file);
            return response()->stream($callback, 200, $header);
        }
        return response()->json(["not_found" => true]);
    }
    public function streamFiles($item_ids)
    {
        $items = PersonalFile::whereIn("id", $item_ids)->where("user_id", auth()->id())->get();
        $downloadable_files = [];
        foreach ($items as $item) {
            $this->checkDownloadPermissions($item->id);
            FileUtils::insertFileDownload(auth()->id(), $item);
            if ($item->type == "folder") {
                $folder_childs = $this->childItems($item->id);
                foreach ($folder_childs as $folder_child) {
                    if ($folder_child->type != 'folder') {
                        $this->checkDownloadPermissions($folder_child->id);
                        FileUtils::insertFileDownload(auth()->id(), $folder_child);
                        $file_path = $folder_child->getRawOriginal("path");
                        $file_name = null;
                        if ($folder_child->parent) {
                            $parent_name = $folder_child->parent->name;
                            $file_name = $parent_name . "/" . Str::after($file_path, $parent_name);
                        } else {
                            $file_name = $item->name .  "/" . Str::after($file_path, $item->name);
                        }
                        $file_path = $this->getFullPath($file_path);
                        $downloadable_files[$file_path] =  $file_name;
                    }
                }
            } else {
                $file_path = $item->getRawOriginal("path");
                $file_path = $this->getFullPath($file_path);
                $downloadable_files[$file_path] = $item->name;
            }
        }
        $date = Carbon::now()->timestamp;
        $file_name = "Smart Friqi $date.zip";
        return ZipStreamFacade::create($file_name, $downloadable_files);
    }


    private function searchInChildren($query, $parent_id)
    {
        $cloneQuery = clone $query;
        $parent_folder  = $cloneQuery->where("id", $parent_id)->first();
        if (!$parent_folder) return null;
        $folder_childs = $this->childItems($parent_folder->id);
        if (count($folder_childs) > 0) {
            $child_ids = array_map(fn ($item) => $item['id'], $folder_childs->toArray());
            return $query->whereIn("id", $child_ids);
        }
        return null;
    }

    public function searchFavorites($searchTerm, $auth, $parent_id)
    {
        $query = PersonalFile::whereHas("favorite")->where("user_id", $auth);
        if ($parent_id) {
            $query = $this->searchInChildren($query, $parent_id);
            if ($query == null) return [];
        }
        $query = $this->searchItem($searchTerm, $query);
        $result =  $query->get();
        return $result;
    }

    public function searchRecent($searchTerm, $auth, $parent_id)
    {
        $query = PersonalFile::whereHas("views")->where("user_id", $auth);
        if ($parent_id) {
            $query = $this->searchInChildren($query, $parent_id);
            if ($query == null) return [];
        }
        $query = $this->searchItem($searchTerm, $query);
        $result =  $query->get();
        return $result;
    }

    public function searchTrashed($searchTerm, $auth, $parent_id = null)
    {
        $query = PersonalFile::onlyTrashed()->where("user_id", $auth);
        if ($parent_id) {
            $query = $this->searchInChildren($query, $parent_id);
            if ($query == null) return [];
        }
        $query = $this->searchItem($searchTerm, $query);
        $result =  $query->get();
        return $result;
    }

    public function searchShared($searchTerm, $auth, $parent_id = null, $my_shared = false)
    {
        if ($parent_id) {
            $is_parent_shared = FileUtils::checkIfParentisShared($parent_id, $auth, PersonalFile::class);
            if (!$is_parent_shared) return [];
            $query = PersonalFile::with(PersonalFile::fetchableRelations())->withCount('views', 'comments', 'downloads');
            $query = $this->searchInChildren($query, $parent_id);
            if ($query == null) return [];
        } else {
            $shared_items = FileShare::where($my_shared ? "shared_by" : "shared_to", $auth)->select("shareable_id")->get();
            if ($shared_items->isEmpty()) return collect();
            $shared_ids = $shared_items->pluck("shareable_id")->toArray();
            $query = PersonalFile::with(PersonalFile::fetchableRelations())
                ->withCount('views', 'comments', 'downloads')
                ->whereIn("id", $shared_ids);
        }
        $query = $this->searchItem($searchTerm, $query);
        $result =  $query->get();
        return $result;
    }

    public function searchDefault($searchTerm, $auth, $parent_id)
    {
        $query = PersonalFile::where("user_id", $auth);
        if ($parent_id) {
            $query = $this->searchInChildren($query, $parent_id);
            if ($query == null) return [];
        }
        $query = $this->searchItem($searchTerm, $query);
        $result =  $query->get();
        return $result;
    }


    private function searchItem($searchTerm, $query)
    {
        $searchTerm =  '%' . $searchTerm . '%';
        $searchableColumns = ["name", "extension", "size"];
        $query->where(function ($itemQuery) use ($searchTerm, $searchableColumns) {
            foreach ($searchableColumns as $column)
                $itemQuery->orWhere($column, "like", $searchTerm);
            return $itemQuery;
        });
        return $query;
    }

    public function setPasswordItem($password, $item_id)
    {
        $selectedItem  = PersonalFile::find($item_id);
        if ($selectedItem->user_id != auth()->id()) {
            throw new Exception("unauthorized", 403);
        }
        $password = Hash::make($password);
        $selectedItem->password = $password;
        $selectedItem->save();
        return $selectedItem;
    }

    public function changePasswordItem($current_password, $new_password, $item_id)
    {
        $selectedItem  = PersonalFile::find($item_id);
        if ($selectedItem->user_id != auth()->id()) {
            throw new Exception("unauthorized", 403);
        }
        if (!Hash::check($current_password, $selectedItem->password)) {
            throw new Exception("incorrect_password", 204);
        }
        $selectedItem->password = Hash::make($new_password);
        $selectedItem->save();
        return $selectedItem;
    }
    public function clearPasswordItem($current_password, $item_id)
    {
        $selectedItem  = PersonalFile::find($item_id);
        if ($selectedItem->user_id != auth()->id()) {
            throw new Exception("unauthorized", 403);
        }
        if (!Hash::check($current_password, $selectedItem->password)) {
            throw new Exception("incorrect_password", 204);
        }
        $selectedItem->password = null;
        $selectedItem->save();
        return $selectedItem;
    }
    public function checkPasswordItem($current_password, $item_id)
    {
        $selectedItem  = PersonalFile::find($item_id);
        if ($selectedItem->user_id != auth()->id()) {
            throw new Exception("unauthorized", 403);
        }
        if (!Hash::check($current_password, $selectedItem->password)) {
            throw new Exception("incorrect_password", 204);
        }
        return $selectedItem;
    }

    public function folderChildsAsTree($root_folders, $ignore_item_ids = [])
    {
        foreach ($root_folders as $index => $folder) {
            $root_folders[$index]->setAppends([]);
            if ($folder->children()->exists()) {
                $children =  $folder->children()->where("type", "folder")
                    ->whereNotIn("id", $ignore_item_ids)
                    ->select(["id", "name", "parent_id"])->get();
                $root_folders[$index]->children = $children;
                $this->folderChildsAsTree($children, $ignore_item_ids);
            }
        }
        return $root_folders;
    }

    public function copyOrMoveItems($action, $parent_id = null, $items)
    {
        DB::beginTransaction();
        $copied_items = [];
        if ($action == 'copy') {
            foreach ($items as $item) {
                if ($item["type"] == 'folder') {
                    $copied_item = $this->copyFolder($item["id"], $parent_id);
                    $copied_items[] = $copied_item;
                } else {
                    $child_item = PersonalFile::find($item["id"]);
                    $copied_item = $this->copyItem($child_item, $parent_id, true);
                    $copied_items[] = $copied_item;
                }
            }
        } else {
            foreach ($items as $item) {
                $movied_item = $this->moveItem($item["id"], $parent_id);
                $copied_items[] = $movied_item;
            }
        }
        DB::commit();
        FileUtils::sendUpdateRealTime($parent_id, auth()->id());
        return $copied_items;
    }

    public function copyFolder($folder_id, $parent_id)
    {
        if ($folder_id == $parent_id) {
            throw new \Exception("This destination folder is a sub folder of the source folder", 402);
        }

        if ($parent_id && $this->isDistinationExistsInChild($folder_id, $parent_id)) {
            throw new \Exception("This destination folder is a sub folder of the source folder", 402);
        }

        $selected_item = PersonalFile::where("id", $folder_id)->where("type", "folder")->first();
        $conditions = true;
        $isParentsShared = FileUtils::getParentShares($parent_id, PersonalFile::class, collect());
        $isShared = $isParentsShared['result'];
        if ($isShared) {
            $share_id = $isParentsShared['fileShares'][0]->shared_by;
            if (auth()->id() !== $share_id) {
                $isDrive = false;
            }
        } else {
            $share_id = auth()->id();
        }

        $LimitQurey = FileLimitionForUsers::where('user_id', $share_id)->first();
        $limitedSize = $LimitQurey->limited_size;
        $usedSize = $LimitQurey->used_size;

        if ($limitedSize == 'unlimited') {
            $conditions = true;
        } else {
            $remainingSize = $limitedSize - $usedSize;
            $conditions = $remainingSize > $selected_item->info["size"] && $limitedSize > $usedSize;
        }

        if (!$conditions) {
            throw new \Exception("Tnot_allowed_limited_size", 405);
        }

        $auth = auth()->id();
        if ($selected_item->user_id != $auth) {
            $not_allowed = true;
            $is_item_shared = FileShare::where('shareable_id', $selected_item->id)
                ->where('shared_to', $auth)->exists();
            if ($is_item_shared)  $not_allowed = false;

            $is_parent_shared = FileUtils::checkIfParentisShared($selected_item->id, $auth, PersonalFile::class);
            if ($is_parent_shared["result"])   $not_allowed = false;

            if ($not_allowed)
                throw new \Exception("You are not authorized to do this operations", 403);
        }
        $folder_info = $this->checkFolderName($selected_item->name, null, $parent_id);
        $folder_info["password"] = $selected_item->password;
        $new_record = PersonalFile::create($folder_info);
        FileUtils::sendCreateRenameRealTime($new_record, 'create', 'none');
        $this->copyFolderChildren($selected_item->id, $new_record->id);
        return $new_record;
    }

    private function isDistinationExistsInChild($source_id, $destination_id)
    {
        $parent_folders = $this->getItemParents($destination_id);
        $found = $parent_folders->pluck("id")->contains($source_id);
        return $found;
    }



    public function moveItem($item_id, $parent_id)
    {
        $selected_item = PersonalFile::find($item_id);
        if ($item_id == $parent_id) {
            throw new \Exception("This destination folder is a sub folder of the source folder", 402);
        }
        $auth = auth()->id();
        if ($selected_item->user_id != $auth) {
            $not_allowed = true;
            $is_item_shared = FileShare::where('shareable_id', $selected_item->id)
                ->where('shared_to', $auth)->exists();
            if ($is_item_shared)  $not_allowed = false;

            $is_parent_shared = FileUtils::checkIfParentisShared($selected_item->id, $auth, PersonalFile::class);
            if ($is_parent_shared["result"])   $not_allowed = false;

            if ($not_allowed)
                throw new \Exception("You are not authorized to do this operations", 403);
        }
        $old_parent_id = $selected_item->parent_id;
        $selected_item->parent_id = $parent_id;
        $selected_item->save();
        FileUtils::sendUpdateRealTime($old_parent_id, auth()->id());
        FileUtils::sendCreateRenameRealTime($selected_item, 'move', 'none', $old_parent_id);

        return $selected_item;
    }


    private function copyFolderChildren($prev_parent_id, $new_parent_id)
    {
        $children = PersonalFile::where("parent_id", $prev_parent_id)->get();
        if (count($children) > 0) {
            foreach ($children as $child) {
                if ($child->type == 'folder') {
                    $folder_info = $this->checkFolderName($child->name, null, $new_parent_id);
                    $folder_info["password"] = $child->password;
                    $new_folder = PersonalFile::create($folder_info);
                    $this->copyFolderChildren($child->id, $new_folder->id);
                } else {
                    $this->copyItem($child, $new_parent_id, false);
                }
            }
        }
        return true;
    }

    private function copyItem($prev_item, $new_parent_id = null, $isRoot)
    {
        $conditions = true;
        $isParentsShared = FileUtils::getParentShares($prev_item->id, PersonalFile::class, collect());
        $isShared = $isParentsShared['result'];
        if ($isShared) {
            $share_id = $isParentsShared['fileShares'][0]->shared_by;
            if (auth()->id() !== $share_id) {
                $isDrive = false;
            }
        } else {
            $share_id = auth()->id();
        }

        $LimitQurey = FileLimitionForUsers::where('user_id', $share_id)->first();
        $limitedSize = $LimitQurey->limited_size;
        $usedSize = $LimitQurey->used_size;

        if ($limitedSize == 'unlimited') {
            $conditions = true;
        } else {
            $remainingSize = $limitedSize - $usedSize;
            $conditions = $remainingSize > $prev_item->size && $limitedSize > $usedSize;
        }

        if (!$conditions) {
            throw new \Exception("Tnot_allowed_limited_size", 405);
        }
        $file_name = $prev_item->name;
        $exists = PersonalFile::where("user_id", auth()->id())
            ->where("type", $prev_item->type)->where('name', $file_name)
            ->exists();
        if ($exists) {
            $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file_name);
            $file_name = $withoutExt . " " .  Carbon::now()->format("Y-m-d H-i-s") . "."  . $prev_item->extension;
        }
        $created_item =  PersonalFile::create([
            'name'          => $file_name,
            'type'          => 'file',
            'mime_type'     => $prev_item->mime_type,
            'path'          => $prev_item->getRawOriginal("path"),
            'thumbnail'     => $prev_item->getRawOriginal('thumbnail'),
            'size'          => $prev_item->size,
            'dimensions'    => $prev_item->dimensions,
            'user_id'       => auth()->id(),
            'parent_id'     => $new_parent_id,
            'extension'     => $prev_item->extension,
            "password"      => $prev_item->password,
        ]);
        if ($isRoot) {
            FileUtils::sendCreateRenameRealTime($created_item, 'create', 'none');
        }
        return $created_item;
    }


    public function checkFolderName($folder_name, $folder_id = null, $parent_id = null)
    {
        $isExists = false;
        $auth = auth()->id();
        $folderQuery = PersonalFile::withTrashed()->where("type", "folder")
            ->where("user_id", $auth)->where("name", $folder_name);

        if ($folder_id) {
            if ($parent_id) {
                $isExists = $folderQuery->where("id", "!=", $folder_id)->where("parent_id", $parent_id)->exists();
            } else {
                $isExists = $folderQuery->where("id", "!=", $folder_id)->exists();
            }
        } else {
            if ($parent_id) {
                $isExists = $folderQuery->where("parent_id", $parent_id)->exists();
            } else {
                $isExists = $folderQuery->exists();
            }
        }
        if ($isExists) {
            $folder_name = $folder_name . " " .  Carbon::now()->format("Y-m-d H-i-s");
        }

        $attributes = [
            "name" => Str::lower($folder_name),
            "type" => "folder",
            "parent_id" => $parent_id,
            "user_id" => $auth,
        ];
        return $attributes;
    }

    public function getItemParents($item_id, $with_trashed = false)
    {
        $parent_relation = [
            "parent" => function ($parent_query) use ($with_trashed) {
                if ($with_trashed) $parent_query->withTrashed();
            }
        ];
        $item =   PersonalFile::with($parent_relation)->withTrashed()->find($item_id);
        $parents = collect();

        if ($item && $item->parent) {
            $parents->push($item->parent);
            $parent_id = $item->parent_id;
            do {
                if ($parent_id) {
                    $item_parent = PersonalFile::with($parent_relation)->withTrashed()->find($parent_id);
                    if ($item_parent && $item_parent->parent_id) {
                        $parents->push($item_parent->parent);
                        $parent_id = $item_parent->parent_id;
                    } else break;
                } else break;
            } while ($parent_id);
        }
        return $parents;
    }

    public function getItemParents2($item_id, $callback)
    {
        $item = PersonalFile::where('id', $item_id)
            ->select('personal_files.id', 'personal_files.parent_id', 'personal_files.type', 'personal_files.description')
            ->withCount('views', 'comments', 'downloads')
            ->withTrashed()
            ->first();
        $callback($item);
        if ($item && $item->parent_id) {
            $this->getItemParents2($item->parent_id,  $callback);
        }
    }


    public function updateUserStorage($storage_id, $user_id, $amount, $type)
    {
        $user_storage = FileLimitionForUsers::where('user_id', $user_id)->where("id", $storage_id)->first();
        if ($user_storage) {
            $used_size = (int) $user_storage->used_size;
            if ($type == "unlimited") {
                $user_storage->limited_size = "unlimited";
                $user_storage->save();
                return ["result" => true, "amount" => "unlimited"];
            }
            if ($amount <= $used_size) return ['result' => false, "greater" => true];
            $user_storage->limited_size = $amount;
            $user_storage->save();
            return ["result" => true, "amount" => $amount];
        }
        return ["not_found" => true, 'result' => false];
    }

    public function fileCount($file, $user_id)
    {
        return PersonalFile::where("user_id", $user_id)->where("mime_type", "like", $file . "%")->count();
    }


    public function settingsPersonalFiles($user_id)
    {
        $data = FileLimitionForUsers::where("user_id", $user_id)->first();
        $others = $data->used_size - $data->images_used - $data->videos_used - $data->audios_used - $data->documents_used - $data->share_files_used;

        $imagesFilesCount = $this->fileCount("image", $user_id);
        $videoFilesCount =  $this->fileCount("video", $user_id);
        $audioFilesCount = $this->fileCount("audio", $user_id);
        $documentFilesCount = $this->fileCount("application", $user_id);
        $othersFilesCount = PersonalFile::where("user_id", $user_id)->where('type', 'not like', "folder")
            ->where("mime_type", "not like", "image%")
            ->where("mime_type", "not like", "video%")
            ->where("mime_type", "not like", "audio%")
            ->where("mime_type", "not like", "application%")
            ->count();
        $sharesFilesCount = PersonalFile::where("sharedBy_id", $user_id)->where('type', 'not like', "folder")->count();
        $storageRequest = RequestStorage::where("user_id", $user_id)->where('status', 'pending')->latest()->first();

        $data["others_used"] = (string)$others;
        $data['storageRequest'] = $storageRequest;
        $result = [
            "Alldata"        => $data,
            "imageFile"      => $imagesFilesCount,
            "videoFiles"     => $videoFilesCount,
            "audioFiles"     => $audioFilesCount,
            "documentFiles"  => $documentFilesCount,
            "otherFiles"     => $othersFilesCount,
            "sharesFiles"    => $sharesFilesCount,
        ];
        return $result;
    }
}
