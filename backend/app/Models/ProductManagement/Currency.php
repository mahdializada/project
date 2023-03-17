<?php

namespace App\Models\ProductManagement;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory, UuidTrait;

    public $timestamps = false;

    protected $table = "pdm_currencies";

    protected $fillable = ["name"];
}
