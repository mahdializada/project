<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepartmentSurvey extends Model
{
    use HasFactory, SoftDeletes, UuidTrait;
    protected static $types = ["Not Started", "In Progress", "Completed"];

    public static function getTypes()
    {
        return DepartmentSurvey::$types;
    }
}
