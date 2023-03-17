<?php

namespace App\Models\ProductManagement;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductManagement\FormComponent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FormFulfillment extends Model
{
    use HasFactory;


    protected $table = "pdm_form_fulfillments";

    protected $fillable = [
        "form_component_id", "study_form_answer"
    ];

    /**
     * Get the formComponent that owns the FormFulfillment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function formComponent(): BelongsTo
    {
        return $this->belongsTo(FormComponent::class, 'form_component_id');
    }
}
