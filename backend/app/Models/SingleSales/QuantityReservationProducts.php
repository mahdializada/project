<?php

namespace App\Models\SingleSales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductManagement\PurchasedProductInfo;


class QuantityReservationProducts extends Model
{
    use HasFactory;

    protected $table = "quantity_reservation_products";
    protected $fillable = [
        'quantity',
        'status',
        'product_id',
        'quantity_reservation_id',
        'purchase_order_number',
        'arrival_time',
        'purchase_date',
    ];
// new codesing
    public function purchaseds(){
        return $this->hasMany(PurchasedProductInfo::class);
    }
}
