<?php

namespace App\Models\Advertisement;

use App\Models\CloudAttachment;
use App\Models\Company;
use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Storage;

class ProductProfileInfo extends Model
{
    use HasFactory, UuidTrait;


    protected $fillable = ['code', 'state', 'item_code', 'prod_sales_stability', 'prod_source', 'prod_sale_goal', 'prod_style', 'prod_section', 'prod_new_product_source', 'prod_work_type', 'prod_import_source', 'prod_production_type', 'prod_cost', 'prod_available_quantity', 'prod_max_adver_cost', 'prod_profitability', 'prod_profit', 'prod_image'];

    protected  function getProdImageAttribute($value)
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        if (!$value) {
            return [];
        }
        return json_decode($value);
        // $img = [];
        // foreach ($value as $key => $val) {
        //     $img[] = env("APP_URL") . Storage::url($val);
        // }
        // return $img;
    }

    protected  function getProdSalesStabilityAttribute($value)
    {
        if ($value)
            return $value;
        return "N/A";
    }
    protected  function getProdProfitabilityAttribute($value)
    {
        if ($value)
            return $value;
        return "N/A";
    }
    protected  function getProdProfitAttribute($value)
    {
        if ($value)
            return $value;
        return "N/A";
    }

    protected  function getProdNewProductSourceAttribute($value)
    {
        return json_decode($value);
    }

    protected  function getProdImportSourceAttribute($value)
    {
        return json_decode($value);
    }

    public function connections()
    {
        return $this->hasMany(Connection::class, "pcode", 'item_code');
    }

    public function countries(): HasManyThrough
    {

        return $this->HasManyThrough(Country::class, Connection::class, "pcode", "id", "item_code",  "country_id");
    }
    public function companies(): HasManyThrough
    {
        return $this->HasManyThrough(Company::class, Connection::class, "pcode", "id", "item_code",  "company_id");
    }

    public function attachments()
    {
        $this->primaryKey = "item_code";
        return $this->morphMany(CloudAttachment::class, 'attachmentable');
    }
}
