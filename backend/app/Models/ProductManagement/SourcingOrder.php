<?php

namespace App\Models\ProductManagement;

use App\Models\Company;
use App\Models\Country;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SourcingOrder extends Model
{
    use HasFactory, UuidTrait;


    protected $table = "pdm_sourcing_orders";

    protected $fillable = [
        "sourcing_type", "is_group", "required_shipping_method", "status", "description",
        "progressive", "searching_reason_id", "company_id", "importing_city",
    ];


    /**
     * Get the company that owns the SourcingOrder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'pdm_sourcing_orders_countries', 'sourcing_order_id', 'country_id');
    }
}
