<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Models\FileShare;
use App\Traits\FileTrait;
use Illuminate\Support\Str;
use App\Models\Notification;
use App\Models\PersonalFile;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use App\Events\SendNotification;
use App\Events\PersonalFileChange;
use App\Http\Controllers\Controller;
use App\Models\FileLimitionForUsers;
use App\Repositories\Files\Personal;
use App\Repositories\Files\FileUtils;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManagerStatic as Image;

class PersonalFileController extends Controller
{
    use FileTrait;


    public function index(Request $request)
    {

        $user_id = $request->user()->id;
        $layout = FileLimitionForUsers::where('user_id', $user_id)->first();
        $files = PersonalFile::with(PersonalFile::fetchableRelations())
            ->withCount('views', 'comments', 'downloads')
            ->orderBy("type", "DESC")
            ->orderBy("name", "ASC")
            ->where("parent_id", null)
            ->where("user_id", $user_id)
            ->get();

        $data = [
            "files" => $files,
            "layout" => $layout->default_view,
            "sort" => $layout->default_sorting,
        ];
        return response()->json($data);
    }

    public function show(Request $request, $id)
    {
        $user_id = $request->user()->id;
        $layout = FileLimitionForUsers::where('user_id', $user_id)->first();
        if ($request->query->has('for_real_time')) {
            return $this->getForRealTime($request, $id);
        }
        $currentFolder = PersonalFile::where("id", $id)
            ->where("type", "folder")
            ->where("user_id", $user_id)
            ->select(["id", "name", "parent_id"])
            ->first();

        if (!$currentFolder) {
            $shared_result = FileUtils::checkIfIsSharedBy($id, $user_id, PersonalFile::class);
            if ($shared_result) {
                $currentFolder = PersonalFile::where("id", $id)->where("type", "folder")
                    ->select(["id", "name", "parent_id"])->first();
            }
        }

        if ($currentFolder && !$currentFolder->isParentDeleted()) {
            $data = [];
            $parentFolder = PersonalFile::where("id", $currentFolder->parent_id)
                ->where("type", "folder")
                ->select(["id", "name", "parent_id"])->first();
            $breadcrumb = FileUtils::getFolderBreadcrumbs($currentFolder, $parentFolder, PersonalFile::class, 'drive');
            $data["breadcrumb"] = $breadcrumb;
            $files = PersonalFile::orderBy("type", "DESC")
                ->where("parent_id", $currentFolder->id)
                ->orderBy("name", "ASC")
                ->with(PersonalFile::fetchableRelations())
                ->withCount('views', 'comments', 'downloads')
                ->get();

            $data["files"]  = $files;
            $data["parent"] = $currentFolder;
            $data["sort"]   = $layout->default_sorting;

            FileUtils::storeFileView($user_id, $currentFolder);
            return response()->json($data);
        }

        return response()->json(["not_found" => true]);
    }

    private function getForRealTime(Request $request, $id)
    {
        $user = $request->user();
        $file = PersonalFile::where('id', $id)
            ->with(PersonalFile::fetchableRelations())
            ->withCount('views', 'comments', 'downloads')
            ->first();
        if ($file) {
            $res = FileUtils::checkIfParentisShared($file->parent_id, $user->id, PersonalFile::class);
            $res2 = false;
            if (!$res['result']) {
                $res2 = FileUtils::checkIfIsSharedBy($file->id, $user->id, PersonalFile::class);
            }
            if ($file->user_id == $user->id || $res['result'] || $res2) {
                return response()->json([
                    'result' => true,
                    'item' => $file
                ], 200);
            }
            return response()->json([
                'result' => false,
                'unauthorized' => true,
            ], 403);
        }
        return response()->json([
            'result' => false,
        ], 404);
    }


    public function createOrRenameFolder(Request $request)
    {

        $request->validate(["folder_name" => ["required", "min:1", "max:250"]]);
        $folder = $this->checkFolderName($request);
        $user = $request->user();
        $folderItem = null;
        if ($request->folder_id) {
            $folderItem = PersonalFile::where("user_id", $user->id)
                ->where("type", "folder")
                ->where("id", $request->folder_id)
                ->first();


            if ($folderItem) {
                $chechSharePem = FileUtils::checkIfParentisShared(
                    $folderItem->id,
                    $user->id,
                    PersonalFile::class,
                );

                if ($chechSharePem["result"] && $chechSharePem['permission'] != 'read_&_write') {
                    return response()->json(["unauthorized" => true], 401);
                }

                if ($user->id != $folderItem->user_id)
                    return response()->json(["unauthorized" => true], 401);

                $folderItem->update($folder);
                // real time start
                FileUtils::sendCreateRenameRealTime($folderItem, 'rename', $user->id);
                // real time finish
                $response = [
                    'result' => true,
                    'folder' => [
                        "id"         => $folderItem->id,
                        "name"       => $folderItem->name,
                        'updated_at' => $folderItem->updated_at,
                    ],
                ];
                return response()->json($response);
            }
            return response()->json(["unauthorized" => true], 401);
        } else {

            $chechSharePem = FileUtils::checkIfParentisShared(
                $folder["parent_id"],
                $user->id,
                PersonalFile::class,
            );

            if ($chechSharePem["result"] && $chechSharePem['permission'] != 'read_&_write') {
                return response()->json(["unauthorized" => true], 401);
            }
            $folder['sharedBy_id'] = $chechSharePem["result"] ? $chechSharePem["fileShare"]->shared_by : $user->id;
            $folderItem = PersonalFile::create($folder);
            $folderItem->load(PersonalFile::fetchableRelations())->loadCount(['views', 'comments', 'downloads']);

            FileUtils::sendCreateRenameRealTime($folderItem, 'create', $user->id);
        }
        return response()->json(['result' => true, 'folder' => $folderItem]);
    }

    public function renameFile(Request $request)
    {
        $user = $request->user();
        $request->validate([
            "file_name" => ["required", "min:1"],
            "file_id"   => ["required", "uuid"],
        ]);
        $fileItem = PersonalFile::where("user_id", $request->user()->id)
            ->where("type", "file")->where("id", $request->file_id)->first();
        if ($fileItem) {
            $fileName = $this->checkFileName($request->file_name, $fileItem->parent_id);
            $fileItem->update(["name" => $fileName]);
            $response = [
                'result'    => true,
                'file'      => [
                    "id"    => $fileItem->id,
                    "name"  => $fileItem->name,
                ],
            ];

            FileUtils::sendCreateRenameRealTime($fileItem, 'rename', $user->id);

            return response()->json($response);
        }
        return response()->json(["unauthorized" => true], 401);
    }

    private function checkFileName($name, $parent_id)
    {
        $record = PersonalFile::withTrashed()
            ->where('name', $name)
            ->select(['id', 'name'])
            ->where('parent_id', $parent_id)
            ->exists();
        if ($record) {
            $extension = pathinfo($name, PATHINFO_EXTENSION);
            $name = pathinfo($name, PATHINFO_FILENAME);
            return $name . ' ' . Carbon::now()->format('Y-m-d H-i-s') . '.' . $extension;
        }
        return $name;
    }

    private function checkFolderName(Request $request)
    {
        $isExists       = false;
        $folderName     = $request->folder_name;
        $folderId       = $request->folder_id;
        $parentId       = $request->parent_id;
        $auth           = $request->user()->id;
        $folderQuery    = PersonalFile::withTrashed()
            ->where("type", "folder")
            ->whereRaw('lower(`name`) = ?', Str::lower($folderName));

        if ($folderId) {
            if ($parentId) {
                $parentUserId = PersonalFile::withTrashed()->where("id", $parentId)
                    ->select("user_id")->first()->user_id;
                $isExists = $folderQuery->where("user_id", $parentUserId)
                    ->where("id", "!=", $folderId)->where("parent_id", $parentId)->exists();
            } else {
                $isExists = $folderQuery->where("user_id", $auth)
                    ->where("id", "!=", $folderId)->where("type", "folder")->exists();
            }
        } else {
            if ($parentId) {
                $parentUserId = PersonalFile::withTrashed()->where("id", $parentId)
                    ->select("user_id")->first()->user_id;
                $isExists = $folderQuery->where("user_id", $parentUserId)->where("type", "folder")
                    ->where("parent_id", $parentId)->exists();
            } else {
                $isExists = $folderQuery->where("user_id", $auth)
                    ->where("type", "folder")->exists();
            }
        }
        if ($isExists) {
            if (strlen($folderName) > 225) {
                $folderName = substr($folderName, 0, -20);
            }
            $folderName = $folderName . " " .  Carbon::now()->format("Y-m-d H-i-s");
        }
        $attributes = [
            "name"      => $folderName,
            "type"      => "folder",
            "parent_id" => $parentId,
            "user_id"   => $request->user()->id,
        ];
        return $attributes;
    }

    public function bulkUploadFolder(Request $request)
    {
        $user = $request->user();
        $parent_id = $request->parent_id;
        $parent = PersonalFile::select(['id', 'name', 'parent_id'])
            ->where('id', $parent_id)
            ->first();
        $responseArr = [];
        foreach ($request->folders as $key => $value) {
            $chunk_id = $key;
            foreach ($value as $folder) {
                $parent_item = array_key_exists('parent_temp_id', $folder) ?
                    $responseArr[$folder['parent_temp_id']] : ($parent ? $parent : null);
                $parent_id =  $parent_item ? $parent_item->id :  null;

                $folderName =  $this->returnNameFolder($folder['name'], $parent_id);

                $chechSharePem = FileUtils::checkIfParentisShared(
                    $parent_id,
                    $user->id,
                    PersonalFile::class,
                );

                if ($chechSharePem["result"] && $chechSharePem['permission'] != 'read_&_write') {
                    return response()->json(["unauthorized" => true], 401);
                }

                $folderCreated = PersonalFile::create([
                    'name'          => $folderName,
                    'type'          => 'folder',
                    'user_id'       => $user->id,
                    'sharedBy_id'   => $chechSharePem['result'] ? $chechSharePem['fileShare']->shared_by : $user->id,
                    'parent_id'     => $parent_id,
                ]);

                $folderCreated->load(PersonalFile::fetchableRelations());

                FileUtils::sendCreateRenameRealTime($folderCreated, 'create', $user->id);

                $responseArr[$folder['id']] = $folderCreated;
            }
        }
        return response()->json([
            'folders' => $responseArr,
            'folders_chunk' => $chunk_id
        ], Response::HTTP_CREATED);
    }

    public function store(Request $request)
    {
        // $this->disk = 's3';
        try {
            $isDrive = true;
            $parent_id = $request->parent_id;
            $user = $request->user();
            $file = $request->file('file');
            $isParentsShared = FileUtils::getParentShares($parent_id, PersonalFile::class, collect());
            $isShared = $isParentsShared['result'];
            if ($isShared) {
                $share_id = $isParentsShared['fileShares'][0]->shared_by;
                if ($user->id !== $share_id) {
                    $isDrive = false;
                }
            } else {
                $share_id = $user->id;
            }

            $LimitQurey = FileLimitionForUsers::where('user_id', $share_id)->first();
            $limitedSize = $LimitQurey->limited_size;
            $usedSize = $LimitQurey->used_size;

            if ($limitedSize == 'unlimited') {
                $conditions = true;
            } else {
                $remainingSize = $limitedSize - $usedSize;
                $conditions = $remainingSize >  $file->getSize() && $limitedSize > $usedSize;
            }

            if (!$conditions) return response()->json(["not_allowed_limited_size" => true], 403);

            $parent = PersonalFile::select(['id', 'name', 'parent_id', 'user_id', 'sharedBy_id'])
                ->where('id', $parent_id)
                ->first();

            $chechSharePem = FileUtils::checkIfParentisShared(
                $parent ? $parent->id : null,
                $user->id,
                PersonalFile::class
            );

            // check if user id is equal to parent user_id or it is shared to user
            if (
                $parent == null ||
                $user->id == $parent->user_id ||
                ($chechSharePem['result'] && $chechSharePem['permission'] == 'read_&_write') ||
                $parent->sharedBy_id == $user->id
            ) {
                $this->prefix = $this->generatePath($parent, $parent ? $parent->user_id : $user->id);
                $new_id = Str::uuid()->toString();
                $name = $this->returnNameFile(
                    new PersonalFile(),
                    $file,
                    $parent ? $parent->id : null,
                    $user->id
                );

                $path = $this->storeFileAs($file, $new_id . '.' . pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
                $dimensions = '';
                $thumbnail = '';
                // if is image
                if (in_array($file->getMimeType(), $this->imageTypes)) {
                    $dimentions = getimagesize($file);
                    $dimensions = ['width' => $dimentions[0], 'height' =>  $dimentions[1]];
                    $image = Image::make($file)->fit(280, 120, function ($constraint) {
                        $constraint->upsize();
                    });
                    $thumbnail = $this->storeImageAsFile($image, '280x120' .  $new_id . '.' . pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
                }

                $file = PersonalFile::create([
                    'id'            => $new_id,
                    'name'          => $name,
                    'type'          => 'file',
                    'mime_type'     => $file->getMimeType(),
                    'path'          => $path,
                    'thumbnail'     => in_array($file->getMimeType(), $this->iconTpye) ? $path : $thumbnail,
                    'size'          => $file->getSize(),
                    'dimensions'    => $dimensions,
                    'user_id'       => $user->id,
                    'parent_id'     => $parent ? $parent->id : null,
                    'extension'     => pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION),
                    "sharedBy_id"   => $isShared ? $share_id : $user->id,
                ]);

                FileUtils::fileSizeCalculate($file, $LimitQurey, $share_id, false);

                FileUtils::sendCreateRenameRealTime($file, 'create', $user->id);
                $parent ? FileUtils::sendUpdateRealTime($parent->id, $user->id) : null;
                $file->is_drive = $isDrive;
                return response()->json(
                    $file->load(PersonalFile::fetchableRelations()),
                    Response::HTTP_CREATED
                );
            } else {
                return response()->json(
                    ["not_allowed" => true],
                    403
                );
            }
        } catch (Exception $exception) {
            return  $exception->getMessage();
        }
    }

    private function returnNameFolder($name, $parent_id)
    {
        $record = PersonalFile::withTrashed()
            ->whereRaw('lower(`name`) = ?', Str::lower($name))
            ->select(['id', 'name'])
            ->where('parent_id', $parent_id)
            ->first();
        if ($record) {
            return
                $name .
                ' ' .
                Carbon::now()->format('Y-m-d H-i-s');
        }
        return $name;
    }


    public function destroy(Request $request)
    {
        $request->validate([
            "items"         => ["required", "array"],
            "items.*.id"    => ["uuid", "exists:personal_files,id"],
            "items.*.type"  => ["required", "string"],
        ]);
        try {
            $items = $request->input("items");
            if ($items) {
                $personal = new Personal();
                $auth = $request->user()->id;
                FileUtils::sendDeleteRealTime($items, $auth);
                $personal->softDeleteItems($items, $auth);
                list($first_item) = $items;
                FileUtils::sendUpdateRealTime($first_item["parent_id"], $auth);
                $response = [
                    "result" => true,
                ];
                return response()->json($response);
            }
        } catch (\Throwable $th) {
            if ($th->getCode() == 401)
                return response()->json(["unauthorized" => true]);
            return response()->json([
                "not_found" => true,
                "error" => [
                    "message" => "There is no record found with specified id",
                ],
            ]);
        }
    }

    public function toggleActivities(Request $request)
    {
        $request->validate([
            "items.*.type"  => ["required"],
            "items.*.id"    => ["required", "uuid", "exists:personal_files,id"],
            "action"        => ["required"],
        ]);

        try {
            $allowedActivities = ["favorites", "pin", "update"];
            $action = $request->action;
            if (in_array($action, $allowedActivities)) {
                $personal = new Personal();
                $items = $request->input("items");
                if (count($items) > 0) {
                    $updated_items = $personal->insertActivities($items, $action);
                    return response()->json(["result" => true, "items" => $updated_items]);
                }
                return response()->json(["not_found" => true]);
            }
            return response()->json(["not_allowed" => true]);
        } catch (\Throwable $th) {
            if ($th->getCode() == 403)
                return response()->json(["unauthorized" => true]);
            return response()->json($th->getMessage(), 500);
        }
    }

    public function fileDetails($id)
    {
        $file = PersonalFile::with(
            [
                'comments',
                'comments.replies',
                'comments.replies.user:id,firstname,lastname,image',
                'comments.user:id,firstname,lastname,image',
                'fileShares:id,shareable_id,shareable_type,shared_to',
                'fileShares.sharedTo:id,firstname,lastname,image',
                'createdBy:id,firstname,lastname'
            ]
        )->find($id);
        if (isset($file)) {
            $is_shared = true;
            $shared_result = FileUtils::checkIfParentisShared($file->id, auth()->id(), PersonalFile::class);
            $is_shared = $shared_result["result"];
            if (!$is_shared) {
                $is_shared = $file->sharedBy_id == auth()->id();
            }
            if ($file->user_id != auth()->id() && !$is_shared) {
                return response()->json(["result" => false, "unauthorized" => true], Response::HTTP_NOT_FOUND);
            }
            FileUtils::storeFileView(auth()->id(), $file);
            return response()->json(["result" => true, "file" => $file->loadCount('views', 'comments', 'downloads')], Response::HTTP_OK);
        } else
            return response()->json(["result" => false, "file" => null], Response::HTTP_NOT_FOUND);
    }

    public function downloadFileOrFolder(Request $request)
    {
        try {
            $request->validate([
                "items.*.id" => ["required", "uuid", "exists:personal_files,id"],
                "items.*.type" => ["required"],
            ]);

            $items = $request->input("items");
            $personal = new Personal();

            if (count($items) == 1) {
                $item = $items[0];
                if ($item["type"] == "folder") {
                    return $personal->streamSingleFolder($item["id"]);
                } else {
                    return $personal->streamSingleFile($item["id"]);
                }
            } else {
                $item_ids = [];
                foreach ($items as $item)
                    $item_ids[] = $item["id"];
                return $personal->streamFiles($item_ids);
            }
        } catch (\Throwable $th) {
            if ($th->getCode() == 403)
                return response()->json(["unauthorized" => true], 401);
            return response()->json(["error" => true], 404);
        }
    }


    public function updateFilePassword(Request $request)
    {
        $password_regex         = "regex:/((?=.*\d)(?=.*[A-z])(?=.*[~!@#$%^&*]).{6,40})/u";
        $request->validate([
            "item.type"         => ["required"],
            "item.id"           => ["required", "uuid", "exists:personal_files,id"],
            "current_password"  => ["nullable", "min:6", $password_regex],
            "new_password"      => ["nullable", "min:6", $password_regex],
            "password"          => ["nullable", "min:6", $password_regex],
        ]);

        try {
            $personal = new Personal();
            $result = null;
            $item_id = $request->item["id"];
            if ($request->password) {
                $result = $personal->setPasswordItem($request->password, $item_id);
            } else if ($request->current_password && $request->new_password) {
                $result = $personal->changePasswordItem($request->current_password, $request->new_password, $item_id);
            } else if ($request->clear_password) {
                $result = $personal->clearPasswordItem($request->current_password, $item_id);
                $result->action = "password_cleared";
            } else if ($request->check_password) {
                $result = $personal->checkPasswordItem($request->current_password, $item_id);
                $result->password = null;
                return response()->json([
                    "result" => true, "item" => [
                        "id" => $result->id,
                        "type" => $result->type,
                        "is_protected" => false,
                        "action" => "check_password",
                        "valid_passowrd" => true,
                        "is_protected" => false,
                        "path" => $result->path,
                        "thumbnail" => $result->thumbnail,
                        "parent_id" => $result->parent_id,
                        "type" => $result->type,
                        "info" => $result->info,
                    ]
                ]);
            } else {
                return response()->json(["not_allowed" => true]);
            }
            if ($result) {
                $result = [
                    "id" => $result->id,
                    "type" => $result->type,
                    "is_protected" => $result->is_protected,
                ];
            }
            return response()->json(["result" => true, "item" => $result]);
        } catch (\Throwable $th) {
            if ($th->getCode() == 403)
                return response()->json(["unauthorized" => true]);
            else if ($th->getCode() == 204)
                return response()->json(["invalid_current_password" => true]);
            return response()->json(["error" => true]);
        }
    }


    public function authFolders(Request $request)
    {
        try {
            $auth = $request->user()->id;
            $ignore_item_ids = $request->ignore_item_ids ?? [];
            $drive = [
                "id" => null,
                "name" => "Drive",
                "parent_id" => null,
                "children" => [],
            ];
            $root_folders = PersonalFile::where("type", "folder")->where('parent_id', null)
                ->whereNotIn("id", $ignore_item_ids)->where("user_id", $auth)
                ->select(["id", "name", "parent_id"])->get();
            if ($root_folders->isEmpty()) {
                return response()->json(["result" => true, "folders" => [$drive]]);
            }
            $personal = new Personal();
            $all_folders =  $personal->folderChildsAsTree($root_folders, $ignore_item_ids);
            $drive["children"] = $all_folders;
            return response()->json([
                "result" => true,
                "folders" => [$drive],
            ]);
        } catch (\Throwable $th) {
            return response()->json(["error" => true]);
        }
    }

    public function copyOrMove(Request $request)
    {
        $request->validate([
            "action" => ["required", "string", "in:copy,move"],
            "parent_id" => ["nullable", "uuid", "exists:personal_files,id"],
            "items" => ["required", "array"],
            "items.*.id" => ["required", "uuid", "exists:personal_files,id"],
            "items.*.type" => ["required"],
        ]);
        try {
            $action = $request->action;
            $parent_id = $request->parent_id;
            $items = $request->input('items');
            $personal = new Personal();
            $result =  $personal->copyOrMoveItems($action, $parent_id, $items);
            return response()->json(["result" => true, "items" => $result]);
        } catch (\Throwable $th) {
            if ($th->getCode() == 403)
                return response()->json(["unauthorized" => true]);
            if ($th->getCode() == 405)
                return response()->json(["not_allowed_limited_size" => true]);
            if ($th->getCode() == 402)
                return response()->json(["same_destination" => true]);
            return response()->json(["error" => $th->getMessage(), 'asdfasdf', 'adfasdf']);
        }
    }

    public function searchPersonalFiles(Request $request)
    {
        $request->validate(["searchTerm" => "required"]);

        // Type["trashed", "favorites", "default|null"];
        $type = $request->type;
        $searchTerm = $request->searchTerm;
        $parent_id = $request->parent_id;
        $auth = $request->user()->id;
        $personal = new Personal();
        $result = null;
        if ($type == "trashed") {
            $result = $personal->searchTrashed($searchTerm, $auth, $parent_id);
        } else if ($type == "favorites") {
            $result = $personal->searchFavorites($searchTerm, $auth, $parent_id);
        } else if ($type == "recent") {
            $result = $personal->searchRecent($searchTerm, $auth, $parent_id);
        } else if ($type == "shared") {
            $result = $personal->searchShared($searchTerm, $auth, $parent_id);
        } else if ($type == "my_shared") {
            $result = $personal->searchShared($searchTerm, $auth, $parent_id, true);
        } else {
            $result = $personal->searchDefault($searchTerm, $auth, $parent_id);
        }
        return response()->json(["result" => true, "items" => $result]);
    }

    public function share(Request $request)
    {
        $request->validate($this->shareValidateRules());

        $items                      = $request->items;
        $selected_companies         = $request->selected_companies;
        $company_selected_users     = $request->company_selected_users;
        $selected_departments       = $request->selected_departments;
        $department_selected_users  = $request->department_selected_users;
        $selected_teams             = $request->selected_teams;
        $team_selected_users        = $request->team_selected_users;
        $selected_roles             = $request->selected_roles;
        $role_selected_users        = $request->role_selected_users;
        $selected_users             = $request->selected_users;
        $company_users              = $this->getCompanyUsers($selected_companies, $company_selected_users);
        $deparment_users            = $this->getDepartmentUsers($selected_departments, $department_selected_users);
        $team_users                 = $this->getTeamRoleUsers($selected_teams, $team_selected_users, 'team');
        $role_users                 = $this->getTeamRoleUsers($selected_roles, $role_selected_users, 'role');
        $all_users                  = array_unique(array_merge($selected_users, $company_users, $deparment_users, $team_users, $role_users));
        $notification               = Notification::where('title', 'files_sharing')->first();
        $filesCount                 = 0;
        $foldersCount               = 0;
        $user                       = $request->user();
        if (in_array($user->id, $all_users)) {
            unset($all_users[array_search('strawberry', $all_users)]);
        }
        $permission = $request->permissions;
        $files = PersonalFile::whereIn("id", $items)->select('id', 'type')->get();
        $fileShares = [];
        foreach ($all_users as $user_id) {
            foreach ($files as $file) {
                $file->type == 'folder' ? $foldersCount++ : $filesCount++;
                $fileShare = new FileShare([
                    "shared_by"     => $user->id,
                    "shared_to"     => $user_id,
                    "share_type"    => 'unlimited',
                    "date_range"    => null,
                    "permission"    => $permission,
                    "seen"          => false,
                ]);
                $temp_share = $file->fileShares()->where([
                    "shared_to"         => $user_id,
                    'shareable_id'      => $file->id,
                    'shareable_type'    => PersonalFile::class
                ])->first();
                if ($temp_share) {
                    $temp_share->delete();
                }
                $file->fileShares()->save($fileShare);
                $fileShares[] = $fileShare->load('sharedTo:id,firstname,lastname,image');

                PersonalFileChange::dispatch(
                    $user_id,
                    [
                        'item_id' => $file->id,
                    ],
                    'shared'
                );
            }
            SendNotification::dispatch(
                $user_id,
                $notification->id,
                [],
                [
                    $user->firstname . ' ' . $user->lastname,
                    $foldersCount,
                    $filesCount
                ],
                [
                    'system' => 'Sharing',
                ]
            );
        }
        return response()->json([
            'result'            => true,
            'items_length'      => $files->count(),
            'items'             => $files->pluck('id'),
            'file_shares'       => $fileShares
        ], Response::HTTP_CREATED);
    }

    private function shareValidateRules()
    {
        return  [
            "items"                         => ["array", "required"],
            "items.*"                       => ["exists:personal_files,id", "uuid"],
            "selected_companies"            => ["array"],
            "selected_companies.*"          => ["exists:companies,id", "uuid"],
            "company_selected_users.*"      => ["array"],
            "company_selected_users.*.*"    => ["exists:users,id", "uuid"],
            "selected_departments"          => ["array"],
            "selected_departments.*"        => ["exists:departments,id", "uuid"],
            "department_selected_users.*"   => ["array"],
            "department_selected_users.*.*" => ["exists:users,id", "uuid"],
            "selected_teams"                => ["array"],
            "selected_teams.*"              => ["exists:teams,id", "uuid"],
            "team_selected_users.*"         => ["array"],
            "team_selected_users.*.*"       => ["exists:users,id", "uuid"],
            "selected_teams"                => ["array"],
            "selected_teams.*"              => ["exists:teams,id", "uuid"],
            "team_selected_users.*"         => ["array"],
            "team_selected_users.*.*"       => ["exists:users,id", "uuid"],
            "selected_roles"                => ["array"],
            "selected_roles.*"              => ["exists:roles,id", "uuid"],
            "role_selected_users.*"         => ["array"],
            "role_selected_users.*.*"       => ["exists:users,id", "uuid"],
            "selected_users"                => ["array"],
            "selected_users.*"              => ["exists:users,id", "uuid"],
        ];
    }

    private function getIds($ids, $item_users)
    {
        // item users is an object of items ids as keys and user ids as value of item ids
        // removes the items which has specific user ids from items array and returns an array of  filtered ids and the specific users_ids
        $filterd_ids = [];
        $users = [];
        foreach ($ids as  $id) {
            if (!array_key_exists($id, $item_users)) {
                $filterd_ids[] = $id;
            } else {
                $users = array_unique(array_merge($users, $item_users[$id]));
            }
        }
        return ['ids' => $filterd_ids, 'users' => $users];
    }

    private function getCompanyUsers($company_ids, $company_users)
    {
        $res = $this->getIds($company_ids, $company_users);
        $filterd_companies = $res['ids'];
        $user_ids = $res['users'];
        $users = User::whereHas('companies', function (Builder $query) use ($filterd_companies) {
            $query->whereIn('company_id', $filterd_companies);
        })->pluck('id');
        return array_unique(array_merge($users->toArray(), $user_ids));
    }

    private function getDepartmentUsers($department_ids, $department_users)
    {
        $res = $this->getIds($department_ids, $department_users);
        $filterd_departments = $res['ids'];
        $user_ids = $res['users'];
        $teams = Team::whereHas('departments', function (Builder $query) use ($filterd_departments) {
            $query->whereIn('department_id', $filterd_departments);
        })->pluck('id');
        $roles = Role::whereHas('departments', function (Builder $query) use ($filterd_departments) {
            $query->whereIn('department_id', $filterd_departments);
        })->pluck('id');
        $teamUsers = User::whereHas('teams', function (Builder $query) use ($teams) {
            $query->whereIn('team_id', $teams);
        })->pluck('id');
        $roleUsers = User::whereHas('roles', function (Builder $query) use ($roles) {
            $query->whereIn('role_id', $roles);
        })->pluck('id');

        return array_unique(array_merge($teamUsers->toArray(), $roleUsers->toArray(), $user_ids));
    }

    private function getTeamRoleUsers($team_ids, $team_users, $type)
    {
        $res = $this->getIds($team_ids, $team_users);
        $filterd_teams_roles = $res['ids'];
        $user_ids = $res['users'];
        $users = User::whereHas("{$type}s", function (Builder $query) use ($filterd_teams_roles, $type) {
            $query->whereIn("{$type}_id", $filterd_teams_roles);
        })
            ->pluck('id');
        return array_unique(array_merge($users->toArray(), $user_ids));
    }

    public function settingsPersonalFiles(Request $request)
    {
        $user_id = $request->user()->id;
        $personal = new Personal();
        $result = $personal->settingsPersonalFiles($user_id);
        return response()->json(["result" => true, "items" => $result]);
    }


    public function defaultSettingsPersonal(Request $request)
    {
        $user_id = $request->user()->id;
        if ($request->default_view) {
            $request->validate([
                "default_view" => ["required", "string", "required_if:thumnail,list"],
            ]);
            $defaultview = $request->default_view;
            FileLimitionForUsers::where('user_id', $user_id)->update([
                'default_view' => $defaultview,
            ]);
            return response()->json(['result' => true]);
        }

        if ($request->default_sorting) {
            $request->validate([
                "default_sorting" => ["required", "string", "required_if:name,size,date_modified"],
            ]);

            $defaultSort = $request->default_sorting;
            FileLimitionForUsers::where('user_id', $user_id)->update([
                'default_sorting' => $defaultSort,
            ]);
            return response()->json(['result' => true]);
        }
    }
    public function updateUserStorage(Request $request)
    {
        $request->validate([
            "amount" => ["required_if:type,limited"],
            "size" => ["required", "in:GB,MB"],
            "type" => ["required", "in:limited,unlimited"],
            "user_id" => ["required", "uuid", "exists:file_limition_for_users,user_id"],
            "id" => ["required", "uuid", "exists:file_limition_for_users,id"],
        ]);

        $type = $request->type;
        $amount = (int) $request->amount;
        $size_type = $request->size;
        $id = $request->id;
        $user_id = $request->user_id;

        $personal = new Personal();
        if ($type == "limited") {
            if ($amount > 0) {
                if ($size_type == "GB") {
                    $amount = round($amount * (1024 * 1024 * 1024));
                } else if ($size_type == "MB") {
                    $amount = round($amount * (1024 * 1024));
                } else {
                    $amount = 0;
                }
            }
            $result = $personal->updateUserStorage($id, $user_id, $amount, $type);
            return response()->json($result);
        }
        $result = $personal->updateUserStorage($id, $user_id, 0, $type);
        return response()->json($result);
    }
}
