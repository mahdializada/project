<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sourcer extends Model
{
    use HasFactory, UuidTrait,SoftDeletes;
    protected $fillable = ['code', 'name','status', 'last_name', 'phone_number', 'email', 'country_id','company_id'];
protected static $types = ['active','inactive','removed'];
public static function getTypes(){
    return Sourcer::$types;
}

    public function company(){
        return $this->belongsTo(Company::class);
    }
    public function country(){
        return $this->belongsTo(Country::class);
    }
    public function reasons()
    {
        return $this->belongsToMany(Reason::class, "reason_sourcer", "sourcer_id", "reason_id")->withPivot("status", "changed_by")->withTimestamps();

    }
    public function attachments()
    {
        return $this->morphMany(CloudAttachment::class, 'attachmentable');
    }
}
