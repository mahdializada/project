<?php

namespace App\Models\AdvetisementUpload;

use App\Models\Advertisement\AdAccount;
use App\Models\Company;
use App\Models\Country;
use App\Traits\UuidTrait;
use App\Models\SingleSales\Ispp;
use App\Models\Advertisement\Project;
use App\Models\Advertisement\Platform;
use Illuminate\Database\Eloquent\Model;
use App\Models\Advertisement\Application;
use App\Models\Label;
use App\Models\Remark;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class ConnectionMU extends Model
{
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;
    use HasFactory, UuidTrait;

    protected $table = "manual_connections";

    public static array $STATUSES = ["ACTIVE", "INACTIVE"];

    protected $fillable = [
        "pcode", "code", "hashed_code", "sales_type", "pname", "generated_link", "server_account_id", "server_ad_id", "landing_page_link", "media_link", "currency", "timezone", 'system_status', "server_campaign_id", "server_ad_adset_id",
        "ispp_id", "platform_id", "application_id", "project_id", "ad_account_id", "company_id", "country_id", "history_ad_id",
    ];

    public static $salesType = ["Single Sale", "Shopping Cart"];

    public function application()
    {
        return $this->belongsTo(Application::class,);
    }

    public function adAccount()
    {
        return $this->belongsTo(AdAccount::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, "company_id");
    }

    public function country()
    {
        return $this->belongsTo(Country::class, "country_id");
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class, "platform_id");
    }
    public function campaigns()
    {
        return $this->belongsTo(HistoryCampaignMU::class, "server_campaign_id", 'campaign_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, "project_id");
    }

    public function ispp()
    {
        return $this->belongsTo(Ispp::class, "ispp_id");
    }

    public function todayHistoryAd()
    {
        $today = Carbon::today()->toDateString();
        $condition = [["data_date", "=", $today]];
        return $this->hasOne(HistoryAdMU::class, 'ad_id',  "server_ad_id")->where($condition);
    }

    public function historyAd()
    {
        return $this->hasOne(HistoryAdMU::class, 'ad_id',  "server_ad_id");
    }
    public function historyAdds()
    {
        return $this->hasMany(HistoryAdMU::class, 'ad_id',  "server_ad_id");
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


    public function adsets()
    {

        return $this->hasManyThrough(
            HistoryCampaignMU::class,
            HistoryAdsetMU::class,
            'server_campaign_id',
            'campaign_id',
            'campaign_id',
            'server_campaign_id'
        );
        return $this->hasManyDeepFromRelations($this->campaigns(), (new HistoryCampaignMU())->campaignAdsets());
    }
}
