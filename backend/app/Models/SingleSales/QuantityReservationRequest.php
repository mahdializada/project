<?php

namespace App\Models\SingleSales;

use App\Models\Country;
use App\Models\ProductManagement\PurchasedProductInfo;
use App\Models\Reason;
use App\Models\State;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuantityReservationRequest extends Model
{
    use HasFactory, UuidTrait, SoftDeletes;
    protected $table = "quantity_reservation_requests_ssp";
    protected $fillable = [
        'code',
        'products_note',
        'shipping_method',
        'products_note',
        'shipping_note',
        'status',
        'importing_state_id',
    ];



    public function receivingState()
    {
        return $this->belongsTo(State::class, 'importing_state_id');
    }

    public function receivingCountry()
    {
        return $this->belongsTo(Country::class, 'receiving_country_id');
    }

    public function Info()
    {
        return $this->hasMany(PurchasedProductInfo::class,'product_requests_id');
    }

   public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'quantity_reservation_products',
            'quantity_reservation_id',
            'product_id',

        )->withPivot('quantity');
    }



    public function reasons()
    {
        return $this->morphToMany(Reason::class, 'reasonable')->withTimestamps();
    }
}
