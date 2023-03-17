<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubAction extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = ["name", "action"];
    protected $hidden = ["pivot"];

    public function actions()
    {
        return $this->belongsToMany(
            Action::class,
            'action_sub_action',
            'sub_action_id',
            'action_id',
        );
    }
}
