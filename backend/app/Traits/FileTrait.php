<?php

namespace App\Traits;

use Zip;
use ZipArchive;
use App\Models\File;
use App\Models\PersonalFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Intervention\Image\Image;
use Illuminate\Http\UploadedFile;
use STS\ZipStream\ZipStreamFacade;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

trait FileTrait
{


    public $disk = "public";
    public $prefix = "admin";
    public $amazonBaseUrl = "https://teebalhoor.s3.amazonaws.com/";
    public $imageTypes = ['image/png', 'image/jpg', 'image/jpeg', 'image/gif', 'image/webp'];
    public $iconTpye = ['image/svg+xml', 'image/vnd.microsoft.icon'];

    /**
     * Store file in $disk directory
     *
     * @param UploadedFile $file
     * @return string
     */
    public function storeFile(UploadedFile $file): string
    {
        if ($this->disk === "s3") {
            $path =  Storage::disk($this->disk)->putFile($this->prefix, $file);
            return Storage::disk()->url($path);
        }

        return Storage::disk($this->disk)->putFile($this->prefix, $file);
    }


    /**
     * Store file with a name
     *
     * @param UploadedFile $file
     * @param string $filename
     * @return string
     */
    public function storeFileAs(UploadedFile $file, string $filename): string
    {
        if ($this->disk === "s3") {
            $path =  Storage::disk($this->disk)->putFileAs($this->prefix, $file, $filename);
            return $path;
        }
        return Storage::disk($this->disk)->putFileAs($this->prefix, $file, $filename);
    }

    public function storeImageAsFile(Image $file, string $filename): string
    {

        $filename = $this->prefix . '/' . $filename;
        if ($this->disk === "s3") {
            $result =  Storage::disk($this->disk)->put($filename, $file->encode());
            return Storage::disk('s3')->path($filename);
        }
        $res =  Storage::disk($this->disk)->put($filename, $file->encode());
        if ($res) {
            return $filename;
        }
        return false;
    }


    /**
     * Store file with base64 file type
     * @param string $file
     * @param string $recordId
     * @return string
     */
    public function storeBase64File($file, $recordId)
    {
        $extension = explode('/', explode(':', substr($file, 0, strpos($file, ';')))[1])[1];
        if ($this->disk === "s3") {
            throw new \Exception("can't upload base64 to amazon aws services", 1);
        }
        return Storage::disk($this->disk)->putFileAs($this->prefix, $file, $recordId . '.' . $extension);
    }

    /**
     * Store multiple files in $disk
     *
     * @param array $files
     * @return array
     */
    public function storeFiles(array $files): array
    {
        $filesPath = [];
        foreach ($files as $file) {
            $path = $this->storeFile($file);
            array_push($filesPath, $path);
        }

        return $filesPath;
    }

    /**
     * Store and remove a file
     *
     * @param UploadedFile $file
     * @param string fileUrl
     * @return string
     */
    public function storeAndRemove(UploadedFile $file, string $filename): string
    {
        $this->deleteFile($filename);
        return $this->storeFile($file);
    }


    /**
     * Delete file from $disk and return 1 for success deletion
     *
     * @param string file
     * @return bool
     */
    public function deleteFile(string $file): bool
    {
        if ($this->disk === "s3") {
            $filteredPath = str_replace($this->amazonBaseUrl, "", $file);
            return Storage::disk($this->disk)->delete($filteredPath);
        }
        return Storage::disk($this->disk)->delete($file);
    }
    public function deleteFileAnyPath(string $file,$path='public')
    {
        if ($path === "s3") {
            $filteredPath = str_replace($this->amazonBaseUrl, "", $file);
            return Storage::disk($path)->delete($filteredPath);
        }
        return Storage::delete($file);
    }


    /**
     * Delete multiple files and return 1 for success deletion
     *
     * @param array $files
     * @return bool
     */
    public function deleteFiles(array $files): bool
    {
        $filteredPaths = [];
        foreach ($files as $file) {
            $filteredPath = str_replace($this->amazonBaseUrl, "", $file);
            array_push($filteredPaths, $filteredPath);
        }
        return Storage::disk($this->disk)->delete($filteredPaths);
    }



    public function makeFolderAsZipStream($files, $folderName)
    {
        $filePaths = [];
        foreach ($files as $file) {
            $fileName = Str::after($file->path, $folderName);
            $filePath = Storage::disk($this->disk)->path($file->path);
            $filePaths[$filePath] = $fileName;
        }
        if ($filePaths)
            return ZipStreamFacade::create("$folderName.zip", $filePaths);
        return null;
    }

    public function getFullPath($file_path)
    {
        return Storage::disk($this->disk)->path($file_path);
    }

    public function existsFile($file_path)
    {
        return Storage::disk($this->disk)->exists($file_path);
    }

    public function downloadFile($file_path)
    {
        Storage::disk($this->disk)->download($file_path);
    }

    public function readFileAsStream($file_path)
    {
        return Storage::disk($this->disk)->readStream($file_path);
    }

    private function nameNumber($parent_id, $fileNameFormated)
    {
        do {
            $foundFile =
                File::where('name', $fileNameFormated)
                ->where('parent_id', $parent_id)
                ->first();
            if ($foundFile) {
                preg_match(
                    '/\([\d]+\)' . '.' . pathinfo($foundFile->name, PATHINFO_EXTENSION) . '/',
                    $foundFile->name,
                    $matches
                );
                if (count($matches) > 0) {
                    preg_match(
                        '/\([\d]+\)/',
                        $matches[0],
                        $secondMatch
                    );
                    $index = intval(str_replace(['(', ')'], '', $secondMatch[0])) + 1;
                    $fileNameFormated = preg_replace(
                        '/\([\d]+\)' . '.' . pathinfo($foundFile->name, PATHINFO_EXTENSION) . '$' . '/',
                        "($index)" . '.' . pathinfo($foundFile->name, PATHINFO_EXTENSION),
                        $foundFile->name
                    );
                } else {
                    $index = 2;
                    $fileNameFormated = preg_replace(
                        '/.' . pathinfo($foundFile->name, PATHINFO_EXTENSION) . '$' . '/',
                        " ($index)" . '.' . pathinfo($foundFile->name, PATHINFO_EXTENSION),
                        $foundFile->name
                    );
                }
            }
        } while ($foundFile != null);
        return $fileNameFormated;
    }

    // return unique name for a file appended with datetime
    private function returnNameFile(Model $model, $file, $parent_id = null, $user_id = null)
    {
        $record = $model->where('name', $file->getClientOriginalName())
            ->select(['id', 'name']);
        if (!empty($parent_id)) {
            $record->where('parent_id', $parent_id);
        }
        if (!empty($user_id)) {
            $record->where('user_id', $user_id);
        }
        $record = $record->first();
        if ($record) {
            return
                pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) .
                ' ' .
                Carbon::now()->format('Y-m-d H-i-s') .
                '.' .
                pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
        }
        return $file->getClientOriginalName();
    }

    private function generatePath($parent, $user_id)
    {
        $path = "file-management/personal/{$user_id}";
        $parent_id = $parent ? $parent->parent_id : null; // dont use this anywhere except in while
        $parent_names_path = $parent ? '/' . $parent->id : '';
        while ($parent_id !== null) {
            $temp_parent = PersonalFile::select(['id', 'name', 'parent_id'])
                ->where('id', $parent_id)
                // ->where('user_id', $user_id)
                ->first();
            if ($temp_parent !== null) {
                $parent_id = $temp_parent->parent_id;
                $parent_names_path = "/{$temp_parent->id}" . $parent_names_path;
            } else {
                $parent_id = null;
            }
        }
        return $path . $parent_names_path;
    }
}
