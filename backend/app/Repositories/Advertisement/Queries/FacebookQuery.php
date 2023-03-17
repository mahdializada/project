<?php

namespace App\Repositories\Advertisement\Queries;

class FacebookQuery
{
    /**
     * Facebook Ad Account Fields
     */
    public static string $AD_ACCOUNT_QUERY =
    "id,name,account_id,account_status,amount_spent,balance,currency,timezone_name,created_time";

    /**
     * Facebook Adset Fields
     */
    public static string $ADSET_QUERY =
    "id,name,status,bid_amount,bid_strategy,daily_budget,effective_status,optimization_goal,campaign_id,start_time,created_time,updated_time";

    /**
     * Facebook Campaigns Fields
     */
    public static string $CAMPAIGN_QUERY =
    "id,name,account_id,status,objective,start_time,stop_time,updated_time,created_time,effective_status";

    /**
     * Facebook Campaigns Fields
     */
    public static string $AD_FIELDS = 'id,campaign_id,adset_id,account_id,status,name,created_time,updated_time';

    public static string $AD_INSIGHTS = 'ad_id,impressions,spend,created_time,clicks,account_currency,purchase_roas,reach,website_purchase_roas,video_p25_watched_actions,video_p50_watched_actions,video_p75_watched_actions,video_avg_time_watched_actions,cost_per_15_sec_video_view,date_start,date_stop,frequency,objective,optimization_goal';
}
