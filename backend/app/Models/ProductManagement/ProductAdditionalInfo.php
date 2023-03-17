<?php

namespace App\Models\ProductManagement;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductManagement\ProductList;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductAdditionalInfo extends Model
{
    use HasFactory, UuidTrait;

    public $timestamps = false;

    protected $table = "pdm_ispp_product_additional_info";

    protected $fillable = [
        "type", "reference_text", "ispp_product_list_id"
    ];

    /**
     * Get the productList that owns the ProductAdditionalInfo
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productList(): BelongsTo
    {
        return $this->belongsTo(ProductList::class, 'ispp_product_list_id');
    }
}
