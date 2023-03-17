<?php

namespace App\Models\SingleSales;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    protected $table = "suppliers_ssp";
    protected $guarded=['created_at','updated_at'];
    use HasFactory, UuidTrait, SoftDeletes;
}
