<?php

namespace App\Models\ProductManagement;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductManagement\Attribute;
use App\Models\ProductManagement\Inventory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductAttribute extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = "pdm_product_attributes";

    protected $fillable = [
        "attribute_value", "inventory_id", "attribute_id"
    ];


    /**
     * Get the inventory that owns the ProductAttribute
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    /**
     * Get the attribute that owns the ProductAttribute
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }
}
