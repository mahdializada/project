<?php

namespace App\Models\SingleSales;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeCategory extends Model
{
    use HasFactory;
    protected $table = "attribute_category_ssp";
    protected $fillable = ['category_id', 'attribute_id'];
}
