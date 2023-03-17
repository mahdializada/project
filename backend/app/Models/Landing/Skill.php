<?php

namespace App\Models\Landing;

use App\Models\User;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Landing\Master\Product\Skill\SkillSubCategory;

class Skill extends Model
{
    use HasFactory, UuidTrait, SoftDeletes;

    protected $table = 'landing_skills';
    
    protected $fillable = ['name', 'description', 'code', 'icon', 'sub_category_id'];

    protected static $types = ["active", "inactive", "blocked", "pending", "removed", "warning"];

    public static function getTypes()
    {
        return Skill::$types;
    }

    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by');
    }

    public function subCategory(){
        return $this->belongsTo(SkillSubCategory::class, 'sub_category_id');
    }
}
