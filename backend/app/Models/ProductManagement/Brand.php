<?php

namespace App\Models\ProductManagement;

use App\Models\Country;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory, UuidTrait, SoftDeletes;



    protected $table = "pdm_brands";

    protected $fillable = ["name", "code", "status", "is_approved", "country_id", "created_by"];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function attachment()
    {
        return $this->morphOne(Attachment::class, 'attachmentable');
    }
}
