<?php

namespace App\Models\ProductManagement;

use App\Traits\UuidTrait;
use App\Models\ProductManagement\Study;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductManagement\Action;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudyPreparation extends Model
{
    use HasFactory, UuidTrait;

    public $timestamps = false;

    protected $table = "pdm_study_preparations";
    protected $fillable = [
        "prepartation_type", "preparationt_content", "status", "study_id", "action_id"
    ];


    /**
     * Get the Study that owns the StudyPreparation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Study(): BelongsTo
    {
        return $this->belongsTo(Study::class, 'study_id');
    }

    /**
     * Get the action that owns the StudyPreparation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function action(): BelongsTo
    {
        return $this->belongsTo(Action::class, 'action_id');
    }
}
