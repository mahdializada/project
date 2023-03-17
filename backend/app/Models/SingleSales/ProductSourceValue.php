<?php

namespace App\Models\SingleSales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSourceValue extends Model
{

    use HasFactory;
    protected $guarded = ['created_at', 'updated_at'];
    protected $table = 'product_source_values';
}
