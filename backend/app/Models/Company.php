<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\UuidTrait;
use App\Models\Advertisement\Platform;
use App\Models\Advertisement\AdAccount;
use App\Models\Advertisement\AdAccountBalance;
use App\Models\Advertisement\HistoryAd;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\Advertisement\Connection;
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

class Company extends Model
{
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;
    use HasFactory, SoftDeletes, UuidTrait;

    protected $fillable = ["name", "code", "status", "advertisement_status", 'domain', "logo", "note", "email", "investment_type", "updated_by", "created_by"];

    protected $hidden = ["updated_at", "pivot"];

    protected static $types = ["active", "inactive", "blocked", "pending", "removed", "warning"];

    public static function getTypes()
    {
        return Company::$types;
    }

    public function getLogoAttribute($value): string
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        return env("APP_URL") . Storage::url($value);
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, "updated_by");
    }

    public function systems()
    {
        return $this->belongsToMany(System::class);
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class);
    }

    public function reasons()
    {
        return $this->belongsToMany(Reason::class, 'reason_company', 'company_id', 'reason_id')->withPivot('status', 'changed_by')->withTimestamps();
    }

    public function changedBy()
    {
        return $this->belongsToMany(User::class, 'reason_company', 'company_id', 'changed_by');
    }

    public function socialMedia()
    {
        return $this->belongsToMany(SocialMedia::class, 'company_social_media', 'company_id', 'social_media_id')->withPivot('id', 'url');
    }



    public function platforms()
    {
        return $this->belongsToMany(Platform::class);
    }

    /**
     * Get all of the adThroughConnection for the Company
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function adThroughConnection(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryAd::class, Connection::class, "company_id", "ad_id", null,  "server_ad_id");
    }
    public function campaignThroughConnection(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryCampaign::class, Connection::class, "company_id", "campaign_id", null,  "server_campaign_id");
    }
    public function adThroughConnectionUpload(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryAdMU::class, ConnectionMU::class, "company_id", "ad_id", null,  "server_ad_id");
    }
    public function campaignThroughConnectionUpload(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryCampaignMU::class, ConnectionMU::class, "company_id", "campaign_id", null,  "server_campaign_id");
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
    public function adsetThroughConnection(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryAdset::class, Connection::class, "company_id", "adset_id", null,  "server_ad_adset_id");
    }

    public function adAccontThroughConnections(): HasManyThrough
    {
        return $this->hasManyThrough(
            AdAccount::class,
            Connection::class,
            "company_id",
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
            "company_id", // fk connection
            "add_account_id", //fk ad account
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
            'item_code',
            "id",
            "company_id"
        );


        // return $this->HasManyThrough(Country::class, Connection::class, "pcode", "id", "item_code",  "country_id");
    }

    public function onlineSales()
    {
        return $this->hasMany(OnlineSales::class);
    }
}
