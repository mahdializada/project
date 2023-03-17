<?php

namespace App\Models\SingleSales;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStudyFormComponent extends Model
{
    use HasFactory,UuidTrait;
     

    protected $fillable = ['type','type_code','label_name','product_study_model_id'];
   
    protected static $type = ["short text", "long text"];
    public static function getType()
    {
        return ProductStudyFormComponent::$type;
    }
}
