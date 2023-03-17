<?php

namespace App\Models\Advertisement;

use App\Models\Label;
use App\Models\Reason;
use App\Models\Remark;
use App\Traits\UuidTrait;
use Illuminate\Support\Carbon;
use App\Models\Advertisement\Platform;
use App\Models\Advertisement\HistoryAd;
use Illuminate\Database\Eloquent\Model;
use App\Models\Advertisement\Connection;
use App\Models\Advertisement\HistoryCampaign;
use App\Models\CommonChangesHistory;
use App\Models\Country;
use App\Repositories\Advertisement\AdvertisementUtil;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class HistoryAdset extends Model
{
    use HasFactory, UuidTrait;
    public static array $STATUSES = ["ACTIVE", "INACTIVE"];


    protected $fillable = [
        'code',
        "adset_id", "name", "status", "currency", "server_campaign_id", "delivery_status", "daily_budget", "bid", "optimization_goal", "pixel_id", "start_time", 'system_status',
        "end_time", "server_created_at", "server_updated_at", "campaign_id", "data_date", "data_timestamp", 'gender', 'age_groups',
        "locations", 'languages', 'network_types', 'operating_systems', 'device_models', 'placements', 'placement_type', 'smart_targeting', 'promotion_type'
    ];
    // 'gender', 'age_groups',
    // "locations", 'languages', 'network_types', 'operating_systems', 'device_models', 'placements', 'placement_type', 'smart_targeting'


    protected $hidden = ["campaign_id"];
    protected $appends = [];

    public static function nonCountableColumns()
    {
        return [
            "history_adsets.id", "history_adsets.adset_id", "history_adsets.name", "history_adsets.status", "history_adsets.currency", "history_adsets.server_campaign_id", "history_adsets.delivery_status",  "history_adsets.optimization_goal", "history_adsets.pixel_id", "history_adsets.start_time",
            'history_adsets.system_status', "history_adsets.end_time", "history_adsets.server_created_at", "history_adsets.server_updated_at", "history_adsets.campaign_id", "history_adsets.data_date", "history_adsets.data_timestamp", "history_adsets.bid", "history_adsets.created_at", "history_adsets.updated_at"
        ];
    }


    protected  function getAgeGroupsAttribute($value)
    {
        if ($value)
            return json_decode($value);
        return [];
    }
    protected  function getNetworkTypesAttribute($value)
    {
        if ($value)
            return json_decode($value);
        return [];
    }
    protected  function getOperatingSystemsAttribute($value)
    {
        if ($value)
            return json_decode($value);
        return [];
    }
    protected  function getDeviceModelsAttribute($value)
    {
        if ($value)
            return json_decode($value);
        return [];
    }
    protected  function getPlacementsAttribute($value)
    {
        if ($value)
            return json_decode($value);
        return [];
    }
    protected  function getLanguagesAttribute($value)
    {
        if ($value)
            return json_decode($value);
        return [];
    }


    protected  function getLocationsAttribute($value)
    {
        if ($value) {

            $location = json_decode($value);
            return $location;
        }
        // if (count($location) > 0) {
        //     $location =  array_change_key_case($location, CASE_UPPER);
        //     return  Country::whereIn('iso2', $location)->select(['name', 'iso2'])->get();
        // }

        return [];
    }
    public static function getCountableRawColumns()
    {
        // $columns = ["history_adsets.daily_budget"];
        $columns = [];
        return AdvertisementUtil::getSumAndRoundColumns($columns);
    }

    public static function getSelectableColumns()
    {
        $non_countable_columns = self::nonCountableColumns();
        $countable_columns  = self::getCountableRawColumns();
        $ad_countable_columns  = HistoryAd::getCountableRawColumns();
        return array_merge($non_countable_columns, $countable_columns, $ad_countable_columns);
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

    protected  function getObjectiveTypeAttribute($value)
    {
        return $value ?? "N/A";
    }
    protected  function getBudgetModeAttribute($value)
    {
        return $value ?? "N/A";
    }
    protected  function getBudgetAttribute($value)
    {
        return $this->addCurrency($value);
    }
    protected  function getCampaignTypeAttribute($value)
    {
        return $value ?? "N/A";
    }
    protected  function getObjectiveAttribute($value)
    {
        return $value ?? "N/A";
    }

    protected  function getBidAttribute($value)
    {
        return $this->addCurrency($value, true);
    }

    protected  function getDailyBudgetAttribute($value)
    {
        return $this->addCurrency($value);
    }
    protected  function getTotalBudgetAttribute($value)
    {
        return $this->addCurrency($value);
    }
    protected  function getOptimizationGoalAttribute($value)
    {
        return $value ?? "N/A";
    }
    protected  function getPixelIdAttribute($value)
    {
        return $value ?? "N/A";
    }

    protected  function getAdSumResultAttribute($value)
    {
        return $value ?? 0;
    }

    protected  function getAdSumTotalPageViewsAttribute($value)
    {
        return $value ?? 0;
    }


    protected  function getAdSumCrmConfirmedPercentageAttribute($value)
    {
        return $value ?? 0;
    }

    protected  function getAdSumCrmConfirmAttribute($value)
    {
        return (int) $value ?? 0;
    }

    protected  function getAdSumCrmCancelledAttribute($value)
    {
        return (int) $value ?? 0;
    }

    protected  function getAdSumVideoViewsAttribute($value)
    {
        return $value ?? 0;
    }

    // relations

    public function ad()
    {
        $today = Carbon::today()->toDateString();
        $condition = [["data_date", "=", $today]];
        return $this->hasMany(HistoryAd::class, "server_adset_id", "adset_id")->where($condition);
    }

    public function adsetAds()
    {
        return $this->hasMany(HistoryAd::class, "server_adset_id", "adset_id");
    }

    public function campaign()
    {
        return $this->belongsTo(HistoryCampaign::class, 'server_campaign_id', 'campaign_id');
    }

    public function labels()
    {
        $this->primaryKey = "adset_id";
        return $this->morphToMany(
            Label::class,
            'labelable'
        );
        # code...
    }

    public function remarks()
    {
        $this->primaryKey = "adset_id";
        return $this->morphMany(Remark::class, 'remarkable');
    }
    public function changesHistory()
    {
        $this->primaryKey = "adset_id";
        return $this->morphMany(CommonChangesHistory::class, 'changeable');
    }

    public function connections()
    {
        return $this->hasMany(Connection::class, "server_ad_adset_id", "adset_id");
    }
    public function connection_date()
    {
        return $this->hasOne(Connection::class, "server_ad_adset_id", "adset_id");
    }

    /**
     * Get all of the adThroughConnection for the Adsets
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function adThroughConnection(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryAd::class, Connection::class, "server_ad_adset_id", "ad_id", "adset_id",  "server_ad_id");
    }
    public function campaignThroughConnection(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryCampaign::class, Connection::class, "server_ad_adset_id", "campaign_id", null,  "server_campaign_id");
    }

    /**
     * Get all of the platform for the campaign
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function productProfileInfo(): HasOneThrough
    {
        return $this->hasOneThrough(ProductProfileInfo::class, Connection::class, "server_ad_adset_id", "item_code", "adset_id",  "pcode")->withDefault(["prod_max_adver_cost" => "N/A"]);
    }
    public function platform(): HasOneThrough
    {
        return $this->hasOneThrough(Platform::class, Connection::class, "server_ad_adset_id", "id", "adset_id",  "platform_id");
    }

    public function adAccount(): HasOneThrough
    {
        return $this->hasOneThrough(AdAccount::class, Connection::class, "server_ad_adset_id", "id", "adset_id",  "ad_account_id");
    }


    public function reasonables()
    {
        $this->primaryKey = "adset_id";
        return $this->morphToMany(Reason::class, 'reasonable')->withPivot('status');
    }
    private function addCurrency($value, $bid = false)
    {
        $value = AdvertisementUtil::round($value);
        return "$value USD";
    }
    public function inactiveAds()
    {
        return $this->hasMany(HistoryAd::class, "adset_id", "id");
    }
}
