<?php

namespace App\Models\ProductManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = "pdm_interest";

    protected $fillable = ["interest_value_id", "interestable_type", "interestable_id"];
}
