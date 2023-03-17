<?php

namespace App\Models\SingleSales;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStudyFormFulfillment extends Model
{
    use HasFactory,UuidTrait;
    protected $table="product_study_form_fulfillment";
    protected $fillable = ['study_form_answer','study_form_components_id'];
   
}
