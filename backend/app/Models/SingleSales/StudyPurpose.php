<?php

namespace App\Models\SingleSales;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

 

class StudyPurpose extends Model
{
    use HasFactory, UuidTrait;
    protected $fillable = ["study_purpose"];
}
