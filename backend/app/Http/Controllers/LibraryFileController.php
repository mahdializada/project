<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\LibraryFile;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use App\Repositories\Files\Library;
use App\Http\Controllers\Controller;
use App\Models\PersonalFile;
use App\Repositories\QueryBuilder\UriQueryBuilder;

class LibraryFileController extends Controller
{


    public function index(Request $request)
    {
        $request->validate(["company_id" => ["required", "exists:companies,id"]]);
        $company = Company::where("id", $request->company_id)->select('id', "name")->first();
        $library_items = LibraryFile::orderBy("type", "DESC")->orderBy("name", "ASC")
            ->where("company_id", $request->company_id)->paginate(500000000);
        $data = $library_items->items();
        $total = $library_items->total();
        // $total = LibraryFile::where("company_id", $request->company_id)->count();
        $result = [
            "data" => $data,
            "total" => $total,
            "company" => $company,
        ];
        return response()->json($result);
    }

    public function fileDetails($id)
    {
        $file = LibraryFile::with(['comments', 'comments.replies', 'comments.replies.user:id,firstname,lastname,image', 'comments.user:id,firstname,lastname,image'])->find($id);

        if (isset($file))
            return response()->json(["result" => true, "file" => $file], Response::HTTP_OK);
        else
            return response()->json(["result" => false, "file" => null], Response::HTTP_NOT_FOUND);
    }

    public function downloadFileOrFolder(Request $request)
    {
        try {
            $request->validate([
                "items.*.id" => ["required"],
                "items.*.type" => ["required"],
            ]);

            $items = $request->input("items");
            $library = new Library();

            if (count($items) == 1) {
                $item = $items[0];
                if ($item["type"] == "folder") {
                    return $library->streamSingleFolder($item["id"]);
                } else {
                    return $library->streamSingleFile($item["id"]);
                }
            } else {
                $item_ids = [];
                foreach ($items as $item)
                    $item_ids[] = $item["id"];
                return $library->streamFiles($item_ids);
            }
        } catch (\Throwable $th) {
            return response()->json(["error" => true]);
        }
    }


    public function renameFile(Request $request)
    {
        $request->validate([
            "file_name" => ["required", "min:1"],
            "file_id" => ["required", "uuid"],
        ]);
        $fileName = $this->checkFileName($request->file_name, $request->user()->id);
        $fileItem = LibraryFile::where("created_by", $request->user()->id)
            ->where("type", "file")->where("id", $request->file_id)->first();
        $fileItem->update(["name" => $fileName]);

        $response = [
            'result' => true,
            'file' => [
                "id" => $fileItem->id,
                "name" => $fileItem->name,
            ],
        ];

        return response()->json($response);
    }


    private function checkFileName($name, $authUser)
    {
        $record = LibraryFile::withTrashed()->where('name', $name)
            ->select(['id', 'name'])
            ->where('created_by', $authUser)
            ->exists();
        if ($record) {
            $extension = pathinfo($name, PATHINFO_EXTENSION);
            $name = pathinfo($name, PATHINFO_FILENAME);
            return $name . ' ' . Carbon::now()->format('Y-m-d H-i-s') . '.' . $extension;
        }
        return $name;
    }


    public function createOrRenameFolder(Request $request)
    {
        $request->validate(["folder_name" => ["required", "min:1"]]);
        $folder = $this->checkFolderName($request);
        $folderItem = null;
        if ($request->folder_id) {
            $folderItem = LibraryFile::where("created_by", $request->user()->id)
                ->where("type", "folder")->where("id", $request->folder_id)->first();
            $folderItem->update($folder);
            $response = [
                'result' => true,
                'folder' => [
                    "id" => $folderItem->id,
                    "name" => $folderItem->name,
                ],
            ];
            return response()->json($response);
        } else {
            $folderItem = LibraryFile::create($folder);
            if ($request->ignore_details)
                $folderItem = ["id" => $folderItem->id, "name" => $folderItem->name];
            else
                $folderItem->load("favorite", "createdBy:id,firstname,lastname");
        }
        return response()->json(['result' => true, 'folder' => $folderItem]);
    }


    private function checkFolderName(Request $request)
    {
        $isExists = false;
        $folderName = $request->folder_name;
        $folderId = $request->folder_id;
        $parentId = $request->parent_id;
        $folderQuery = LibraryFile::withTrashed()->where("type", "folder")
            ->where("createdby", $request->user()->id)->where("name", $folderName);

        if ($folderId) {
            if ($parentId) {
                $isExists = $folderQuery->where("id", "!=", $folderId)->where("parent_id", $parentId)->exists();
            } else {
                $isExists = $folderQuery->where("id", "!=", $folderId)->where("type", "folder")->exists();
            }
        } else {
            if ($parentId) {
                $isExists = $folderQuery->where("type", "folder")->where("parent_id", $parentId)->exists();
            } else {
                $isExists = $folderQuery->where("type", "folder")->exists();
            }
        }
        if ($isExists) {
            $folderName = $folderName . " " .  Carbon::now()->format("Y-m-d H-i-s");
        }

        $attributes = [
            "name" => Str::lower($folderName),
            "type" => "folder",
            "parent_id" => $parentId,
            "created_by" => $request->user()->id,
        ];
        return $attributes;
    }



    public function toggleActivities(Request $request)
    {
        $request->validate([
            "items.*.type" => ["required"],
            "items.*.id" => ["required", "uuid"],
            "action" => ["required"],
        ]);

        $allowedActivities = ["favorites", "pin", "update"];
        $action = $request->action;
        if (in_array($action, $allowedActivities)) {
            $library = new Library();
            $items = $request->input("items");
            if (count($items) > 0) {
                $updated_items = $library->insertActivities($items, $action);
                return response()->json(["result" => true, "items" => $updated_items]);
            }
            return response()->json(["not_found" => true]);
        }
        return response()->json(["not_allowed" => true]);
    }


    public function generateLink(Request $request, $fileId)
    {
        $file = PersonalFile::find($fileId);
        if ($file) {
            $response = [
                "result" => true,
                "file" => [
                    "id" => $file->id,
                    "path" => $file->path,
                    "mime_type"=> $file->mime_type,
                ]
            ];
            return response()->json($response);
        }
        return response()->json(["not-found" => true]);
    }



    public function destroy(Request $request)
    {

        $request->validate([
            "items" => ["required", "array"],
            "items.*.id" => ["uuid", "exists:library_files,id"],
            "items.*.type" => ["required", "string"],
        ]);
        $items = $request->input("items");
        if ($items) {
            $library = new Library();
            $auth = $request->user()->id;
            $library->softDeleteItems($items, $auth);
            return response()->json(["result" => true]);
        }
        return response()->json([
            "not_found" => true,
            "error" => [
                "message" => "There is no record found with specified item",
            ],
        ]);
    }
}
