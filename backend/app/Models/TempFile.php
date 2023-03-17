<?php

namespace App\Models;

use App\Models\SingleSales\AttachmentSsp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TempFile extends Model
{

    protected $guarded = ['created_at','updated_at'];
    protected static function boot() {
        parent::boot();
        static::deleting(function($query) {
            if (\Illuminate\Support\Facades\Storage::exists($query->path)){
                \Illuminate\Support\Facades\Storage::delete($query->path);
            }
        });
    }

// this method delete database record with uploaded files
    public function deleteTempfile($path){
        $q = $this->where("path",'=', $path)->first();
        if($q){
            return $q->delete();
        }
        else {
            $q = AttachmentSsp::where("path",$path)->first();
            return $q->delete();
        }

    }

    // this method only delete method
    public function deleteRec($path){
        if(!is_array($path)){
            $path = [$path];
        }
//        $q = $this->where("path",'=', $path)->first();
        return $this->whereIn('path',$path)->update(['path'=>null]);
    }

}
