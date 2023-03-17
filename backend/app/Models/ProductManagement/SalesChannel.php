<?php

namespace App\Models\ProductManagement;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesChannel extends Model
{
    use HasFactory, UuidTrait;

    protected $table = "pdm_sales_channels";

    public $timestamps = false;

    protected $fillable = [
        "channel_name"
    ];
}
