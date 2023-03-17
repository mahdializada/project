<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WrongPassword extends Model
{
    use UuidTrait;

    protected $fillable = ["user_id", "created_at"];

    use HasFactory;
}
