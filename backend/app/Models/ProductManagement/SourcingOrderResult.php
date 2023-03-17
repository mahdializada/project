<?php

namespace App\Models\ProductManagement;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\ProductManagement\SourcingOrderProduct;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SourcingOrderResult extends Model
{
    use HasFactory, UuidTrait;

    protected $table = "pdm_sourcing_orders_results";

    protected $fillable = [
        "available_quantities", "cost", "shipping_method", "delivery_time", "quantity_model",
        "shipping_cost", "product_note", "import_restrictions", "import_note", "sourcing_note",
        "progressive", "sourcing_order_product_id", "supplier_id"
    ];

    /**
     * Get the sourcingOrderProduct that owns the SourcingOrderResult
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sourcingOrderProduct(): BelongsTo
    {
        return $this->belongsTo(SourcingOrderProduct::class, 'sourcing_order_product_id');
    }

}
