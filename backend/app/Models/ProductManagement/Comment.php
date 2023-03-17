<?php

namespace App\Models\ProductManagement;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory, UuidTrait;

    protected $table = "pdm_comments";

    protected $fillable = ["comment", "user_id", "commentable_type", "commentable_id"];
}
