<?php

namespace App\Models;


use Carbon\Carbon;
use App\Traits\UuidTrait;
use App\Models\Advertisement\AdAccount;
use App\Models\Advertisement\AdAccountBalance;
use App\Models\Advertisement\HistoryAd;
use Illuminate\Database\Eloquent\Model;
use App\Models\Advertisement\Connection;
use Illuminate\Notifications\Notifiable;
use App\Models\Advertisement\HistoryAdset;
use App\Models\Advertisement\HistoryCampaign;
use App\Models\Advertisement\ProductProfileInfo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AdvetisementUpload\HistoryAdMU;
use App\Models\AdvetisementUpload\ConnectionMU;
use App\Models\AdvetisementUpload\HistoryCampaignMU;
use App\Models\OnlineSalesManagement\OnlineSales;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Country extends Model
{
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;
    use  HasFactory, Notifiable, SoftDeletes, UuidTrait;


    protected $fillable = [
        "name", "code", "iso2", "iso3", "numeric_code", "phone_code", "capital", "national",
        "currency", "currency_name", "currency_symbol", "tld", "native", "region", "advertisement_status",
        "subregion", "timezones", "translations", "latitude", "longitude", "emoji", "emojiU", "visibility",
    ];

    protected $hidden = ["updated_at", 'pivot'];

    protected static $types = ["active", "inactive", "blocked"];

    public function getIso2Attribute($value)
    {
        return strtolower($value);
    }

    // protected $appends = ["connection_count"];

    public static function getTypes()
    {
        return Country::$types;
    }

    public function languages()
    {
        return $this->belongsToMany(Language::class);
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class);
    }



    /**
     * Get all of the adThroughConnection for the Country
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function adThroughConnection(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryAd::class, Connection::class, "country_id", "ad_id", null,  "server_ad_id");
    }
    public function campaignThroughConnection(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryCampaign::class, Connection::class, "country_id", "campaign_id", null,  "server_campaign_id");
    }
    public function adThroughConnectionUpload(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryAdMU::class, ConnectionMU::class, "country_id", "ad_id", null,  "server_ad_id");
    }
    public function campaignThroughConnectionUpload(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryCampaignMU::class, ConnectionMU::class, "country_id", "campaign_id", null,  "server_campaign_id");
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

    public function connections()
    {
        return $this->hasMany(Connection::class);
    }
    public function connection_date()
    {
        return $this->hasOne(Connection::class);
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
            "country_id",
            "account_id",
            "id",
            "server_account_id"
        );
    }

    public function adsetThroughConnection(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryAdset::class, Connection::class, "country_id", "adset_id", null,  "server_ad_adset_id");
    }



    public function adAccontBalanceThroughConnection(): HasManyThrough
    {
        return $this->hasManyThrough(
            AdAccount::class,
            Connection::class,
            "country_id", // fk connection
            "id", //fk ad account
            "id", //pk country
            "ad_account_id" // key connection
        );
    }

    public function productProfileInfo(): HasManyThrough
    {
        return $this->hasManyThrough(

            ProductProfileInfo::class,
            Connection::class,
            "pcode",
            "item_code",
            "id",
            "country_id"
        );


        // Deployment::class,
        // Environment::class,
        // 'project_id', // Foreign key on the environments table...
        // 'environment_id', // Foreign key on the deployments table...
        // 'id', // Local key on the projects table...
        // 'id' // Local key on the environments table...
    }

    public function onlineSales()
    {
        return $this->hasMany(OnlineSales::class);
    }
}
