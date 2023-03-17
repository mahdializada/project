<?php

namespace App\Models\ProductManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetCity extends Model
{
    use HasFactory;

    protected $table = "pdm_target_cities";

    protected $fillable = [
        "targetable_type", "targetable_id", "city_id"
    ];
}
