<?php

namespace App\Models\Advertisement;

use App\Models\Label;
use App\Models\Reason;
use App\Models\Remark;
use App\Models\Company;
use App\Traits\UuidTrait;
use Illuminate\Support\Str;
use App\Models\Advertisement\AdAccount;
use App\Models\Advertisement\HistoryAd;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\Advertisement\Connection;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AdvetisementUpload\HistoryAdMU;
use App\Models\AdvetisementUpload\ConnectionMU;
use App\Models\AdvetisementUpload\HistoryCampaignMU;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Platform extends Model
{
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;
    use HasFactory, UuidTrait, SoftDeletes;

    public static array $STATUSES = ["ACTIVE", "INACTIVE"];

    protected $fillable = [
        "platform_name", "platform_key", 'system_status', "code", "advertisement_status"
    ];
    protected $appends = ['logo'];

    public function getLogoAttribute()
    {
        $icons = [
            "twitter" => "social-media/circle_twitter_icon.svg",
            "snapchat" => "social-media/circle_snapchat_icon.svg",
            "linkedin" => "social-media/circle_linkedin_icon.svg",
            "instagram" => "social-media/circle_instagram_icon.svg",
            "google ads"     => "social-media/circle_google_icon.svg",
            "tiktok" => "social-media/circle_tiktok_icon.svg",
            "facebook" => "social-media/circle_facebook_logo_icon.svg",
            "youtube" => "social-media/circle_youtube_icon.svg",
            "google analytics" =>  "social-media/circle_google_analytics_icon.svg",
        ];

        $icon =  $icons[$this->platform_name];
        return env("APP_URL") . Storage::url($icon);
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class,);
    }

    public function connection()
    {
        return $this->hasOne(Connection::class,);
    }
    public function connection_date()
    {
        return $this->hasOne(Connection::class);
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
        return $this->hasManyThrough(HistoryAd::class, Connection::class, "platform_id", "ad_id", null,  "server_ad_id");
    }
    public function campaignThroughConnection(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryCampaign::class, Connection::class, "platform_id", "campaign_id", null,  "server_campaign_id");
    }
    public function adsetThroughConnection(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryAdset::class, Connection::class, "platform_id", "adset_id", null,  "server_ad_adset_id");
    }
    public function adThroughConnectionUpload(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryAdMU::class, ConnectionMU::class, "platform_id", "ad_id", null,  "server_ad_id");
    }
    public function campaignThroughConnectionUpload(): HasManyThrough
    {
        return $this->hasManyThrough(HistoryCampaignMU::class, ConnectionMU::class, "platform_id", "campaign_id", null,  "server_campaign_id");
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
            'platform_id', // Foreign key on the application table...
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
    public function adsets()
    {
        return $this->hasManyDeepFromRelations($this->connections(), (new Connection)->campaigns(), (new HistoryCampaign)->campaignAdsets())->groupBy('history_adsets.id');
    }


    public function adAccontThroughConnections(): HasManyThrough
    {
        return $this->hasManyThrough(
            AdAccount::class,
            Connection::class,
            "platform_id",
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
            "platform_id", // fk connection
            "add_account_id", //fk ad account
            "id", //pk country
            "ad_account_id" // key connection
        );
    }
}
