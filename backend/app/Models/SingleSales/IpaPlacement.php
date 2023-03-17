<?php

namespace App\Models\SingleSales;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpaPlacement extends Model
{
    use HasFactory, UuidTrait; 
    protected $fillable = ['ipa_id','ipa_add_placement_id'];
    protected $table = 'ipa_placements';
}
