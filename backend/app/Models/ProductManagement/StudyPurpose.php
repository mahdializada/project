<?php

namespace App\Models\ProductManagement;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyPurpose extends Model
{
    use HasFactory, UuidTrait;
    protected $table = "pdm_study_purposes";

    protected $fillable = [ "study_purpose"];

}
