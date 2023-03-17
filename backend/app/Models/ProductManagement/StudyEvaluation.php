<?php

namespace App\Models\ProductManagement;

use App\Traits\UuidTrait;
use App\Models\ProductManagement\Study;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudyEvaluation extends Model
{
    use HasFactory, UuidTrait;


    protected $table = "pdm_study_evaluations";

    protected $fillable = ["rating", "comment", "study_id"];

    /**
     * Get the study that owns the StudyEvaluation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function study(): BelongsTo
    {
        return $this->belongsTo(Study::class, 'study_id');
    }
}
