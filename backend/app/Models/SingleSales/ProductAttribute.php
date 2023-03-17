<?php

namespace App\Models\SingleSales;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    use HasFactory, UuidTrait;
    protected $table = "product_attribute_ssp";
    protected $fillable = ['id','product_id','type', 'attribute_id', 'attribute_value'];


    public function getAttributeValueAttribute($value){
        return json_decode($value);
    }
    public function productInventory()
    {
        return $this->belongsTo(ProductInventory::class, 'product_inventory_id');
    }
    public function attributes()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }
}
