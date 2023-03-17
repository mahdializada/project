<?php

namespace App\Models\Landing\Master\Product;

use App\Models\User;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Eloquent
{
    use HasFactory, UuidTrait, SoftDeletes;

    protected $fillable = ['name', 'description', 'icon', 'code'];

    protected $table = 'product_categories';

    protected static $types = ["active", "inactive", "blocked", "pending", "removed", "warning"];

    public static function getTypes()
    {
        return ProductCategory::$types;
    }

    public function subCategories(){
        return $this->hasMany(ProductSubCategory::class, 'category_id');
    }

    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by');
    }
}
