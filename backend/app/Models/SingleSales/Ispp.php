<?php

namespace App\Models\SingleSales;

use App\Models\Company;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Language;
use App\Models\Reason;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Current;

class Ispp extends Model
{

    protected $table = 'ispp_ssp';
    protected $fillable = [
        'code', 'company_id', 'working_type','product_type', 'priority', 'product_availablity', 'is_seasonal', 'season_or_event_name', 'available_quantity', 'product_source_value_id', 'sale_note', 'product_note', 'chanel_note', 'financial_note', 'head_selling_price', 'currency_id','status'
    ];
    use HasFactory, SoftDeletes, UuidTrait;

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function productStudy()
    {
        return $this->belongsTo(ProductStudy::class, 'product_study_id');
    }
    public function displayLanguage()
    {
        return $this->belongsTo(Language::class, 'display_language_id');
    }
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function targetSaleCountry()
    {
        return $this->belongsToMany(Country::class, "ispp_target_sale_countries_ssp", "ispp_id", "country_id");
    }

    public function actions()
    {
        return $this->morphMany(Action::class, 'dependable');
    }

    public function reasons()
    {
        return $this->morphToMany(Reason::class, 'reasonable');
    }


    public function studies()
    {
        return $this->morphMany(ProductStudy::class, 'studyable');
    }
}
