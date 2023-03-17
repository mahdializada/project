<?php

namespace App\Models\SingleSales;

use App\Models\TempFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class ActionClass extends Model
{

    // $table->foreignUuid('action_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
    // $table->string('statement');
    // $table->text('value')->nullable();
    // $table->json('attachements')->nullable();





    protected $table = "action_classes_ssp";
    protected $fillable = ['type','statement'];
    use HasFactory;

    public function attachments():MorphMany{
        return $this->morphMany(AttachmentSsp::class,'attachmentable');
    }

    public function saveUploadedFile($classes,$attachments){
        if (is_array($classes)){
            $classes = [$classes];
        }

        foreach ($classes as $class){
            $files = TempFile::whereIn('path',$attachments)->get(['size','path','name']);
            foreach ($files as $file){
                $class->attachments()->create(['name'=>$file->name,'size'=>$file->size,'path'=>$file->path]);
            }

            $temp = new TempFile();
            $temp->deleteRec($attachments);
        }
    }
}
