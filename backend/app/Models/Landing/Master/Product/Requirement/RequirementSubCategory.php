<?php

namespace App\Models\Landing\Master\Product\Requirement;

use App\Models\User;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequirementSubCategory extends Eloquent
{
    use HasFactory, UuidTrait, SoftDeletes;

    protected $fillable = ['name', 'description', 'icon', 'category_id', 'code'];

    protected $table = 'product_requirement_sub_categories';

    protected static $types = ["active", "inactive", "blocked", "pending", "removed", "warning"];

    public static function getTypes()
    {
        return RequirementSubCategory::$types;
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function category()
    {
        return $this->belongsTo(RequirementCategory::class, 'category_id');
    }
}
