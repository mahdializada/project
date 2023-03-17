<?php

namespace App\Models\ProductManagement;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdPlacement extends Model
{
    use HasFactory, UuidTrait;

    public $timestamps = false;

    protected $table = "pdm_ad_placemnets";

    protected $fillable = ["name", "platform_id"];
}
