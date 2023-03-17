<?php

namespace App\Models\ProductManagement;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductManagement\Request;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MultipleQuantityStyle extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = "pdm_multiple_quantity_style";

    protected $fillable = ["ispp_request_id", "number_of_items", "total_price"];


    /**
     * Get the request that owns the MultipleQuantityStyle
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class, 'ispp_request_id');
    }
}
