<?php

namespace App\Models\SingleSales;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpaAddPlacement extends Model
{
    use HasFactory,UuidTrait;
    protected $table="ipa_add_placements";
    protected $fillable = ['placement_name','add_platform_id'];
}
