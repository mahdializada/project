<?php

namespace App\Models\Landing\Master\Product\Requirement;

use App\Models\User;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequirementCategory extends Eloquent
{
    use HasFactory, UuidTrait, SoftDeletes;

    protected $fillable = ['name', 'description', 'icon', 'code'];

    protected $table = 'product_requirement_categories';

    protected static $types = ["active", "inactive", "blocked", "pending", "removed", "warning"];

    public function subCategories()
    {
        return $this->hasMany(RequirementSubCategory::class, 'category_id');
    }

    public static function getTypes()
    {
        return RequirementCategory::$types;
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
