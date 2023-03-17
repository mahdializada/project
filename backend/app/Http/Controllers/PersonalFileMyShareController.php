<?php

namespace App\Http\Controllers;

use App\Models\FileShare;
use App\Models\PersonalFile;
use Illuminate\Http\Request;
use App\Events\PersonalFileChange;
use App\Models\FileLimitionForUsers;
use App\Repositories\Files\FileUtils;

class PersonalFileMyShareController extends Controller
{
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
        $shared_files = FileShare::where('shared_by', $user->id)
            ->whereHas('shareable', function ($query) {
                $query->where('personal_files.deleted_at', null);
            })
            ->with([
                'shareable',
                'shareable.createdBy',
                "shareable.favorite",
                "shareable.createdBy:id,firstname,lastname",
                'shareable.fileShares:id,shareable_id,shareable_type,shared_to,permission',
                'shareable.fileShares.sharedTo:id,firstname,lastname,image',
            ])
            ->groupBy('shareable_id')
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
        $user = $request->user();
        $layout = FileLimitionForUsers::where('user_id', $user->id)->first();
        if ($request->query->has('for_real_time')) {
            return $this->getForRealTime($request, $id);
        }
        $parent = PersonalFile::where('id', $id)->first();
        if ($parent) {
            $result = FileUtils::checkIfIsSharedBy($parent->id, $user->id, PersonalFile::class);
            if ($result) {
                $files = PersonalFile::where('parent_id', $parent->id)->with([
                    'createdBy',
                    "favorite",
                    "createdBy:id,firstname,lastname",
                    'fileShares:id,shareable_id,shareable_type,shared_to,permission',
                    'fileShares.sharedTo:id,firstname,lastname,image',
                ])
                    ->orderBy("type", "DESC")
                    ->orderBy("name", "ASC")
                    ->get();
                $parentFolder = PersonalFile::where("id", $parent->parent_id)
                    ->where("type", "folder")
                    ->select(["id", "name",  "parent_id"])->first();
                $breadcrumb = FileUtils::getFolderBreadcrumbs($parent, $parentFolder, PersonalFile::class, 'my_shares');
                FileUtils::storeFileView($user->id, $parent);
                return response()->json([
                    'files' => $files,
                    'parent' => $parent,
                    'breadcrumb' => $breadcrumb,
                    "sort" => $layout->default_sorting,
                ]);
            } else {
                return response()->json(['files' => 'not_allowed'], 403);
            }
        }
        return response()->json(['files' => 'not_found'], 404);
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
    public function destroy(Request $request)
    {
        $items = $request->items;
        $ids = array_map(function ($item) {
            return $item["id"];
        }, $items);
        $user = $request->user();
        $fileShares = FileShare::where('shared_by', $user->id)
            ->whereIn('shareable_id', $ids)
            ->with('shareable:id,user_id,parent_id,type')
            ->select('id', 'shareable_id', 'shareable_type', 'shared_by', 'shared_to',);
        $files = $fileShares->get();
        $fileShares->delete();
        $deletedItems = [];
        foreach ($files->toArray() as $fileShare) {
            $deletedItems[$fileShare['shareable']['id']] = $fileShare['shareable'];
            PersonalFileChange::dispatch(
                $fileShare['shared_to'],
                [
                    'item_id' => $fileShare['shareable']['id'],
                    'parent_id' => $fileShare['shareable']['parent_id'],
                    'type' => $fileShare['shareable']['type'],
                ],
                "unshare-item"
            );
        }

        return response()->json([
            "result" => true,
            "items" => array_values($deletedItems),
        ]);
    }
}
