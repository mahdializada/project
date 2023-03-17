<?php

namespace App\Models\SingleSales;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductStudyCategory extends Model
{
    use HasFactory, UuidTrait , SoftDeletes;
    protected $table="product_study_category";
    protected $fillable = ['category_name'];
}
