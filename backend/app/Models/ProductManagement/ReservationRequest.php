<?php

namespace App\Models\ProductManagement;

use App\Models\State;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReservationRequest extends Model
{
    use HasFactory, UuidTrait;

    protected $table = "pdm_reservation_requests";

    protected $fillable = [
        "is_group", "status", "products_note", "shipping_note", "shipping_mehtod",
        "company_id", "importing_city_id", "reason_id"
    ];


    /**
     * Get the company that owns the ReservationRequest
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Get the importingCity that owns the ReservationRequest
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function importingCity(): BelongsTo
    {
        return $this->belongsTo(State::class, 'importing_city_id');
    }
}
