<?php

namespace App\Models\SingleSales;

use App\Models\Company;
use App\Models\Language;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IsppTargetSaleCountry extends Model
{

    protected $table = 'ispp_target_sale_countries_ssp';
    protected $fillable = [
        'country_id', 'ispp_id'
    ];
    use HasFactory;
}
