<?php

namespace App\Models\SingleSales;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterestValue extends Model
{
   
    use HasFactory, UuidTrait;
    protected $table="interest_values";
    protected $fillable = ['interest','sub_interest_category_id'];

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }
}
