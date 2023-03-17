<?php

namespace App\Models\Advertisement;

use App\Models\Label;
use App\Models\Reason;
use App\Models\Remark;
use App\Traits\UuidTrait;
use Illuminate\Support\Carbon;
use App\Models\Advertisement\HistoryAd;
use Illuminate\Database\Eloquent\Model;
use App\Models\Advertisement\Connection;
use App\Models\Advertisement\Application;
use App\Models\Advertisement\AdAccountPixel;
use App\Models\AdvetisementUpload\HistoryAdMU;
use App\Models\AdvetisementUpload\ConnectionMU;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\AdvetisementUpload\HistoryCampaignMU;
use App\Repositories\Advertisement\AdvertisementUtil;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class AdAccount extends Model
{
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;
    use HasFactory, UuidTrait;
    public static array $STATUSES = ["ACTIVE", "INACTIVE"];


    protected $fillable = [
        "code", "name", "account_id", "account_po", "status", "currency", "timezone_name", "type", 'system_status',
        "balance", "organization_id", "billing_center_id", "application_id",
        "server_created_at", "server_updated_at", 'advertisement_status', 'ad_account_balance'
    ];

    protected $hidden = ["application", "application_id"];

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

    protected  function getTypeAttribute($value)
    {
        return $value ?? "N/A";
    }
    protected  function getBalanceAttribute($value)
    {
        return  $this->addCurrency($value);
    }
    protected  function getOrganizationIdAttribute($value)
    {
        return $value ?? "N/A";
    }
    protected  function getBillingCenterIdAttribute($value)
    {
        return $value ?? "N/A";
    }


    public function connection()
    {
        return $this->hasOne(Connection::class);
    }
    public function subcribtion()
    {
        return $this->hasOne(AdAccountSubscribtion::class);
    }


    public function connections()
    {
        return $this->hasMany(Connection::class);
    }
    public function connection_date()
    {
        return $this->hasOne(Connection::class);
    }

    public function application()
    {
        return $this->belongsTo(Application::class, "application_id");
    }

    /**
     * Get all of the adThroughConnection for the Account
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function adThroughConnection(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryAd::class, Connection::class, "ad_account_id", "ad_id", null,  "server_ad_id");
    }
    public function campaignThroughConnection(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryCampaign::class, Connection::class, "ad_account_id", "campaign_id", null,  "server_campaign_id");
    }
    public function adsetThroughConnection(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryAdset::class, Connection::class, "ad_account_id", "adset_id", null,  "server_ad_adset_id");
    }

    public function adThroughConnectionUpload(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryAdMU::class, ConnectionMU::class, "platform_id", "ad_id", null,  "server_ad_id");
    }
    public function campaignThroughConnectionUpload(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryCampaignMU::class, ConnectionMU::class, "platform_id", "campaign_id", null,  "server_campaign_id");
    }


    public function labels()
    {
        return $this->morphToMany(
            Label::class,
            'labelable'
        );
        # code...
    }

    public function remarks()
    {
        return $this->morphMany(Remark::class, 'remarkable');
    }
    public function reasonables()
    {
        return $this->morphToMany(Reason::class, 'reasonable')->withPivot('status');
    }

    public function adsets()
    {
        return $this->hasManyDeepFromRelations($this->connections(), (new Connection)->campaigns(), (new HistoryCampaign)->campaignAdsets())->groupBy('history_adsets.id');
    }
    private function addCurrency($value)
    {
        $value = AdvertisementUtil::round($value);
        return "$value USD";
    }


    public function balances()
    {
        return $this->hasMany(AdAccountBalance::class, 'add_account_id')->orderBY('created_at', 'desc');
    }

    /**
     * Get all of the adAccountPixels for the AdAccount
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function adAccountPixels(): HasMany
    {
        return $this->hasMany(AdAccountPixel::class, 'ad_account_id', 'account_id');
    }

    public function bankAccount()
    {
        return $this->hasMany(BankAccount::class, 'add_account_id')->orderBY('created_at', 'desc');
    }
}
