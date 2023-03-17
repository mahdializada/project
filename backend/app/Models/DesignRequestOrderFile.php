<?php

namespace App\Models;

use App\Models\User;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DesignRequestOrderFile extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = [
        'url',
        "type",
        "name",
        "size",
        "changed_by",
        "design_request_order_id",
    ];

    public function getUrlAttribute($value): string
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        return env("APP_URL") . Storage::url($value);
    }

    /**
     * Get the user that owns the DesignRequestOrderFile
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function designRequestOrder()
    {
        return $this->belongsTo(DesignRequestOrder::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, "changed_by");
    }
}
