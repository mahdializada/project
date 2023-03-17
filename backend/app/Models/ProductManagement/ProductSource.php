<?php

namespace App\Models\ProductManagement;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductSource extends Model
{
    use HasFactory, UuidTrait;

    public $timestamps = false;

    protected $table = "pdm_product_sources";

    protected $fillable = [
        "source_name", "parent_id"
    ];



    /**
     * Get the parent that owns the ProductSource
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}
