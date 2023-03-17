<?php

namespace App\Repositories\Files;

use App\Models\CloudAttachment;
use App\Models\CloudinaryTempFile;
use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

// Configuration::instance([
//     'cloud' => [
//         'cloud_name' => 'rohullahdev',
//         'api_key' => '865763483868779',
//         'api_secret' => 'kl-WX1u6kDn1aRmMKelALXQlIdM'
//     ],
//     'url' => [
//         'secure' => true
//     ]
// ]);

Configuration::instance([
    'cloud' => [
        'cloud_name' => 'smartfriqidevs',
        'api_key' => '779224616452333',
        'api_secret' => 'bD28s8BLbi_hZJYGG3wlaCYGM4s'
    ],
    'url' => [
        'secure' => true
    ]
]);

class CloudinaryFileUtils
{

    public function __construct()
    {
    }

    // for server
    public  static function uploadFile($file, $company = 'smartFriqi')
    {
        $temp_record =   self::storeTempFile($file);
        $path =   str_replace("public", "storage", $temp_record['path']);
        try {
            DB::beginTransaction();
            $uploadApi   =  new UploadApi();
            $type =  $temp_record['type'] == 'raw' ? 'Documents' : $temp_record['type'];
            $response = $uploadApi->upload($path, [
                "resource_type" => $temp_record['type'],
                'chunk_size' => 6000000,
                "use_filename" => TRUE,
                "unique_filename" => TRUE,
                'folder' => 'smart-friqi/' . $company . '/' . $type,
            ]);
            $record =   CloudinaryTempFile::find($temp_record['id']);
            $record->public_id = $response['public_id'];
            $record->cloud_path = $response['secure_url'];
            $record->save();
            DB::commit();
            return response()->json(['result' => true, 'public_id' => $response['public_id'], 'path' => $response['secure_url'], 'file_type' => $temp_record['type']]);
        } catch (\Throwable $th) {
            DB::commit();
            return response()->json(['result' => false, 'message' => $th->getMessage()], 500);
        }
    }


    // for server
    public static function deleteFile($path)
    {
        $api = new UploadApi();

        $response = $api->destroy($path['public_id'], $options =  ['resource_type' => $path['file_type']]);
        return $response['result'];
    }

    public static function deleteFileByPath($path)
    {
        $path =   CloudinaryTempFile::where("cloud_path", $path)
            ->first();
        if ($path) {
            Storage::delete($path->locale_path);
            $api = new UploadApi();
            $response = $api->destroy($path['public_id'], $options =  ['resource_type' => $path['file_type']]);
            if ($response['result'] == 'ok') {
                CloudinaryTempFile::where('cloud_path', $path)->delete();
            }
            return $response['result'];
        } else {
            return 'local file not found';
        }
    }
    public static function multiDeleteByPath($paths)
    {
        $publicIds =   CloudAttachment::whereIn("path", $paths)->get();
            foreach($publicIds as $value){
                $data =   self::deleteFile($value);
                if($data == 'ok' || $data == 'not found'){
                   $ok = CloudAttachment::where('path',$value->path)->delete();
                }
            }
            return true;
    }



    // store file in locale
    public static function storeTempFile($file)
    {
        $size = $file->getSize();
        $name = $file->getClientOriginalName();
        $file_type =  $file->getClientMimeType();
        $uploadedPath = $file->storeAs('public/cloudinary-temp', $name);
        if (strpos($file_type, 'image') !== false) {
            $type = 'image';
        } elseif (strpos($file_type, 'video') !== false) {
            $type = 'video';
        } else {
            $type = 'raw';
        }
        $temp_record =   CloudinaryTempFile::create(['locale_path' => $uploadedPath, 'name' => $name, 'size' => $size, 'file_type' => $type]);

        return ['path' => $uploadedPath, 'id' => $temp_record->id, 'type' => $type];
    }
    // delete temp file from locale
    public static function deleteTempFile(array $paths)
    {
        foreach ($paths as  $path) {
            $record = CloudinaryTempFile::where('cloud_path', $path)->first();
            if ($record) {
                Storage::delete($record->locale_path);
            }
            $record = CloudinaryTempFile::where('cloud_path', $path)->delete();
            if ($record == 0) {
                return false;
            }
        }
        return true;
    }

    public static function generatePublicId($path)
    {
        $extenion = pathinfo($path, PATHINFO_EXTENSION);
        $type = self::fileType($extenion);
        if ($type == 'image' || $type == 'video') {
            $path =  substr($path, strpos($path, 'smart-friqi'));
            return ['type' => $type, 'public_id' => rtrim($path, '.' . $extenion)];
        } else {
            return  ['type' => 'raw', 'public_id' => substr($path, strpos($path, 'smart-friqi'))];
        }
    }


    public static function fileType($extenion)
    {
        $video =  [
            "webm", "mkv", "flv", "vob", "ogv", "ogg", "rrc", "gifv", "mng", "mov", "avi", "qt", "wmv", "yuv", "rm", "asf", "amv", "mp4", "m4p", "m4v", "mpg", "mp2", "mpeg", "mpe", "mpv", "m4v", "svi", "3gp", "3g2", "mxf", "roq", "nsv", "flv", "f4v", "f4p", "f4a", "f4b", "mod"
        ];
        $image = [
            'png',
            'jpe',
            'jpeg',
            'jpg',
            'gif',
            'bmp',
            'ico',
            'tiff',
            'tif',
            'svg',
            'svgz'
        ];
        if (in_array(strtolower($extenion), $video)) {
            return 'video';
        }
        if (in_array(strtolower($extenion), $image)) {
            return 'image';
        }
        return 'raw';
    }

    // update file for table
    public static function  updateFiles($model, $parent_id, $paths)
    {

        $paths = array_map(function ($row) {
            if (gettype($row) == 'array') {
                return $row['path'];
            }
            return $row;
        }, $paths);

        try {
            foreach ($paths as  $path) {
                $existExist =  CloudAttachment::where('attachmentable_id', $parent_id)->where('path', $path)->first();
                if (!$existExist) {
                    $tempFile =   CloudinaryTempFile::where("cloud_path", $path)
                        ->first();
                    // return ['result' => false, "message" => $tempFile];
                    if ($tempFile) {
                        Storage::delete($tempFile->locale_path);
                        $model->attachments()->create([
                            'size' => $tempFile->size, "name" => $tempFile->name,
                            'file_type' => $tempFile->file_type,
                            "public_id" => $tempFile->public_id,
                            "path" => $tempFile->cloud_path
                        ]);
                        CloudinaryTempFile::where("cloud_path", $path)->delete();
                    }
                    // else {
                    //     return ['result' => false, "message" => 'locale record not found'];
                    // }
                }
            }

            $deleted_files =  CloudAttachment::where('attachmentable_id', $parent_id)->whereNotIn('path', $paths)->get();
            if (count($deleted_files) > 0) {
                foreach ($deleted_files as $key => $value) {
                    $result =   self::deleteFile($value);
                    if ($result != "ok" && $result != "not found") {
                        return ['result' => false, "message" => $result];
                    }
                }
                CloudAttachment::where('attachmentable_id', $parent_id)->whereNotIn('path', $paths)->delete();
            }
            return ['result' => true, "message" => 'success', $deleted_files];
        } catch (\Throwable $th) {

            return ['result' => false, "message" => $th->getMessage()];
        }
    }

    public static function  storeFiles($model,  $paths)
    {
        if (count($paths) == 0) {
            return ['result' => true, "message" => 'success'];
        }

        try {
            $images =  CloudinaryTempFile::whereIn(
                'cloud_path',
                $paths
            )->select(['size', 'name', 'cloud_path as path', 'file_type', 'public_id'])->get()->toArray();
            $model->attachments()->createMany($images);
            $temp =   self::deleteTempFile($paths);
            if (!$temp) {
                return ['result' => false, "message" => 'locale record not found'];
            }
            return ['result' => true, "message" => 'success'];
        } catch (\Throwable $th) {

            return ['result' => false, "message" => $th->getMessage()];
        }
    }
}
