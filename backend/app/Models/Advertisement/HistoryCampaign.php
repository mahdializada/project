<?php

namespace App\Models\Advertisement;

use App\Models\Label;
use App\Models\Reason;
use App\Models\Remark;
use App\Traits\UuidTrait;
use Illuminate\Support\Carbon;
use App\Models\Advertisement\Platform;
use App\Models\Advertisement\AdAccount;
use App\Models\Advertisement\HistoryAd;
use Illuminate\Database\Eloquent\Model;
use App\Models\Advertisement\Connection;
use App\Models\Advertisement\HistoryAdset;
use App\Repositories\Advertisement\AdvertisementUtil;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;


class HistoryCampaign extends Model
{
    use HasFactory, UuidTrait;
    public static array $STATUSES = ["ACTIVE", "INACTIVE"];

    protected $fillable = [
        'code',
        "campaign_id", "name", "status", "server_account_id", "delivery_status", "objective_type",
        "budget_mode", "budget", "campaign_type", "objective", "start_time", 'system_status',
        "end_time", "server_created_at", "server_updated_at", "ad_account_id", "data_date", "data_timestamp"
    ];
    protected $hidden = ["ad_account_id"];


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
    protected  function getDeliveryStatusAttribute($value)
    {
        return $value ?? "N/A";
    }


    // relations
    public function adAccount()
    {
        return $this->belongsTo(AdAccount::class, "ad_account_id");
    }
    private function addCurrency($value)
    {
        $value = AdvertisementUtil::round($value);
        return "$value USD";
    }
    public function adset()
    {
        $today = Carbon::today()->toDateString();
        $condition = [["data_date", "=", $today]];
        return $this->hasMany(HistoryAdset::class, 'server_campaign_id', 'campaign_id')->where($condition);
    }

    public function campaignAdsets()
    {
        return $this->hasMany(HistoryAdset::class, 'server_campaign_id', 'campaign_id');
    }

    public function connections()
    {
        return $this->hasMany(Connection::class, "server_campaign_id", "campaign_id");
    }
    public function connection_date()
    {
        return $this->hasOne(Connection::class, "server_campaign_id", "campaign_id");
    }

    /**
     * Get all of the adThroughConnection for the Account
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function adThroughConnection(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryAd::class, Connection::class, "server_campaign_id", "ad_id", "campaign_id",  "server_ad_id");
    }
    public function productProfileInfo(): HasOneThrough
    {
        return $this->hasOneThrough(ProductProfileInfo::class, Connection::class, "server_campaign_id", "item_code", "campaign_id",  "pcode")->withDefault(["prod_max_adver_cost" => "N/A",]);
    }

    /**
     * Get all of the platform for the campaign
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function platform(): HasOneThrough
    {
        return $this->hasOneThrough(Platform::class, Connection::class, "server_campaign_id", "id", "campaign_id",  "platform_id");
    }

    public function labels()
    {
        $this->primaryKey = "campaign_id";
        return $this->morphToMany(
            Label::class,
            'labelable'
        );
    }

    public function remarks()
    {
        $this->primaryKey = "campaign_id";
        return $this->morphMany(Remark::class, 'remarkable');
    }

    public function reasonables()
    {
        $this->primaryKey = "campaign_id";
        return $this->morphToMany(Reason::class, 'reasonable')->withPivot('status');
    }

    public function inActiveAdset()
    {
        return $this->hasMany(HistoryAdset::class, "campaign_id", "id");
    }
}
