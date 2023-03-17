<?php

namespace App\Models\ProductManagement;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, UuidTrait;

    protected $table = "pdm_categories";

    protected $appends = ['type'];


    protected $fillable = ["name", "status", "parent_id"];


    public function parent()
    {
        return $this->belongsTo(self::class, "parent_id");
    }

    public function getTypeAttribute()
    {
        return $this->parent_id == null ? 'category' : 'sub_category';
    }
}
