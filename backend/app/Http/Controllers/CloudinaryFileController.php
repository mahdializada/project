<?php

namespace App\Http\Controllers;

use App\Models\CloudAttachment;
use App\Models\CloudinaryTempFile;
use App\Repositories\Files\CloudinaryFileUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CloudinaryFileController extends Controller
{
    //
    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            $file   = $request->file('file');
            $system = $request->get('system_name');
            $cloud  =  CloudinaryFileUtils::uploadFile($file, $system);
            return  $cloud;
        }
        return response()->json(['result' => false, 'message' => 'no file '], 500);
    }

    public function destroy(Request $request)
    {
        $path =  $request->path;
        $cloud =  CloudinaryFileUtils::deleteFileByPath($path);
        if ($cloud == 'ok') {
            return response()->json(['result' => true, 'message' => $cloud], 200);
        }

        return ['result' => false, 'message' => 'record not found in database'];
    }
}
