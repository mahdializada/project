<?php

namespace App\Models\SingleSales;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStudyModel extends Model
{
    use HasFactory, UuidTrait;
    protected $table="product_study_model";
    protected $fillable = ['study_model_name','study_sub_category_id'];
}
