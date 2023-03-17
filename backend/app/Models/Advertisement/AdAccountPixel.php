<?php

namespace App\Models\Advertisement;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdAccountPixel extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = [
        "pixel_name", "pixel_id", "pixel_code", "organization_id", "ad_account_id", "status", "pixel_script"
    ];

}
