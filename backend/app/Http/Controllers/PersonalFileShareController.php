<?php

namespace App\Http\Controllers;

use Closure;
use Exception;
use App\Models\FileShare;
use App\Traits\FileTrait;
use App\Models\PersonalFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Repositories\Files\FileUtils;
use App\Models\FileLimitionForUsers;

class PersonalFileShareController extends Controller
{
    use FileTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user_id = $request->user()->id;
        $layout = FileLimitionForUsers::where('user_id', $user_id)->first();
        $user = $request->user();
        $shared_files = FileShare::where('shared_to', $user->id)
            ->whereHas('shareable', function ($query) {
                $query->where('personal_files.deleted_at', null);
            })
            ->with([
                'shareable',
                'shareable.createdBy',
                "shareable.favorite",
                "shareable.createdBy:id,firstname,lastname",
                'shareable.fileShares:id,shareable_id,shareable_type,shared_to',
                'shareable.fileShares.sharedTo:id,firstname,lastname,image',
            ])
            ->get();

        $filtered_files = [];
        foreach ($shared_files as $file) {
            $item = $file;
            $item->shareable = $file->shareable->loadCount(['views', 'comments', 'downloads']);
            $filtered_files[] = $item;
        }


        $data = [
            "files" => $filtered_files,
            "layout" => $layout->default_view,
            "sort" => $layout->default_sorting,
        ];

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $user_id = $request->user()->id;
        $layout = FileLimitionForUsers::where('user_id', $user_id)->first();
        if ($request->query->has('for_real_time')) {
            return $this->getForRealTime($request, $id);
        }
        $user = $request->user();
        $parent = PersonalFile::where('id', $id)->first();
        if ($parent) {
            $result = FileUtils::checkIfParentisShared($parent->id, $user->id, PersonalFile::class);
            if ($result['result']) {
                $files = PersonalFile::where('parent_id', $parent->id)->with([
                    'createdBy',
                    "favorite",
                    "createdBy:id,firstname,lastname",
                    'fileShares:id,shareable_id,shareable_type,shared_to',
                    'fileShares.sharedTo:id,firstname,lastname,image',
                ])
                    ->orderBy("type", "DESC")
                    ->orderBy("name", "ASC")
                    ->get();
                $parentFolder = PersonalFile::where("id", $parent->parent_id)
                    ->where("type", "folder")
                    ->select(["id", "name",  "parent_id"])->first();
                $breadcrumb = FileUtils::getFolderBreadcrumbs($parent, $parentFolder, PersonalFile::class, 'shared');
                FileUtils::storeFileView($user->id, $parent);
                return response()->json([
                    'files' => $files,
                    'parent' => $parent,
                    'breadcrumb' => $breadcrumb,
                    'permission' => $result['permission'],
                    "sort" => $layout->default_sorting,
                ]);
            } else {
                return response()->json(['files' => 'not_allowed'], 403);
            }
        }
        return response()->json(['files' => 'not_found'], 404);
    }

    private function getForRealTime(Request $request, $id)
    {
        $user = $request->user();
        $fileShare = FileShare::where('shareable_id', $id)
            ->where('shared_to', $user->id)
            ->with([
                "shareable",
                "shareable.createdBy",
                "shareable.favorite",
                "shareable.createdBy:id,firstname,lastname",
                "shareable.fileShares:id,shareable_id,shareable_type,shared_to",
                "shareable.fileShares.sharedTo:id,firstname,lastname,image",
            ])
            ->first();
        if ($fileShare) {
            $item = $fileShare;
            $item->shareable = $fileShare->shareable->loadCount(['views', 'comments', 'downloads']);
            return response()->json([
                'result' => true,
                'item' => $item
            ], 200);
        }
        return response()->json([
            'result' => false,
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $user = $request->user();
            FileShare::where('id', $id)
                ->where('shared_by', $user->id)
                ->orWhere('shared_to', $user->id)->delete();
            DB::commit();
            return response()->json(['result' => true, 'id' => $id]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['result' => false], 500);
        }
    }

    public function deleteFromShare(Request $request)
    {
        try {
            DB::beginTransaction();
            $action = $request->action;
            $items = $request->items;
            $delete_files = $request->delete_files;
            $user = $request->user();
            $ids = array_map(
                function ($item) {
                    return $item['id'];
                },
                $items
            );

            if ($action == 'remove-root') {
                if ($delete_files) {
                    $res = $this->deleteRootWithFiles($items, $ids, $user->id);
                    DB::commit();
                    return $res;
                } else {
                    FileShare::whereIn('shareable_id', $ids)
                        ->where('shared_to', $user->id)
                        ->delete();
                    DB::commit();
                    return response()->json([
                        'result' => true,
                        'items' => $items,
                    ]);
                }
            } else if ($action == 'remove-inside') {
                $res = $this->deleteInside($ids, $user->id);
                DB::commit();
                return $res;
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'result' => false,
                'error' => 'something_went_wrong',
            ]);
        }
    }


    private function deleteRootWithFiles($items, $ids, $user_id)
    {
        try {
            DB::beginTransaction();
            // delete files start
            $userFileIds = $this->getChildenListIds($items, $user_id, true);
            $itemsToDelete = PersonalFile::withTrashed()
                ->where("user_id", $user_id)
                ->whereIn("id", $userFileIds)
                ->select('id', 'name', 'type', 'parent_id', 'user_id', 'path', 'thumbnail', 'size', 'mime_type', "sharedBy_id")
                ->get();



            FileUtils::sendDeleteRealTime($itemsToDelete, $user_id);
            $filesDeleted = $this->deleteFileRecords($itemsToDelete, true);

            // delete files end
            // delete folders start
            $emptyUsersFolderIds = $this->getChildenListIds($items, $user_id, false);
            $itemsToDelete = PersonalFile::withTrashed()
                ->where("user_id", $user_id)
                ->whereIn("id", $emptyUsersFolderIds)
                ->select('id', 'name', 'type', 'parent_id', 'user_id', 'path', 'thumbnail', 'size', 'mime_type', 'sharedBy_id')
                ->get();
            FileUtils::sendDeleteRealTime($itemsToDelete, $user_id);
            $foldersDeleted = $this->deleteFileRecords($itemsToDelete, false);
            // delete folders end
            // delete share records start
            FileShare::whereIn('shareable_id', $ids)
                ->where('shared_to', $user_id)
                ->delete();
            // delete share records end
            DB::commit();

            return response()->json([
                'result' => true,
                'filesDeleted' => $filesDeleted,
                'foldersDeleted' => $foldersDeleted,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'result' => false,
                'error' =>  $th->getMessage(),
            ], 500);
        }
    }


    private function deleteInside($ids, $user_id)
    {
        try {
            DB::beginTransaction();
            $fileItems = PersonalFile::whereIn('id', $ids)
                ->select('id', 'name', 'type', 'parent_id', 'user_id', 'path', 'thumbnail', 'size', 'mime_type')
                // ->where('user_id', $user_id)
                ->get();
            if ($fileItems->count() > 0) {
                // delete user files start
                $userFileIds = $this->getChildenListIds($fileItems, $user_id, true);

                $itemsToDelete = PersonalFile::withTrashed()
                    ->where("user_id", $user_id)
                    ->whereIn("id", $ids)
                    ->select('id', 'name', 'type', 'parent_id', 'user_id', 'path', 'thumbnail', 'size', 'mime_type', "sharedBy_id")
                    ->get();

                FileUtils::sendDeleteRealTime($itemsToDelete, $user_id);
                $filesDeleted = $this->deleteFileRecords($itemsToDelete, true, $user_id);

                // delete user files end
                // get remaining children
                $list = collect();
                $this->getChildren(
                    $fileItems,
                    $user_id,
                    function ($child) use ($list) {
                        $list[] = $child;
                    }
                );
                // seprating deletable folders from non deletable folders
                $deletableFolderIds = [];
                $nonDeletableFolders = [];
                foreach ($list as $listItem) {
                    if (
                        $listItem['user_id'] == $user_id &&
                        $listItem['type'] == 'folder' &&
                        $listItem['info']['files'] == 0 &&
                        $listItem['info']['size'] == 0
                    ) {
                        $deletableFolderIds[] = $listItem['id'];
                    } else if (in_array($listItem['id'], $ids) && $listItem['type'] == 'folder') {
                        $nonDeletableFolders[] = $listItem;
                    }
                }
                // delete deletable folders

                $itemsToDelete = PersonalFile::withTrashed()
                    ->where("user_id", $user_id)
                    ->whereIn("id", $deletableFolderIds)
                    ->select('id', 'name', 'type', 'parent_id', 'user_id', 'path', 'thumbnail', 'size', 'mime_type', "sharedBy_id")
                    ->get();


                FileUtils::sendDeleteRealTime($itemsToDelete, $user_id);
                $foldersDeleted = $this->deleteFileRecords($itemsToDelete, false);
                DB::commit();
                return response()->json([
                    'result' => true,
                    // 'foldersDeleted' => $foldersDeleted,
                    // 'filesDeleted' => $filesDeleted,
                    'nonDeletableFolders' => $nonDeletableFolders,
                ]);
            } else {
                return response()->json([
                    'result' => true,
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'result' => false,
                'erorr' => $e->getMessage(),
            ], 500);
        }
    }


    private function getChildren($items, $user_id, Closure $callback)
    {
        $arr = [];
        foreach ($items as  $item) {
            $callback($item);
            $arr[$item['id']]['item'] = $item;
            if ($item['type'] == 'folder') {
                $res = $this->returnChildren($item, $user_id);
                if ($res->count()) {
                    $arr[$item['id']]['children'] = $this->getChildren($res->toArray(), $user_id, $callback);
                }
            }
        }
        return $arr;
    }


    private function returnChildren($item, $user_id)
    {
        $files = PersonalFile::where('parent_id', $item['id'])
            ->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id)
                    ->where('type', 'file')
                    ->orWhere('type', 'folder');
            })
            ->select('id', 'name', 'type', 'parent_id', 'user_id', 'path', 'thumbnail', 'size', 'mime_type')
            ->get();
        return $files;
    }


    private function getChildenListIds($items, $user_id, $get_file_ids = true)
    {
        $list = collect();
        $ids = [];
        $this->getChildren(
            $items,
            $user_id,
            function ($child) use ($list) {
                $list[] = $child;
            }
        );
        if ($get_file_ids) {
            foreach ($list as  $listItem) {
                if ($listItem['type'] == 'file') {
                    $ids[] = $listItem['id'];
                }
            }
        } else {
            foreach ($list as  $listItem) {
                if (
                    $listItem['parent_id'] &&
                    $listItem['user_id'] == $user_id &&
                    $listItem['info']['files'] == 0 &&
                    $listItem['info']['size'] == 0
                ) {
                    $ids[] = $listItem['id'];
                }
            }
        }
        return $ids;
    }


    private function deleteFileRecords($itemsToDelete, $isFile)
    {
        foreach ($itemsToDelete as $itemToDelete) {
            if ($isFile) {
                if (!(PersonalFile::where('path', $itemToDelete->path)->where('id', '!=', $itemToDelete->id)->exists())) {
                    $this->deleteFile($itemToDelete->getRawOriginal("path"));
                    if ($itemToDelete->thumbnail) {
                        $this->deleteFile($itemToDelete->getRawOriginal("thumbnail"));
                    }
                }

                $id = $itemToDelete->id;
                $isParentsShared = FileUtils::getParentShares($id, PersonalFile::class, collect());
                $share_id = $isParentsShared['fileShares'][0]->shared_by;
                $LimitQurey = FileLimitionForUsers::where('user_id', $share_id)->first();
                FileUtils::fileSizeCalculate($itemToDelete, $LimitQurey, $share_id, true);
            }

            $itemToDelete->comments()->delete();
            $itemToDelete->views()->delete();
            $itemToDelete->downloads()->delete();
            $itemToDelete->fileShares()->delete();
            $itemToDelete->forceDelete();
        }
        return $itemsToDelete;
    }
}
