<?php

namespace App\Models\ProductManagement;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductManagement\ProductRequest;
use App\Traits\UuidTrait;
use App\Models\SingleSales\QuantityReservationProducts;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchasedProductInfo extends Model
{
    use HasFactory,UuidTrait;

    protected $table = "pdm_purchased_products_info";

    protected $fillable = [
        "order_no", "arrival_time", "purchase_date", "product_requests_id"
    ];

    /**
     * Get the productRequest that owns the PurchasedProductInfo
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productRequest(): BelongsTo
    {
        return $this->belongsTo(ProductRequest::class, 'product_request_id');
    }
// new coding
    public function QuantityReservationProducts(): BelongsTo
    {
        return $this->belongsTo(QuantityReservationProducts::class,'quantity_reservation_id');
    }

}
