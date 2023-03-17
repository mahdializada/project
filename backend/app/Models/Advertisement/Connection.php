<?php

namespace App\Models\Advertisement;

use App\Models\Company;
use App\Models\Country;
use App\Traits\UuidTrait;
use App\Models\SingleSales\Ispp;
use App\Models\Advertisement\Project;
use App\Models\Advertisement\Platform;
use App\Models\Advertisement\AdAccount;
use App\Models\Advertisement\HistoryAd;
use Illuminate\Database\Eloquent\Model;
use App\Models\Advertisement\Application;
use App\Models\Label;
use App\Models\OnlineSalesManagement\OnlineSalesNote;
use App\Models\Reason;
use App\Models\Remark;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Psy\ExecutionLoop\RunkitReloader;

class Connection extends Model
{
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;
    use HasFactory, UuidTrait;
    public static array $STATUSES = ["ACTIVE", "INACTIVE"];

    protected $fillable = [
        "pcode", "code", "hashed_code", "sales_type", "pname", "generated_link", "server_account_id", "server_ad_id", "landing_page_link", "media_id", "media_link", "media_size", "currency", "timezone", 'system_status', "server_campaign_id", "server_ad_adset_id",
        "ispp_id", "platform_id", "application_id", "project_id", "ad_account_id", "company_id", "country_id", "history_ad_id",
    ];

    public static $salesType = ["Single Sale", "Shopping Cart", 'WhatsApp'];

    public function application()
    {
        return $this->belongsTo(Application::class,);
    }

    public function adAccount()
    {
        return $this->belongsTo(AdAccount::class);
    }

    public function productProfileInfo()
    {
        return $this->hasOne(ProductProfileInfo::class, 'item_code', 'pcode')->withDefault([
            "state" => "N/A",
            "prod_sales_stability" => "N/A",
            "prod_source" => "N/A",
            "prod_sale_goal" => "N/A",
            "prod_style" => "N/A",
            "prod_section" => "N/A",
            "prod_new_product_source" => "N/A",
            "prod_work_type" => "N/A",
            "prod_import_source" => "N/A",
            "prod_production_type" => "N/A",
            "prod_cost" => "N/A",
            "prod_available_quantity" => "N/A",
            "prod_max_adver_cost" => "N/A",
            "prod_profitability" => "N/A",
            "prod_profit" => "N/A",
        ]);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, "company_id");
    }

    public function country()
    {
        return $this->belongsTo(Country::class, "country_id");
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class, "platform_id");
    }
    public function campaigns()
    {
        return $this->belongsTo(
            HistoryCampaign::class,
            "server_campaign_id",
            'campaign_id'
        );
    }

    public function project()
    {
        return $this->belongsTo(Project::class, "project_id");
    }

    public function ispp()
    {
        return $this->belongsTo(Ispp::class, "ispp_id");
    }

    public function todayHistoryAd()
    {
        $today = Carbon::today()->toDateString();
        $condition = [["data_date", "=", $today]];
        return $this->hasOne(HistoryAd::class, 'ad_id',  "server_ad_id")->where($condition);
    }

    public function historyAd()
    {
        return $this->hasOne(HistoryAd::class, 'ad_id',  "server_ad_id");
    }

    public function historyAdds()
    {
        return $this->hasMany(HistoryAd::class, 'ad_id',  "server_ad_id");
    }
    public function historyCampians()
    {

        return $this->hasMany(HistoryCampaign::class, 'campaign_id',  "server_campaign_id");
    }
    public function historyAddsets()
    {
        return $this->hasMany(HistoryAdset::class, 'adset_id',  "server_ad_adset_id");
    }

    public function itemCodeHistoryAds()
    {

        return $this->hasMany(HistoryAd::class, 'ad_pcode',  "pcode");
    }

    public function SalesTypeHistoryAds()
    {

        return $this->hasMany(HistoryAd::class, 'sales_type',  "sales_type");
    }
    public function labels()
    {
        $this->primaryKey = "pcode";
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

    public function remarks()
    {
        $this->primaryKey = "pcode";
        return $this->morphMany(Remark::class, 'remarkable');
    }


    public function adsets()
    {

        return $this->hasManyThrough(
            HistoryAdset::class,
            HistoryCampaign::class,
            'campaign_id',
            'server_campaign_id',
            'server_campaign_id',
            'campaign_id',
        )->groupBy('history_adsets.id');
    }


    public function reasonables()
    {
        $this->primaryKey = "pcode";
        return $this->morphToMany(Reason::class, 'reasonable')->withPivot('status', 'created_at', 'id');
    }

    public function reasonables2()
    {
        $this->primaryKey = "pcode";
        return $this->morphToMany(Reason::class, 'reasonable')->withPivot('status', 'created_at')
            ->orderBy('reasonables.created_at', 'desc');
        // ->groupBy(DB::raw('DATE(reasonables.created_at)'));
    }

    public function salesTypeReasonable()
    {
        $this->primaryKey = "sales_type";
        return $this->morphToMany(Reason::class, 'reasonable')->withPivot('status', 'created_at', 'id');
    }
    public function itemStatus()
    {
        return $this->hasOne(ItemStatus::class, 'item_code', 'pcode')->where('isActive',true);
    }
    public function allItemStatus()
    {
        return $this->hasMany(ItemStatus::class, 'item_code', 'pcode');
    }
    public function onlineSalesNote()
    {
        return $this->hasMany(OnlineSalesNote::class, 'product_code', 'pcode');
    }
}
