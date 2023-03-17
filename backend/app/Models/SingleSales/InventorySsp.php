<?php

namespace App\Models\SingleSales;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventorySsp extends Model
{
    use HasFactory,UuidTrait;
    protected $table = "product_inventory_ssp";

    protected $fillable = ["sku","quantity", "price_per_unit", "product_id"];


    /**
     * Get the product that owns the InventorySsp
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'product_attribute_ssp','attribute_id','product_inventory_id')->withPivot('attribute_value');
    }
    public function productAttribute()
    {
        return $this->hasMany(ProductAttribute::class,'product_inventory_id','id');
    }
}
