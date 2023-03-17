<?php

namespace App\Models\ProductManagement;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Instruction extends Model
{
    use HasFactory, UuidTrait;


    protected $table = "pdm_instructions";

    public $timestamps = false;

    protected $fillable = [
        "instruction"
    ];

    /**
     * The actions that belong to the Instruction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function actions(): BelongsToMany
    {
        return $this->belongsToMany(Action::class, 'pdm_action_instructions', 'instuction_id', 'action_id');
    }
}
