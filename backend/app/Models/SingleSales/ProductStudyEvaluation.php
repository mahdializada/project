<?php

namespace App\Models\SingleSales;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStudyEvaluation extends Model
{
  

    use HasFactory, UuidTrait;
    protected $fillable = ['product_study_id', 'user_id','rating','comment'];
  
}
