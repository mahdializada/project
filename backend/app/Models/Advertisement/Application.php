<?php

namespace App\Models\Advertisement;

use App\Models\User;
use App\Models\Label;
use App\Models\Reason;
use App\Models\Remark;
use App\Traits\UuidTrait;
use App\Models\Advertisement\Platform;
use App\Models\Advertisement\AdAccount;
use App\Models\Advertisement\HistoryAd;
use Illuminate\Database\Eloquent\Model;
use App\Models\Advertisement\Connection;
use App\Models\Advertisement\HistoryCampaign;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AdvetisementUpload\HistoryAdMU;
use App\Models\AdvetisementUpload\ConnectionMU;
use App\Models\AdvetisementUpload\HistoryCampaignMU;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Application extends Model
{
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;
    use HasFactory, UuidTrait, SoftDeletes;
    public static array $STATUSES = ["ACTIVE", "INACTIVE", "TOKEN_EXPIRED", "REMOVED"];

    protected $fillable = [
        "name", "code", "client_id", "client_secret", "access_token", 'system_status',  "organization_id", "developer_token", "redirect_url", "scope", "refresh_token", "expires_in", "platform_id",  "updated_by", "created_by", 'country_id'
    ];

    protected $hidden = [
        "client_secret", "access_token", "organization_id", "developer_token", "refresh_token",
    ];


    protected  function getScopeAttribute($value)
    {
        return $value ?? "N/A";
    }

    public function connection()
    {
        return $this->hasOne(Connection::class,);
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class,);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, "updated_by");
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }


    public function connections()
    {
        return $this->hasMany(Connection::class);
    }
    public function connection_date()
    {
        return $this->hasOne(Connection::class);
    }

    /**
     * Get all of the adThroughConnection for the Application
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function adThroughConnection(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryAd::class, Connection::class, "application_id", "ad_id", null,  "server_ad_id");
    }
    public function campaignThroughConnection(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryCampaign::class, Connection::class, "application_id", "campaign_id", null,  "server_campaign_id");
    }
    public function adsetThroughConnection(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryAdset::class, Connection::class, "application_id", "adset_id", null,  "server_ad_adset_id");
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


    public function adAccontThroughConnections(): HasManyThrough
    {
        return $this->hasManyThrough(
            AdAccount::class,
            Connection::class,
            "application_id",
            "account_id",
            "id",
            "server_account_id"
        );
    }
}
