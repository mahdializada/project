<?php

namespace App\Models\ProductManagement;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory, UuidTrait;

    protected $table = "pdm_ispp_channels";

    protected $fillable = ["sales_channel_template_id", "ispp_request_id"];
}
