<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = ["id","name", "state_code", "latitude", "longitude", "country_id"];

}
