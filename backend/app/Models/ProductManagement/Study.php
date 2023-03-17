<?php

namespace App\Models\ProductManagement;

use App\Models\Company;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductManagement\StudyModel;
use App\Models\ProductManagement\StudyPurpose;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Study extends Model
{
    use HasFactory, UuidTrait;

    protected $table = "pdm_studies";

    protected $fillable = [
        "studiable_type", "studiable_id", "study_language", "status", "work_priority",
        "order_note", "study_recommendations", "target_gender", "start_target_age",
        "end_target_age", "target_note", "company_id", "study_purpose_id", "study_model_id"
    ];

    /**
     * Get the company that owns the Study
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the studyPurpose that owns the Study
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function studyPurpose(): BelongsTo
    {
        return $this->belongsTo(StudyPurpose::class, 'study_purpose');
    }

    /**
     * Get the studyModel that owns the Study
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function studyModel(): BelongsTo
    {
        return $this->belongsTo(StudyModel::class, 'study_model_id');
    }
}
