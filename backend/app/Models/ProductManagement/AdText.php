<?php

namespace App\Models\ProductManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdText extends Model
{
    use HasFactory;

    protected $table = "ad_text";

    protected $fillable = ["ad_text", "ad_text_language", "ad_text_name", "ad_textable_type", "ad_textable_id"];
}
