<?php

namespace App\Models\ProductManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPageUrl extends Model
{
    use HasFactory;

    protected $table = "pdm_landing_page_url";

    protected $fillable = ["landing_page_url", "landing_page_urlable_type", "landing_page_urlable_id"];
}
