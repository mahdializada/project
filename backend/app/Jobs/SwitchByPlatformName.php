<?php

namespace App\Jobs;

use App\Events\SendRefreshAdsEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use App\Models\Advertisement\Connection;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Repositories\Advertisement\Platforms\TikTok;
use App\Repositories\Advertisement\Platforms\Facebook;
use App\Repositories\Advertisement\Platforms\GoogleAd;
use App\Repositories\Advertisement\Platforms\Snapchat;
use Exception;

class SwitchByPlatformName implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Connection  $connectionItem;

    public array $extra_data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Connection $connection, array $extra_data)
    {
        $this->connectionItem = $connection;
        $this->extra_data   = $extra_data;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {



        $platform_name = $this->connectionItem->platform->platform_name;
        $server_ad_id = $this->connectionItem->historyAd->ad_id;

        switch ($platform_name) {
            case 'facebook':
                $item = Facebook::createAdWithAdsetAndCampaign($this->connectionItem, $server_ad_id, $this->extra_data);
                $items[] = $item;
                break;
            case 'snapchat':
                $item = Snapchat::createAdWithAdsetAndCampaign($this->connectionItem, $server_ad_id, $this->extra_data);
                $items[] = $item;
                break;
            case 'tiktok':
                $item = TikTok::createAdWithAdsetAndCampaign($this->connectionItem, $server_ad_id, $this->extra_data);
                $items[] = $item;
                break;
            case 'google ads':
                $item = GoogleAd::createAdWithAdsetAndCampaign($this->connectionItem, $server_ad_id, $this->extra_data);
                $items[] = $item;
                break;
            default:
                return null;
        }
    }

    public function failed($exception)
    {

        // throw new Exception($exception->getMessage(), 6000);
        dd($exception->getMessage());
        // $error = ['status' => 'faild', 'faild_message' => $exception->getMessage()];
        // $error = array_merge($error, $this->extra_data);
        // SendRefreshAdsEvent::dispatch($error);
    }
}
