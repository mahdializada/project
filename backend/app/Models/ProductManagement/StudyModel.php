<?php

namespace App\Models\ProductManagement;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductManagement\StudySubCategory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudyModel extends Model
{
    use HasFactory, UuidTrait;


    protected $table = "pdm_study_model";

    protected $fillable = [
        "name", "sub_category_id"
    ];


    /**
     * Get the studySubCategory that owns the StudyModel
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function studySubCategory(): BelongsTo
    {
        return $this->belongsTo(StudySubCategory::class, 'sub_category_id');
    }

}
