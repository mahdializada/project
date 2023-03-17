<?php

namespace App\Models\AdvetisementUpload;

use App\Models\Label;
use App\Models\Reason;
use App\Models\Remark;
use App\Traits\UuidTrait;
use Illuminate\Support\Carbon;
use App\Models\Advertisement\Platform;
use App\Models\Advertisement\AdAccount;
use Illuminate\Database\Eloquent\Model;
use App\Models\Advertisement\Connection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;


class HistoryCampaignMU extends Model
{
    use HasFactory, UuidTrait;

    protected $table = "manual_upload_campaigns";

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
    protected  function getDeliveryStatusAttribute($value)
    {
        return $value ?? "N/A";
    }


    // relations
    public function adAccount()
    {
        return $this->belongsTo(AdAccount::class, "ad_account_id");
    }

    public function adset()
    {
        $today = Carbon::today()->toDateString();
        $condition = [["data_date", "=", $today]];
        return $this->hasMany(HistoryAdsetMU::class, 'server_campaign_id', 'campaign_id')->where($condition);
    }

    public function campaignAdsets()
    {
        return $this->hasMany(HistoryAdsetMU::class, 'server_campaign_id', 'campaign_id');
    }


    public function connections()
    {
        return $this->hasMany(ConnectionMU::class, "server_campaign_id", "campaign_id");
    }

    /**
     * Get all of the adThroughConnectionUpload for the Account
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function adThroughConnectionUpload(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryAdMU::class, ConnectionMU::class, "server_campaign_id", "ad_id", "campaign_id",  "server_ad_id");
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
        # code...
    }

    public function remarks()
    {
        $this->primaryKey = "campaign_id";
        return $this->morphMany(Remark::class, 'remarkable');
    }

    public function reasonables()
    {
        return $this->morphToMany(Reason::class, 'reasonable')->withPivot('status');
    }
}
