<?php

namespace App\Models\SingleSales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSource extends Model
{

    use HasFactory;
    protected $guarded = ['created_at', 'updated_at'];
    protected $table = 'product_sources';
    protected $fillable = ['source_name','parent_id'];
}
