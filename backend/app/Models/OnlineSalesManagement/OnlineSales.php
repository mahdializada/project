<?php

namespace App\Models\OnlineSalesManagement;

use App\Models\Advertisement\ItemStatus;
use App\Models\Advertisement\ProductProfileInfo;
use App\Models\Advertisement\Project;
use App\Models\CloudAttachment;
use App\Models\Company;
use App\Models\Country;
use App\Models\Label;
use App\Models\ProductManagement\ChangedRecord;
use App\Models\Reason;
use App\Models\Remark;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OnlineSales extends Model
{

    protected static $customPrimaryKey = 'id';

    public static function setcustomPrimaryKey($key)
    {
        self::$customPrimaryKey = $key;
    }
    use HasFactory, UuidTrait, SoftDeletes;
    protected $fillable = ['code', 'product_name', 'product_name_arabic', 'status','page_link', 'product_code', 'max_code', 'country_id', 'company_id', 'project_id', 'sales_type'];

    protected static $types = ["active", "inactive"];

    public static function getTypes()
    {
        return OnlineSales::$types;
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function remarks()
    {
        $this->primaryKey =  self::$customPrimaryKey;
        return $this->morphMany(Remark::class, 'remarkable');
    }
    public function labels()
    {
        $this->primaryKey = "product_code";
        return $this->morphToMany(
            Label::class,
            'labelable'
        );
    }
    public function SalesTypeLabels()
    {
        $this->primaryKey = "sales_type";
        return $this->morphToMany(
            Label::class,
            'labelable'
        );
    }


    public function ProductInfo()
    {
        return $this->belongsTo(ProductProfileInfo::class, 'product_code', 'item_code')->withDefault([
            "state" => "N/A",
            "prod_sales_stability" => "N/A",
            "prod_source" => "N/A",
            "prod_sale_goal" => "N/A",
            "prod_style" => "N/A",
            "prod_section" => "N/A",
            "prod_work_type" => "N/A",
            "prod_production_type" => "N/A",
            "prod_cost" => "N/A",
            "prod_available_quantity" => "N/A",
            "prod_max_adver_cost" => "N/A",
            "prod_profitability" => "N/A",
            "prod_profit" => "N/A",
        ]);
    }

    public function itemStatus()
    {

        return $this->hasOne(ItemStatus::class, 'item_code', 'product_code')->where('isActive', true)->withDefault([
            "item_code" => "N/A",
            "country_id" => "N/A",
            "item_status" => 'new_sales',
            "isActive" => true,
        ]);
    }
    public function itemStatus2()
    {

        return $this->hasOne(ItemStatus::class, 'item_code', 'product_code')->where('isActive', true)->withDefault([
            "item_code" => "N/A",
            "country_id" => "N/A",
            "item_status" => 'ready_to_sale',
            "isActive" => true,
        ]);
    }
    public function allItemStatus()
    {
        return $this->hasMany(ItemStatus::class, 'item_code', 'product_code');
    }




    public function currentStatus()
    {
        $this->primaryKey =  self::$customPrimaryKey;
        return $this->morphMany(
            ChangedRecord::class,
            'changeable',
            null,
            'changeable_id'
        );
    }

    public function reasonables($primary_key)
    {
        $this->primaryKey = $primary_key;
        return $this->morphToMany(Reason::class, 'reasonable', 'reasonables', 'reasonable_id', null, $this->primaryKey)->withPivot('status');
    }
    public function onlineSalesNote()
    {
        return $this->hasMany(OnlineSalesNote::class, 'product_code', 'product_code');
    }
}
