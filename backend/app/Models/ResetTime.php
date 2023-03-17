<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResetTime extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = ["user_id"];

    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
