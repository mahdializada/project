<?php

namespace App\Models\ProductManagement;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductManagement\Category;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudySubCategory extends Model
{
    use HasFactory, UuidTrait;

    protected $table = "pdm_study_sub_categories";

    protected $fillable = ["name", "category_id"];


    /**
     * Get the category that owns the StudySubCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, "category_id");
    }
}
