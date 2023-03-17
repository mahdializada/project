<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\PersonalFile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\FileLimitionForUsers;
use App\Traits\FileTrait;
use App\Repositories\Files\FileUtils;


use function PHPUnit\Framework\returnValue;

class PersonalFileTrashController extends Controller
{
    use FileTrait;
    public function index(Request $request)
    {
        $user_id = $request->user()->id;
        $layout = FileLimitionForUsers::where('user_id', $user_id)->first();

        $files = PersonalFile::with(
            "favorite",
            "createdBy:id,firstname,lastname",
            "fileShares"
        )->onlyTrashed()->where(function ($query) {
            $query->whereHas('parent', function ($builder) {
                $builder->whereNull('deleted_at');
            })->orWhereNull("parent_id");
        })->orderBy("type", "DESC")
            ->orderBy("name", "ASC")
            ->where("user_id", $request->user()->id)->get();
        $filtered_items = [];

        foreach ($files as $file) {
            if (!$file->isParentDeleted()) $filtered_items[] = $file;
        }

        $data = [
            "files" => $filtered_items,
            "layout" => $layout->default_view,
            "sort" => $layout->default_sorting,
        ];

        return response()->json($data);
    }


    public function destroy(Request $request)
    {
        try {
            $this->disk = 's3';
            DB::beginTransaction();
            $request->validate([
                "items" => ["required", "array"],
                "items.*.id" => ["uuid", "exists:personal_files,id"],
                "items.*.type" => ["required", "string"],
            ]);
            $items =  $request->items;
            $ids = array_map(fn ($item) => $item['id'], $items);

            $forceDeleteFolderFiles = $this->childForRestoreAndDelete($ids, auth()->user()->id);
            foreach ($forceDeleteFolderFiles as $folderFile) {
                $this->deleteDetails($folderFile);
            }

            $fileForForceDelete = PersonalFile::withTrashed()->where("user_id", auth()->user()->id)->whereIn("id", $ids)->get();
            foreach ($fileForForceDelete as $folderFile) {

                $this->deleteDetails($folderFile);
            }

            DB::commit();
            return response()->json(["result" => true]);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new Exception($th->getMessage(), 1);
        }
    }


    public function deleteDetails($folderFile)
    {
        $this->disk = 's3';
        $parent_id = $folderFile->parent_id;
        $user_id = auth()->user()->id;
        $isParentsShared = FileUtils::getParentShares($parent_id, PersonalFile::class, collect());
        if ($isParentsShared['result']) {
            $share_id = $isParentsShared['fileShares'][0]->shared_by;
        } else {
            $share_id = $user_id;
        }
        $LimitQurey = FileLimitionForUsers::where('user_id', $share_id)->first();
        FileUtils::fileSizeCalculate($folderFile, $LimitQurey, $share_id, true);

        if ($folderFile->type == 'file') {
            $this->deleteFile($folderFile->getRawOriginal("path"));
            $this->deleteFile($folderFile->getRawOriginal("thumbnail"));
        }
        $folderFile->comments()->delete();
        $folderFile->views()->delete();
        $folderFile->downloads()->delete();
        $folderFile->fileShares()->delete();
        $folderFile->forceDelete();
    }


    public function restoreFileOrFolder(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                "items" => ["required", "array"],
                "items.*.id" => ["uuid", "exists:personal_files,id"],
                "items.*.type" => ["required", "string"],
            ]);
            $items =  $request->items;
            $ids = array_map(fn ($item) => $item["id"], $items);
            $restoreFolderFiles = $this->childForRestoreAndDelete($ids, auth()->user()->id);
            foreach ($restoreFolderFiles as $folderFile) {
                $folderFile->restore();
            }
            $records = PersonalFile::with("parent")->withTrashed()->where("user_id", auth()->user()->id)->whereIn("id", $ids)->get();
            foreach ($records as $record) {
                if ($record->parent_id) {
                    $isExits = PersonalFile::withTrashed()->where("user_id", "!=", auth()->id())->where("id", $record->parent_id)->exists();
                    if ($isExits) {
                        $record->sharedBy_id = $record->user_id;
                        $record->parent_id = null;
                        $record->deleted_at = null;
                        $record->save();
                    } else {
                        $record->restore();
                    }
                } else {
                    $record->restore();
                }
            }

            DB::commit();
            return response()->json(["result" => true]);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new Exception($th->getMessage(), 1);
        }
    }

    public function childForRestoreAndDelete($ids, $user_id)
    {
        $trashedFolder = PersonalFile::withTrashed()->where("user_id", $user_id)->whereIn("id", $ids);
        $subFolderIds = $trashedFolder->get()->pluck('id');
        $allFolderIds = [];
        if ($subFolderIds) {
            do {
                $folderOrFileQuery = PersonalFile::withTrashed()->whereIn("parent_id", $subFolderIds);
                $subFolderIds = $folderOrFileQuery->select("id")->get()->pluck('id')->toArray();
                if ($subFolderIds) {
                    $allFolderIds = array_merge($allFolderIds, $subFolderIds);
                }
            } while ($subFolderIds);
        }
        $folderFiles = PersonalFile::withTrashed()->whereIn('id', $allFolderIds)->get();
        return $folderFiles;
    }
}
