<?php

namespace App\Models\ProductManagement;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SourcingOrderProduct extends Model
{
    use HasFactory, UuidTrait;

    protected $table = "pdm_sourcing_order_products";

    protected $fillable = [
        "approx_quantity", "target_cost", "variation_note", "product_note", "reference",
        "status", "sourcing_order_id", "product_id"
    ];

    /**
     * Get the sourcingOrder that owns the SourcingOrderProduct
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sourcingOrder(): BelongsTo
    {
        return $this->belongsTo(SourcingOrder::class, 'sourcing_order_id');
    }

    /**
     * Get the product that owns the SourcingOrderProduct
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

}
