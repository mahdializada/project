<?php

namespace App\Models\Landing\Master\Worker;

use App\Models\User;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkerSubCategory extends Eloquent
{
    use HasFactory, UuidTrait, SoftDeletes;

    protected $fillable = ['name', 'description', 'icon', 'category_id', 'code'];

    protected $table = 'worker_sub_categories';

    protected static $types = ["active", "inactive", "blocked", "pending", "removed", "warning"];

    public static function getTypes()
    {
        return WorkerSubCategory::$types;
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function subCategories()
    {
        return $this->hasMany(WorkerSubCategory::class, 'category_id');
    }
}
