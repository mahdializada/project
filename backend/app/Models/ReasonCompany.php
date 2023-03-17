<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReasonCompany extends Model
{
    use HasFactory, SoftDeletes;
    public $table = "reason_company";

}
