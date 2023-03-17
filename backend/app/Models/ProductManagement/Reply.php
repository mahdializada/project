<?php

namespace App\Models\ProductManagement;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory, UuidTrait;

    protected $table = "pdm_replies";

    protected $fillable = [
        "reply", "comment_id", "user_id",
    ];

}
