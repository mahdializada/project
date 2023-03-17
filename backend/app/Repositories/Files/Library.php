<?php


namespace App\Repositories\Files;

use Exception;
use App\Traits\FileTrait;
use App\Models\LibraryFile;
use Illuminate\Support\Str;
use App\Models\FileActivity;
use App\Models\PersonalFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use STS\ZipStream\ZipStreamFacade;
use Illuminate\Support\Facades\Hash;


class Library
{

    use FileTrait;


    public function updateDescriptions($item_id, $description)
    {
        $user_id = auth()->id();
        $shared_result = FileUtils::checkIfParentisShared($item_id, $user_id, LibraryFile::class);
        $query = LibraryFile::where("id", $item_id);
        if (!$shared_result["result"]) {
            $query = $query->where("user_id", $user_id);
        }

        $item = $query->first();
        if ($item) {
            $description = is_null($description) ? null : $description;
            $item->update(["description" => $description]);
            // FileUtils::sendUpdateRealTime($item->id, $user_id);
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
                $updatedItems[] = $updatedItem;
            }
            DB::commit();
            return $updatedItems;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new Exception($th->getMessage(), 1);
        }
    }


    public function insertActivity($type, $item_id, $action, $user_id)
    {
        $selectedItem = LibraryFile::where("type", $type)->where("id", $item_id)->first();
        if ($selectedItem) {
            $fileActivity =   FileActivity::where("user_id", $user_id)
                ->where("activityable_type", LibraryFile::class)
                ->where("activityable_id", $selectedItem->id)
                ->where("action", $action)->first();

            if ($fileActivity) {
                $fileActivity->delete();
            } else {
                FileActivity::create([
                    "activityable_type" => LibraryFile::class,
                    "activityable_id" => $selectedItem->id,
                    "action" => $action,
                    "user_id" => $user_id,
                ]);
            }
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



    private function childItems($folder_id)
    {
        $child_items_temp = LibraryFile::with("children")->where("parent_id", $folder_id)->get();
        $child_items = $child_items_temp->all();
        foreach ($child_items_temp as $child_item) {
            if ($child_item->children()->exists()) {
                $item_children = $child_item->children->all();
                $child_items = array_merge($child_items, $item_children);
                $this->childItems($child_item->id);
            }
        }
        return $child_items;
    }


    public function streamSingleFolder($folder_id)
    {
        $current_folder = LibraryFile::where("type", "folder")->where("id", $folder_id)->first();
        if ($current_folder) {
            FileUtils::insertFileDownload(auth()->id(), $current_folder);
            $folder_childs = $this->childItems($folder_id);
            $file_paths = [];
            $folder_name = $current_folder->name;
            foreach ($folder_childs as $folder_child) {
                if ($folder_child->type != 'folder') {
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
        $library_file = LibraryFile::withTrashed()
            ->where("type", "file")->where("id", $file_id)->first();
        if ($library_file) {
            $file_path = $library_file->getRawOriginal("path");
            if (!$this->existsFile($file_path)) {
                return response()->json(["not_found" => true]);
            }

            $header = [
                'Cache-Control'                 => 'must-revalidate, post-check=0, pre-check=0',
                'Content-Type'                  => 'application/octet-stream',
                'Content-Length'                => $library_file->size,
                'Content-Disposition'           => 'attachment; filename="' . $library_file->name . '"',
                'Pragma'                        => 'public',
            ];

            $callback = function () use ($file_path) {
                $fileStream = $this->readFileAsStream($file_path);
                fpassthru($fileStream);
                if (is_resource($fileStream)) {
                    fclose($fileStream);
                }
            };
            FileUtils::insertFileDownload(auth()->id(), $library_file);
            return response()->stream($callback, 200, $header);
        }
        return response()->json(["not_found" => true]);
    }

    public function streamFiles($item_ids)
    {
        $items = LibraryFile::whereIn("id", $item_ids)->get();
        $downloadable_files = [];
        foreach ($items as $item) {
            FileUtils::insertFileDownload(auth()->id(), $item);
            if ($item->type == "folder") {
                $folder_childs = $this->childItems($item->id);
                foreach ($folder_childs as $folder_child) {
                    if ($folder_child->type != 'folder') {
                        FileUtils::insertFileDownload(auth()->id(), $folder_child);
                        $file_path = $folder_child->getRawOriginal("path");
                        $file_name = null;
                        if ($folder_child->parent) {
                            $parent_name = $folder_child->parent->name;
                            $file_name = $parent_name . Str::after($file_path, $parent_name);
                        } else {
                            $file_name = $item->name . Str::after($file_path, $item->name);
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
        $folder_childs = $this->childItems($parent_folder->id);
        if (count($folder_childs) > 0) {
            $child_ids = array_map(fn ($item) => $item->id, $folder_childs);
            return $query->whereIn("id", $child_ids);
        }
        return null;
    }

    public function searchFavorites($searchTerm, $auth, $parent_id)
    {
        $query = LibraryFile::whereHas("favorite");
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
        $query = PersonalFile::onlyTrashed();
        if ($parent_id) {
            $query = $this->searchInChildren($query, $parent_id);
            if ($query == null) return [];
        }
        $query = $this->searchItem($searchTerm, $query);
        $result =  $query->get();
        return $result;
    }

    public function searchDefault($searchTerm, $auth, $parent_id)
    {
        $query = LibraryFile::query();
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
        $selectedItem  = LibraryFile::find($item_id);
        if ($selectedItem->user_id != auth()->id()) {
            throw new Exception("unauthorized", 403);
        }
        $password = Hash::make($password);
        $selectedItem->password = $password;
        $selectedItem->save();
        return $selectedItem;
    }


    public function softDeleteItems(array $items, $auth)
    {
        try {
            DB::beginTransaction();
            foreach ($items as $item) {
                if ($item["type"] == 'folder') {
                    $folder = LibraryFile::where("type", "folder")->where("id", $item["id"])->first();
                    $child_items = $this->childItems($folder->id);
                    $child_item_ids = array_map(fn ($itemTem) => $itemTem->id, $child_items);
                    LibraryFile::whereIn("id", $child_item_ids)->delete();
                    $folder->delete();
                } else {
                    $file = LibraryFile::where("type", "file")->where("id", $item["id"])->first();
                    $file->delete();
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new Exception($th->getMessage(), 1);
        }
    }
}
