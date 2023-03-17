<?php

namespace App\Models\ProductManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetCountry extends Model
{
    use HasFactory;

    protected $table = "pdm_target_countries";

    protected $fillable = [
        "targetable_type", "targetable_id", "country_id"
    ];
}
