<?php

namespace App\Models\Advertisement;

use App\Models\Label;
use App\Models\Reason;
use App\Models\Remark;
use App\Traits\UuidTrait;
use Illuminate\Support\Carbon;
use App\Models\Advertisement\Platform;
use Illuminate\Database\Eloquent\Model;
use App\Models\Advertisement\Connection;
use App\Models\Advertisement\HistoryAdset;
use App\Models\Advertisement\HistoryCampaign;
use App\Repositories\Advertisement\AdvertisementUtil;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class HistoryAd extends Model
{
    use HasFactory, UuidTrait;
    public static array $STATUSES = ["ACTIVE", "INACTIVE"];
    protected static $customPrimaryKey = 'ad_id';
    public static function setcustomPrimaryKey($key)
    {
        self::$customPrimaryKey = $key;
    }
    protected $fillable = [
        "code", "ad_name", "ad_id",  "server_adset_id", "server_account_id", "status", "view_completion", "delivery_status", "result", "impressions", 'system_status',
        "viewable_impressions", "two_second_video_views", "six_second_video_views", "video_views", "average_screen_time", "spend", "ad_pcode",
        "clicks", "total_page_views", "story_opens", "currency", "reach", "cost_per_fifteen_sec_video_view", "frequency",
        "objective", "optimization_goal", "crm_total_orders", "crm_confirm", "crm_cancelled",
        "crm_repeated", "crm_pickedup", "crm_total_pickedup", "crm_logis_deliverd", "crm_logis_cancelled",
        "crm_total_sale", "crm_product_cost", "crm_delivery_cost", "ga_total_users", "ga_new_users",
        "ga_user_engagement", "ga_sessions", "ga_engaged_sessions", "ga_page_views", "start_time",
        "end_time", "server_created_at", "server_updated_at", "adset_id", "data_date", "data_timestamp", 'crm_refresh_date', 'tikrok_refresh_date', 'facebook_refresh_date', 'google_ads_refresh_date', 'snapchat_refresh_date', "sale_price", "exact_price",
        'sales_type'
    ];



    protected $hidden = [];

    public static function nonCountableColumns()
    {
        return [
            "ad_id as id", "ad_pcode", 'code', "ad_name", "ad_id", "server_adset_id", "server_account_id", "status", "delivery_status", "system_status",
            "currency", "objective", "optimization_goal", "start_time",
            "end_time", "server_created_at", "server_updated_at", "adset_id", "data_date", "data_timestamp", "created_at", "updated_at", 'sales_type'
        ];
    }

    public static function getCountableRawColumns()
    {
        $columns = [
            "spend", "view_completion", "result", "impressions", "viewable_impressions", "two_second_video_views", "six_second_video_views", "video_views", "average_screen_time",  "clicks", "total_page_views", "story_opens", "reach", "cost_per_fifteen_sec_video_view", "frequency", "crm_total_orders", "crm_confirm", "crm_cancelled", "crm_repeated", "crm_pickedup", "crm_total_pickedup", "crm_logis_deliverd", "crm_logis_cancelled", "crm_total_sale", "crm_product_cost", "crm_delivery_cost", "ga_total_users", "ga_new_users", "ga_user_engagement", "ga_sessions", "ga_engaged_sessions", "ga_page_views", "sale_price", "exact_price"
        ];
        return $columns;
    }


    protected  function getServerCreatedAtAttribute($value)
    {
        if ($value)
            return Carbon::parse($value)->toDateString();
        return "N/A";
    }
    protected  function getServerUpdatedAtAttribute($value)
    {
        if ($value)
            return Carbon::parse($value)->toDateString();
        return "N/A";
    }
    protected  function getStartTimeAttribute($value)
    {
        if ($value)
            return Carbon::parse($value)->toDateString();
        return "N/A";
    }
    protected  function getEndTimeAttribute($value)
    {
        if ($value)
            return Carbon::parse($value)->toDateString();
        return "N/A";
    }
    protected  function getDeliveryStatusAttribute($value)
    {
        if ($value)
            return explode(",", $value);
        return [];
    }
    protected  function getTotalPageViewsAttribute($value)
    {
        return $this->notAvailable($value);
    }
    protected  function getBpAttribute($value)
    {
        return $value ? "$value %" : "N/A";
    }
    protected  function getAapAttribute($value)
    {
        return $value ? "$value %" : "N/A";
    }
    protected  function getVqpAttribute($value)
    {
        return $value ? "$value %" : "N/A";
    }
    protected  function getClicksAttribute($value)
    {
        return $this->notAvailable($value);
    }
    protected  function getSpendAttribute($value)
    {
        return $this->addCurrency($value);
    }
    protected  function getTotalSpendTAttribute($value)
    {
        return $this->addCurrency($value);
    }


    protected  function getObjectiveAttribute($value)
    {
        return $this->notAvailable($value);
    }
    protected  function getVideoViewsAttribute($value)
    {
        return $this->notAvailable($value);
    }
    protected  function getViewCompletionAttribute($value)
    {
        return $this->notAvailable($value);
    }
    protected  function getTwoSecondVideoViewsAttribute($value)
    {
        return $this->notAvailable($value);
    }
    protected  function getSixSecondVideoViewsAttribute($value)
    {
        return $this->notAvailable($value);
    }
    protected  function getCostPerFifteenSecVideoViewAttribute($value)
    {
        return round((float)$value, 2) ??  "N/A";
    }
    protected  function getAverageScreenTimeAttribute($value)
    {
        return round($value, 2) ??  "N/A";
    }
    protected function getCreatedAtAttribute($value): string
    {
        return Carbon::create($value)->toDateString();
    }
    protected function getUpdatedAtAttribute($value): string
    {
        return Carbon::create($value)->toDateString();
    }

    protected  function getViewableImpressionsAttribute($value)
    {
        return $this->notAvailable($value);
    }
    protected  function getImpressionsAttribute($value)
    {
        return $this->notAvailable($value);
    }
    protected  function getOptimizationGoalAttribute($value)
    {
        return $this->notAvailable($value);
    }
    protected  function getStoryOpensAttribute($value)
    {
        return $this->notAvailable($value);
    }
    protected  function getStoryOpenRateAttribute($value)
    {
        return $this->notAvailable($value);
    }
    protected  function getReachAttribute($value)
    {
        return $this->notAvailable($value);
    }
    protected  function getFrequencyAttribute($value)
    {
        return $this->notAvailable($value);
    }
    protected  function getGaTotalUsersAttribute($value)
    {
        return $this->notAvailable($value);
    }
    protected  function getGaNewUsersAttribute($value)
    {
        return $this->notAvailable($value);
    }
    protected  function getGaUserEngagementAttribute($value)
    {
        return $this->notAvailable($value);
    }
    protected  function getGaSessionsAttribute($value)
    {
        return $this->notAvailable($value);
    }
    protected  function getGaEngagedSessionsAttribute($value)
    {
        return $this->notAvailable($value);
    }
    protected  function getGaPageViewsAttribute($value)
    {
        return $this->notAvailable($value);
    }
    protected  function getResultAttribute($value)
    {
        return $this->notAvailable($value);
    }
    protected  function getCpmAttribute($value)
    {
        return  $value ? "$value USD" : "N/A";
    }
    protected  function getCpoAttribute($value)
    {
        return $this->notAvailable($value);
    }
    protected  function getGraphAttribute($value)
    {
        return $this->notAvailable($value);
    }
    protected  function getCtrAttribute($value)
    {
        return $this->notAvailable($value);
    }
    protected  function getCppAttribute($value)
    {
        return $this->notAvailable($value);
    }
    protected  function getCpcAttribute($value)
    {
        return  $value ? "$value USD" : "N/A";
    }
    protected  function getAvgSalesPerConfirmAttribute($value)
    {
        return $this->notAvailable($value);
    }
    protected  function getTotalRegisterSalesAttribute($value)
    {
        return $this->notAvailable($value);
    }
    protected  function getAvgSalesOrderAttribute($value)
    {
        return $this->notAvailable($value);
    }

    protected  function getCostPerStoryOpenAttribute($value)
    {
        return $this->addCurrency($value);
    }
    protected  function getCrmConfirmedPercentageAttribute($value)
    {
        return $this->addPercentage($value);
    }
    protected  function getCrmLogisCancelledPercentageAttribute($value)
    {
        return $this->addPercentage($value);
    }
    protected  function getCrmSendBackPercentageAttribute($value)
    {
        return $this->addPercentage($value);
    }
    protected  function getCrmDeliveredPercentageAttribute($value)
    {
        return $this->addPercentage($value);
    }
    protected  function getCrmCancelledPercentageAttribute($value)
    {
        return $this->addPercentage($value);
    }
    protected  function getCrmFinalPercentageAttribute($value)
    {
        return $this->addPercentage($value);
    }
    protected  function getCrmProfitPercentageAttribute($value)
    {
        return $this->addPercentage($value);
    }
    protected  function getCrmProfitLoseAttribute($value)
    {
        return $this->addCurrency($value);
    }
    protected  function getCrmProductCostPercentageAttribute($value)
    {
        return $this->addPercentage($value);
    }
    protected  function getCrmDeliveryCostPercentageAttribute($value)
    {
        return $this->addPercentage($value);
    }
    protected  function getCrmAdCostPercentageAttribute($value)
    {
        return $this->addPercentage($value);
    }
    protected  function getCrmPercentageAttribute($value)
    {
        return $this->addPercentage($value);
    }
    protected  function getGaEngagementRatePercentageAttribute($value)
    {
        return $this->addPercentage($value);
    }
    protected  function getCrmLogisticsPercentageAttribute($value)
    {
        return $this->addPercentage($value);
    }
    protected  function getCrmDifferenceAttribute($value)
    {
        return $this->addCurrency($value);
    }
    protected  function getCrmTotalExpenseAttribute($value)
    {
        return $this->addCurrency($value);
    }

    private function addCurrency($value)
    {
        $value = AdvertisementUtil::round($value);
        return "$value USD";
    }

    private function addPercentage($value)
    {
        $value =  $value ?? 0;
        return "$value %";
    }

    private function notAvailable($value)
    {
        return  $value ?? "N/A";
    }

    // relations
    public function adset()
    {
        return $this->belongsTo(HistoryAdset::class, "server_adset_id", "adset_id");
    }

    public function connection()
    {
        return $this->belongsTo(Connection::class, "ad_id", "server_ad_id");
    }

    public function productProfileInfo(): HasOneThrough
    {
        return $this->hasOneThrough(
            ProductProfileInfo::class,
            Connection::class,
            "server_ad_id", //fk in connection
            "item_code", //fk in product profile info should be the same with key(pcode) in connection
            "ad_id", // pk in history_ads
            "pcode" // key in connection should be the same with fk(item_code) in product profile info
        )->withDefault([
            "prod_max_adver_cost" => "N/A",
            "prod_image" => [],
        ]);
    }
    public function connection_date()
    {
        return $this->belongsTo(Connection::class, "ad_id", "server_ad_id");
    }


    public function labels()
    {
        $this->primaryKey =  self::$customPrimaryKey;
        return $this->morphToMany(
            Label::class,
            'labelable'
        );
        # code...
    }

    public function remarks()
    {
        $this->primaryKey =  self::$customPrimaryKey;
        return $this->morphMany(Remark::class, 'remarkable');
    }

    /**
     * Get all of the campaignThroughConnection for the Ads
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function campaignThroughConnection(): HasOneThrough
    {
        return $this->hasOneThrough(HistoryCampaign::class, Connection::class, "server_ad_id", "campaign_id", "ad_id",  "server_campaign_id");
    }
    public function campaigns(): HasOneThrough
    {
        return $this->hasOneThrough(HistoryCampaign::class, Connection::class, "server_ad_id", "campaign_id", "ad_id",  "server_campaign_id");
    }


    /**
     * Get all of the platform for the campaign
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function platform(): HasOneThrough
    {
        return $this->hasOneThrough(Platform::class, Connection::class, "server_ad_id", "id", "ad_id",  "platform_id");
    }


    public function reasonables()
    {
        $this->primaryKey =  self::$customPrimaryKey;
        return $this->morphToMany(Reason::class, 'reasonable')->withPivot('status');
    }
}
