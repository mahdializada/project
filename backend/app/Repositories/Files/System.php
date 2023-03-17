<?php


namespace App\Repositories\Files;

use Exception;
use App\Models\SubSystem;
use App\Traits\FileTrait;
use App\Models\SystemFile;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManagerStatic as Image;

class System
{
    use FileTrait;
    private $rootPath = "file-management/system/";

    public function storeSystemFile($filename, $subsystem, $file, $company_id)
    {
        $subSystemId = SubSystem::where('name', 'LIKE',  '%' . $subsystem . '%')->first()->id;
        try {
            DB::beginTransaction();
            $dimensions                     = '';
            $thumbnail                      = '';
            $filenamePlusExtension          = $filename . '.' . pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
            $authId                         = auth()->user()->id;
            $this->prefix                   = $this->rootPath . strtolower(implode('-',explode(" ", $subsystem)));
            $fullPath                       = $this->storeFileAs($file, $filenamePlusExtension);

            if (in_array($file->getMimeType(), $this->imageTypes)) { // Only Image types
                $dimentions = getimagesize($file);
                $dimensions = ['width' => $dimentions[0], 'height' =>  $dimentions[1]];
                $image = Image::make($file)->fit(260, 100, function ($constraint) {
                    $constraint->upsize();
                });
                $thumbnail = $this->storeImageAsFile($image, '260x100' . $filenamePlusExtension);
            }

            $isSubSystemFolderExist             = SystemFile::where(['type' => 'folder', 'sub_system_id' => $subSystemId])->exists();
            if(!$isSubSystemFolderExist){
                $systemFileModel                    = new SystemFile();
                $systemFileModel->name              = $subsystem;
                $systemFileModel->type              = 'folder';
                $systemFileModel->created_by        = $authId;
                $systemFileModel->updated_by        = $authId;
                $systemFileModel->company_id        = $company_id;
                $systemFileModel->sub_system_id     = $subSystemId;
                $systemFileModel->save();
            }

            $systemFileModel                    = new SystemFile();
            $systemFileModel->name              = $filenamePlusExtension;
            $systemFileModel->type              = 'file';
            $systemFileModel->mime_type         = $file->getMimeType();
            $systemFileModel->path              = $fullPath;
            $systemFileModel->thumbnail         = $thumbnail;
            $systemFileModel->size              = $file->getSize();
            // $systemFileModel->icon           = $icon;
            $systemFileModel->dimensions        = $dimensions;
            $systemFileModel->description       = 'store this please';
            $systemFileModel->created_by        = $authId;
            $systemFileModel->updated_by        = $authId;
            $systemFileModel->company_id        = $company_id;
            $systemFileModel->sub_system_id     = $subSystemId;
            $systemFileModel->extension         = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
            $systemFileModel->save();

            DB::commit();
            return $systemFileModel;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new Exception($th->getMessage(), 1);
        }
    }

    public function getStringParenthesisValue($string, $filename){
        $openIndex           = strripos($string, '(');
        $closeIndex          = strripos($string, ')');
        $lastFileCount       = (!$openIndex || !$closeIndex) ? 0 : (int) substr($string, $openIndex + 1, $closeIndex - $openIndex - 1);
        return $filename . ' (' . ($lastFileCount * 1 + 1) . ')';
    }
}
