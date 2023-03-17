<?php

namespace App\Models\Advertisement;

use App\Models\Label;
use App\Models\Reason;
use App\Models\Remark;
use App\Models\Company;
use App\Models\Country;
use App\Traits\UuidTrait;
use App\Models\Advertisement\AdAccount;
use Illuminate\Database\Eloquent\Model;
use App\Models\Advertisement\Connection;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AdvetisementUpload\HistoryAdMU;
use App\Models\AdvetisementUpload\ConnectionMU;
use App\Models\AdvetisementUpload\HistoryCampaignMU;
use App\Models\OnlineSalesManagement\OnlineSales;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Project extends Model
{
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;
    use HasFactory, UuidTrait, SoftDeletes;
    public static array $STATUSES = ["ACTIVE", "INACTIVE"];

    protected $fillable = [
        "name", "countries", "domain", 'system_status', "companies", "code", "advertisement_status",
    ];

    public function company()
    {
        return $this->belongsTo(Company::class,);
    }

    public function getCountriesAttribute($countries)
    {
        $countries = json_decode($countries);
        if ($countries) {
            $countries = Country::whereIn("id", $countries)->select(["id", "name", "iso2"])->get();
            return $countries;
        }
        return [];
    }
    public function getCompaniesAttribute($companies)
    {
        $companies = json_decode($companies);
        if ($companies) {
            $companies = Company::whereIn("id", $companies)->select(["id", "name", "logo", "code"])->get();
            return $companies;
        }
        return [];
    }
    public function companies()
    {
        return $this->belongsToMany(Company::class,);
    }

    public function connection()
    {
        return $this->hasOne(Connection::class,);
    }


    public function connections()
    {
        return $this->hasMany(Connection::class,);
    }

    /**
     * Get all of the adThroughConnection for the Platform
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function adThroughConnection(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryAd::class, Connection::class, "project_id", "ad_id", null,  "server_ad_id");
    }
    public function campaignThroughConnection(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryCampaign::class, Connection::class, "project_id", "campaign_id", null,  "server_campaign_id");
    }

    public function adsetThroughConnection(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryAdset::class, Connection::class, "project_id", "adset_id", null,  "server_ad_adset_id");
    }

    public function adThroughConnectionUpload(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryAdMU::class, ConnectionMU::class, "project_id", "ad_id", null,  "server_ad_id");
    }
    public function campaignThroughConnectionUpload(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryCampaignMU::class, ConnectionMU::class, "project_id", "campaign_id", null,  "server_campaign_id");
    }

    /**
     * Get all of the ad_accounts of a Platform
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function ad_accounts(): HasManyThrough
    {
        return $this->hasManyThrough(
            AdAccount::class,
            Application::class,
            'project_id', // Foreign key on the application table...
            'application_id', // Foreign key on the ad_accounts table...
            'id', // Local key on the projects table...
            'id' // Local key on the application table...
        );
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
            "project_id",
            "account_id",
            "id",
            "server_account_id"
        );
    }
    public function adAccontBalanceThroughConnection(): HasManyThrough
    {
        return $this->hasManyThrough(
            AdAccountBalance::class,
            Connection::class,
            "project_id", // fk connection
            "add_account_id", //fk ad account
            "id", //pk country
            "ad_account_id" // key connection
        );
    }

    public function onlineSales()
    {
        return $this->hasMany(OnlineSales::class);
    }
}
