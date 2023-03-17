<?php

namespace App\Models\ProductManagement;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\ProductManagement\SalesChannel;

class SalesChannelTemplate extends Model
{
    use HasFactory, UuidTrait;

    protected $table = "pdm_sales_channel_templates";

    public $timestamps = false;

    protected $fillable = [
        "template", "sales_channel_id"
    ];

    /**
     * Get the salesChannel that owns the SalesChannelTemplate
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salesChannel(): BelongsTo
    {
        return $this->belongsTo(SalesChannel::class, 'sales_channel_id');
    }
}
