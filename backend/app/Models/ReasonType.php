<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReasonType extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'reason_sub_system_id', "code", "created_by"];
}
