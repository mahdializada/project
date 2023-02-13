<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMedia extends Model
{
    use HasFactory;

    use HasFactory;
    protected $table='social_medias';

    protected $fillable = [
        'name',
        'status',
        'image',
        'created_by',
        'updated_by',
        'website',
        'per_status',
    ];
    public function getCreatedAtAttribute($value)
 {
     return date("d F Y H:i A", strtotime($value));
 }
    public function getUpdatedAtAttribute($value)
 {
     return date("d F Y H:i A", strtotime($value));
 }
    public function createdBy(){
        return $this->belongsTo(User::class,'created_by')->select('id','first_name','last_name');
    }
    public function updatedBy(){
        return $this->belongsTo(User::class,'updated_by')->select('id','first_name','last_name');
    }
    public function companies(){
        return $this->belongsToMany(
           Company::class,
           "company_social_media",
           "social_media_id",
           "company_id",
        );

    }

}
