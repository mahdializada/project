<?php

namespace App\Models\ProductManagement;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Action extends Model
{
    use HasFactory, UuidTrait;

    public $timestamps = false;

    protected $table = "pdm_actions";

    protected $fillable = ["actionable_type", "actionable_id", "work_priority", "action_note", "prograssive", "status"];

    public function instructions(): BelongsToMany
    {
        return $this->belongsToMany(Action::class, 'pdm_action_instructions',  'action_id', 'instuction_id');
    }
}
