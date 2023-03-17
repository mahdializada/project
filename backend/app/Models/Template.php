<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Template extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = [
        "name",
        "code",
        "created_by"
    ];
}
