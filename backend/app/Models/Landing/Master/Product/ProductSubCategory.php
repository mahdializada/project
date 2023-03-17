<?php

namespace App\Models\Landing\Master\Product;

use App\Models\User;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSubCategory extends Eloquent
{
    use HasFactory, UuidTrait, SoftDeletes;
    protected $fillable = ['name', 'description', 'icon', 'code',  'category_id'];

    protected $table = 'product_sub_categories';

    
    protected static $types = ["active", "inactive", "blocked", "pending", "removed", "warning"];

    public static function getTypes()
    {
        return ProductSubCategory::$types;
    }

    public function category(){
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by');
    }
}
