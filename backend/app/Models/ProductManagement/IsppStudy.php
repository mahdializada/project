<?php

namespace App\Models\ProductManagement;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IsppStudy extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = "pdm_ispp_studies";

    protected $fillable = ["study_id", "ispp_request_id", "study_note"];
}
