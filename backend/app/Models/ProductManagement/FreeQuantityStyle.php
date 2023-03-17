<?php

namespace App\Models\ProductManagement;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductManagement\Request;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FreeQuantityStyle extends Model
{
    use HasFactory;

    protected $table = "pdm_free_quantity_style";

    public $timestamps = false;

    protected $fillable = ["ispp_request_id", "number_of_items", "free_item"];



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
