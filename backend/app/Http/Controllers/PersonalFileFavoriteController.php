<?php

namespace App\Http\Controllers;

use App\Models\PersonalFile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Files\FileUtils;
use App\Models\FileLimitionForUsers;


class PersonalFileFavoriteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user_id = $request->user()->id;
        $files = PersonalFile::whereHas('favorite')
            ->with(PersonalFile::fetchableRelations())
            ->withCount('views', 'comments', 'downloads')
            ->orderBy("type", "DESC")->orderBy("name", "ASC")
            ->where("user_id", $request->user()->id)->get();

        $layout = FileLimitionForUsers::where('user_id', $user_id)->first();

        $data = [
            "files" => $files,
            "layout" => $layout->default_view,
            "sort" => $layout->default_sorting,
        ];

        return response()->json($data);
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
        $currentFolder = PersonalFile::where("id", $id)->where("user_id", $user_id)
            ->where("type", "folder")
            ->select(["id", "name", "parent_id"])->first();
        if ($currentFolder && !$currentFolder->isParentDeleted()) {
            if (!$currentFolder->favorites && !$currentFolder->isParentInFavorites()) {
                return response()->json(["not_found" => true]);
            }
            $data = [];
            $parentFolder = null;
            if ($currentFolder->parent_id) {
                $parentFolder = PersonalFile::where("id", $currentFolder->parent_id)
                    ->where("type", "folder")
                    ->select(["id", "name", "parent_id"])->first();
            }
            $breadcrumb = FileUtils::getFavoriteFolderBreadcrumbs($currentFolder, $parentFolder, PersonalFile::class, 'favorites');
            $data["breadcrumb"] = $breadcrumb;
            $files = PersonalFile::orderBy("type", "DESC")
                ->where("parent_id", $currentFolder->id)
                ->where("user_id", $request->user()->id)
                ->orderBy("name", "ASC")
                ->with(PersonalFile::fetchableRelations())
                ->withCount('views', 'comments', 'downloads')
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
