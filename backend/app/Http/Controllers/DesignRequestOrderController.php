<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Folder;
use App\Events\Updated;
use App\Models\SubSystem;
use App\Traits\FileTrait;
use App\Models\LibraryFile;
use App\Models\PersonalFile;
use Illuminate\Http\Request;
use App\Models\DesignRequestOrder;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\DesignRequestOrderFile;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\DesignRequestOrderRepository;
use Intervention\Image\ImageManagerStatic as Image;



class DesignRequestOrderController extends Controller
{
    private $repository;
    use FileTrait;

    public function __construct()
    {
        $this->repository = new DesignRequestOrderRepository();
        // $this->middleware('scope:drov')->only(['index', 'show']);
        // $this->middleware('scope:droc')->only(['store']);
        // $this->middleware('scope:drou')->only(['update', 'changeStatus']);
        // $this->middleware('scope:drod')->only(['destroy']);
        // $this->middleware('scope:droa')->only(['assignUsers']);

        // $this->middleware('dailylogs:landing_page,design_request,insert')->only(['store']);
        // $this->middleware('dailylogs:landing_page,design_request,update')->only(['update']);
        // $this->middleware('dailylogs:landing_page,design_request,delete')->only(['destroy']);
        // $this->middleware('dailylogs:landing_page,design_request,assign_user')->only(['assignUsers']);
        // $this->middleware('dailylogs:landing_page,design_request,change status')->only(['changeStatus']);
    }
    /**
     * Display a listing of the order.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $onlyAssigned = true;
        if ($this->tokenCan('drva')) {
            $onlyAssigned = false;
        }
        if ($request->query->get("product-code") == true) {
            return $this->repository->getProductCode();
        }
        return $this->repository->paginate($request, $onlyAssigned);
    }

    public function updatePrograss(Request $request)
    {
        return $this->repository->updatePrograss($request);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        $uploadedFiles = [];
        $design_request_order = DesignRequestOrder::with("designRequest")->findOrFail($id);
        $company_id = $design_request_order->designRequest->company_id;
        $this->prefix = 'file-management/' . $company_id . '/' . $design_request_order->design_request_id;
        try {

            $files = $request->file("files");
            if ($files) {
                // AB file ------- upload must be optimized

                // end AB file upload

                foreach ($files as $file) {
                    // AB file ------- upload must be optimized
                    $newName = $this->returnNameFile(new PersonalFile(), $file, null, $request->user()->id);
                    $url = $this->storeFileAs($file, $newName);
                    $imageType = $file->getMimeType();
                    $image = null;
                    $path_new = null;

                    if (in_array($imageType, $this->imageTypes)) {
                        $image = Image::make($file)->fit(350, 250, function ($constraint) {
                            $constraint->upsize();
                        });
                        $path_new = $this->storeImageAsFile($image, '350x250' . $newName);
                    }

                    $newFile = LibraryFile::create([
                        'name' => $newName,
                        'extension' => $file->getClientOriginalExtension(),
                        'mime_type' => $file->getMimeType(),
                        'path' => $url,
                        "size" => $file->getSize(),
                        "thumbnail" => in_array($imageType, $this->iconTpye) ? $url : $path_new,
                        'company_id' => $company_id,
                        'created_by' => $request->user()->id,
                        'updated_by' => $request->user()->id,
                        'design_request_id' => $design_request_order->design_request_id
                    ]);

                    broadcast(
                        new Updated(
                            'file_management',
                            [
                                "id" => $newFile->id,
                                "parent_id" => $newFile->parent_id,
                                "type" => "file"
                            ],
                            'created'
                        )
                    );
                    //end AB file upload
                    $uploadedFiles[] = $url;
                }
            } else if ($request->fileId) {
                $deletedModel =  $this->deleteFileData($request->fileId);
            }
            $relations = [
                "designRequest",
                "designRequest.files" => function ($query) {
                    $query->where("type", "file")->select([
                        "id", "design_request_id", "created_by", "name", "type", "size", "updated_at",
                    ]);
                },
                'designRequest.files.createdBy:id,firstname,lastname',
            ];
            $design_request_order = DesignRequestOrder::with($relations)->find($id);
            $images =  $design_request_order->designRequest->files;
            DB::commit();
            return response()->json(["result" => true, "images" => $images]);
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->deleteFiles($uploadedFiles);
            return response()->json(["result" => false, "error" => $th->getMessage()], 501);
        }
    }

    function deleteFileData($fileId)
    {
        $fileModel = LibraryFile::findOrFail($fileId);
        $this->deleteFile($fileModel->name . '.' . $fileModel->type);
        $fileModel->forceDelete();
        return response()->json(["result" => true]);
    }

    public function searchDesignRequestOrder(Request $request)
    {
        return $this->repository->search($request);
    }

    public function getDesignRequestFiles(Request $request, $id)
    {
        $images = DesignRequestOrderFile::with(["changedBy:id,firstname,lastname"])
            ->where("design_request_order_id", $id)->get();

        return response()->json(["result" => true, "images" => $images]);
    }
}
