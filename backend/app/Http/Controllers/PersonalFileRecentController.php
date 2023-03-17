<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FileView;
use App\Models\PersonalFile;
use App\Repositories\Files\FileUtils;
use Illuminate\Http\Request;
use App\Models\FileLimitionForUsers;

class PersonalFileRecentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user_id= $request->user()->id;
        $layout = FileLimitionForUsers::where('user_id', $user_id)->first();
        $files = PersonalFile::with(PersonalFile::fetchableRelations())
            ->withCount('views', 'comments', 'downloads')
            ->whereHas('views')
            ->orderBy("type", "DESC")
            ->orderBy("name", "ASC")
            ->latest()
            ->limit(50)
            ->where("user_id", $request->user()->id)->get();

        $filtered_items = [];
        foreach ($files as $file) {
            if (!$file->isParentDeleted()) $filtered_items[] = $file;
        }

        $data = [
            "files"=>$filtered_items,
            "layout" =>$layout->default_view,
            "sort" =>$layout->default_sorting,
        ];
        return response()->json($data);
    }

    public function show(Request $request, $id)
    {
        $user_id= $request->user()->id;
        $layout = FileLimitionForUsers::where('user_id', $user_id)->first();
        $currentFolder = PersonalFile::where("id", $id)
            ->where("type", "folder")
            ->where("user_id", $user_id)
            ->select(["id", "name", "parent_id"])
            ->first();
        if ($currentFolder && !$currentFolder->isParentDeleted()) {

            if ($currentFolder->views->isEmpty() && !$currentFolder->isParentInRecent()) {
                return response()->json(["not_found" => true]);
            }

            $data = [];
            if (!$request->cursor) {
                $parentFolder = PersonalFile::where("id", $currentFolder->parent_id)
                    ->where("type", "folder")
                    ->select(["id", "name",  "parent_id"])->first();
                $breadcrumb = FileUtils::getFolderBreadcrumbs($currentFolder, $parentFolder, PersonalFile::class, 'recent');
                $data["breadcrumb"] = $breadcrumb;
            }
            $auth = $request->user()->id;
            $files = PersonalFile::orderBy("type", "DESC")
                ->where("parent_id", $currentFolder->id)
                ->orderBy("name", "ASC")
                ->with(PersonalFile::fetchableRelations())->withCount('views', 'comments', 'downloads')
                ->get();

            $data["files"] = $files;
            $data["parent"] = $currentFolder;
            $data["sort"] = $layout->default_sorting;
            FileUtils::storeFileView($user_id, $currentFolder);
            return response()->json($data);
        }
        return response()->json(["not_found" => true]);
    }
}
