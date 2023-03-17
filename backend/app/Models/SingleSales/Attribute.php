<?php

namespace App\Models\SingleSales;

use App\Traits\UuidTrait;
use App\Models\User;
use App\Models\Reason;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;



class Attribute extends Model
{
    use HasFactory,  SoftDeletes, UuidTrait;

    protected $hidden = ['pivot'];

    protected $table = 'attributes_ssp';

    protected $fillable = ['id', 'code', 'name', 'arabic_name' ,'status', 'values','type', 'created_by'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->select('id', 'firstname', 'lastname', 'image');
    }
    public function categories()
    {
        return $this->belongsToMany(
            Category::class,
            "attribute_category_ssp",
            "attribute_id",
            "category_id",
         );
    }
    public function getValuesAttribute($value){
        return json_decode($value);
    }

    public function reasons()
    {
        return $this->morphToMany(Reason::class, 'reasonable');
    }
}
