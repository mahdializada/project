<?php

namespace App\Models\ProductManagement;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductManagement\ProductSource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SourceValue extends Model
{
    use HasFactory, UuidTrait;

    protected $table = "pdm_sources_values";

    public $timestamps = false;

    protected $fillable = [
        "value", "product_source_id"
    ];


    /**
     * Get the productSource that owns the SourceValue
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productSource(): BelongsTo
    {
        return $this->belongsTo(ProductSource::class, 'product_source_id');
    }
}
