<?php

namespace App\Models\OnlineSalesManagement;

use App\Models\CloudAttachment;
use App\Models\User;
use App\Traits\UuidTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineSalesNote extends Model                                                                                                                                                                                                                                                                                                                                                                                                       
{
    use HasFactory,UuidTrait;
    protected $fillable = ['name','note','product_code','created_by','created_at','updated_at'];
    public function getCreatedAtAttribute($value): string
    {
        return Carbon::create($value)->format('Y-m-d');
    }
    public function attachments()
    {
        return $this->morphMany(CloudAttachment::class, 'attachmentable');
    }
    public function user(){
        return $this->belongsTo(User::class,'created_by');
    }
}
