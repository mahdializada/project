<?php

namespace App\Models\SingleSales;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetCountry extends Model
{
     protected $table = "target_countries_ssp";
    protected $fillable=['targetable_type','targetable_id','country_id'];
    use HasFactory, UuidTrait;
}
