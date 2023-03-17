<?php

namespace App\Models\ProductManagement;

use App\Models\ProductManagement\Study;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reference extends Model
{
    use HasFactory;

    protected $table = "pdm_references";

    protected $fillable = [
        "refrence_name", "refrence_description", "study_id",
    ];


    /**
     * Get the study that owns the Reference
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function study(): BelongsTo
    {
        return $this->belongsTo(Study::class, 'study_id');
    }
}
