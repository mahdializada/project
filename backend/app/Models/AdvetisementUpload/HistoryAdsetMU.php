<?php

namespace App\Models\AdvetisementUpload;

use App\Models\Label;
use App\Models\Remark;
use App\Traits\UuidTrait;
use Illuminate\Support\Carbon;
use App\Models\Advertisement\Platform;
use App\Models\Advertisement\AdAccount;
use Illuminate\Database\Eloquent\Model;
use App\Models\AdvetisementUpload\HistoryAdMU;
use App\Models\AdvetisementUpload\ConnectionMU;
use App\Models\AdvetisementUpload\HistoryCampaignMU;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use App\Repositories\AdvetisementUpload\AdvertisementUtilMU;

class HistoryAdsetMU extends Model
{
    use HasFactory, UuidTrait;
    public static array $STATUSES = ["ACTIVE", "INACTIVE"];

    protected $table = "manual_upload_adsets";

    protected $fillable = [
        'code',
        "adset_id", "name", "status", "currency", "server_campaign_id", "delivery_status", "daily_budget", "bid", "optimization_goal", "pixel_id", "start_time", 'system_status',
        "end_time", "server_created_at", "server_updated_at", "campaign_id", "data_date", "data_timestamp"
    ];

    protected $hidden = ["campaign_id"];
    protected $appends = [];

    public static function nonCountableColumns()
    {
        return [
            "history_adsets.id", "history_adsets.adset_id", "history_adsets.name", "history_adsets.status", "history_adsets.currency", "history_adsets.server_campaign_id", "history_adsets.delivery_status",  "history_adsets.optimization_goal", "history_adsets.pixel_id", "history_adsets.start_time",
            'history_adsets.system_status', "history_adsets.end_time", "history_adsets.server_created_at", "history_adsets.server_updated_at", "history_adsets.campaign_id", "history_adsets.data_date", "history_adsets.data_timestamp", "history_adsets.bid", "history_adsets.created_at", "history_adsets.updated_at"
        ];
    }

    public static function getCountableRawColumns()
    {
        // $columns = ["history_adsets.daily_budget"];
        $columns = [];
        return AdvertisementUtilMU::getSumAndRoundColumns($columns);
    }

    public static function getSelectableColumns()
    {
        $non_countable_columns = self::nonCountableColumns();
        $countable_columns  = self::getCountableRawColumns();
        $ad_countable_columns  = HistoryAdMU::getCountableRawColumns();
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
        return $value ?? "N/A";
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
        return $value ?? 0;
    }

    protected  function getDailyBudgetAttribute($value)
    {
        return $value ?? 0;
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
        return $this->hasMany(HistoryAdMU::class, "server_adset_id", "adset_id")->where($condition);
    }

    public function adsetAds()
    {
        return $this->hasMany(HistoryAdMU::class, "server_adset_id", "adset_id");
    }

    public function campaign()
    {
        return $this->belongsTo(HistoryCampaignMU::class, 'server_campaign_id', 'campaign_id');
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

    public function connections()
    {
        return $this->hasMany(ConnectionMU::class, "server_ad_adset_id", "adset_id");
    }

    /**
     * Get all of the adThroughConnection for the Adsets
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function adThroughConnectionUpload(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryAdMU::class, ConnectionMU::class, "server_ad_adset_id", "ad_id", "adset_id",  "server_ad_id");
    }
    public function campaignThroughConnectionUpload(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryCampaignMU::class, ConnectionMU::class, "server_ad_adset_id", "campaign_id", null,  "server_campaign_id");
    }

    /**
     * Get all of the platform for the campaign
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function platform(): HasOneThrough
    {
        return $this->hasOneThrough(Platform::class, ConnectionMU::class, "server_ad_adset_id", "id", "adset_id",  "platform_id");
    }

    public function adAccount(): HasOneThrough
    {
        return $this->hasOneThrough(AdAccount::class, ConnectionMU::class, "server_ad_adset_id", "id", "adset_id",  "ad_account_id");
    }

}
