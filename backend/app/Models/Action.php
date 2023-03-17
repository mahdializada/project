<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = ["name", "phrase"];

    protected $hidden = ["pivot"];

    public function ActionsSubSystem()
    {
        return $this->hasMany(ActionSubSystem::class);
    }

    public function subActions()
    {
        return $this->belongsToMany(SubAction::class);
    }

    public function subSystems()
    {
        return $this->belongsToMany(
            SubSystem::class,
            "action_sub_system",
            "action_id",
            "sub_system_id",
        );
    }
}
