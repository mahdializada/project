<?php

namespace App\Models\ProductManagement;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductManagement\StudyModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FormComponent extends Model
{
    use HasFactory, UuidTrait;


    protected $table = "pdm_form_components";

    protected $fillable = ["type", "type_code", "label_name", "study_model_id"];

    /**
     * Get the studyModel that owns the FormComponent
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function studyModel(): BelongsTo
    {
        return $this->belongsTo(StudyModel::class, 'study_model');
    }
}
