<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanySocialMedia extends Model
{
    use HasFactory;
    protected $fillable = [
        "company_id",
        "social_media_id",
        "url",
    ];
}
