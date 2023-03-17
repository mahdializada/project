<?php

namespace App\Models\SingleSales;

use App\Models\Reason;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    protected $table = "categories_ssp";
    protected $fillable = ['id', 'name','arabic_name','status','icon','description','arabic_description','created_by', 'parent_id','code'];
    protected $appends = ['code'];

    use HasFactory, SoftDeletes, UuidTrait;

    public function getCodeAttribute($value)
    {
        return rand(1000000, 9999999999);
    }

    public function user(){
        return $this->belongsTo(User::class,'created_by');
    }

    public function reasons()
    {
        return $this->morphToMany(Reason::class, 'reasonable');
    }


    public function parent(){
        return $this->belongsTo(Category::class,'parent_id');
    }

    public function insterestValues(){
        return $this->hasMany(InterestValue::class,'category_id');
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, "attribute_category_ssp", "category_id", "attribute_id");
    }

    public function country(){
        return $this->belongsTo(Category::class,'country_id');

    }
    public function company(){
        return $this->belongsToMany(Category::class,'company_id');

    }
}
