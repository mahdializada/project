<?php

namespace App\Repositories\Advertisement\Queries;

class TikTokQuery
{
    /**
     * This query used to fetch adsets or ad group from tiktok API for Adset Tab in Advertisement Page
     */
    public static string $ADSET_FIELDS = '[
        "adgroup_id", "adgroup_name", "secondary_status", "budget", "schedule_start_time", "schedule_end_time",
        "optimization_goal", "bid_price", "bid_type", "modify_time", "campaign_id", "delivery_mode", "pixel_id", "advertiser_id", "create_time",
        "age_groups", "gender", "location_ids", "operating_systems", "placements", "languages", "network_types", "promotion_type", "placement_type","auto_targeting_enabled"
    ]';


    public static array $AD_METRICS = [
        "spend",
        "impressions",
        "reach",
        "clicks",
        "result",
        "frequency",
        "currency",
        "video_watched_2s",
        "video_watched_6s",
        "video_play_actions",
        "total_purchase"
    ];

    public static string $AD_FIELDS = '["ad_id", "ad_name", "secondary_status",  "modify_time", "advertiser_id","operation_status", "campaign_id", "adgroup_id","create_time"]';
}
