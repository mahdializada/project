<?php

namespace App\Models\ProductManagement;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdPlatform extends Model
{
    use HasFactory, UuidTrait;

    public $timestamps = false;

    protected $table = "pdm_ad_platforms";

    protected $fillable = ["name", "is_active"];
}
