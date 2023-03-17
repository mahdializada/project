<?php

namespace App\Models\ProductManagement;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductManagement\Product;
use App\Models\ProductManagement\ReservationRequest;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductRequest extends Model
{
    use HasFactory, UuidTrait;

    protected $table = "pdm_products_requests";

    protected $fillable = [
        "quantity", "status", "prograssive", "product_id", "reservation_request_id",
    ];

    /**
     * Get the product that owns the ProductRequest
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the reservationRequest that owns the ProductRequest
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reservationRequest(): BelongsTo
    {
        return $this->belongsTo(ReservationRequest::class, 'reservation_request_id');
    }
}
