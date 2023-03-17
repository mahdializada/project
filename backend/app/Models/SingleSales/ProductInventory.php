<?php

namespace App\Models\SingleSales;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductInventory extends Model
{
    protected $table = "product_inventory_ssp";
    protected $fillable = ['product_id', 'sku', 'quantity', 'price_per_unit'];
    use HasFactory, UuidTrait, SoftDeletes;
}
