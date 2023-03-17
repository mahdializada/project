<?php

namespace App\Models\Landing\Master\Product\Skill;

use App\Models\User;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class SkillCategory extends Eloquent
{
    use HasFactory, UuidTrait, SoftDeletes;

    protected $fillable = ['name', 'description', 'icon', 'code'];

    protected $table = 'product_skill_categories';

    protected static $types = ["active", "inactive", "blocked", "pending", "removed", "warning"];

    public static function getTypes()
    {
        return SkillCategory::$types;
    }

    public function subCategories()
    {
        return $this->hasMany(SkillSubCategory::class, 'category_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
