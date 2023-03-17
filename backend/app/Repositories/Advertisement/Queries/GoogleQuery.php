<?php

namespace App\Repositories\Advertisement\Queries;

class GoogleQuery
{

    public static string $ADSET_QUERY = "SELECT ad_group.id, ad_group.name, ad_group.status, segments.date, segments.day_of_week, accessible_bidding_strategy.id, accessible_bidding_strategy.name, accessible_bidding_strategy.type, bidding_strategy.currency_code, bidding_strategy.id, bidding_strategy.name, bidding_strategy.status, bidding_strategy.type, campaign.id, customer.id FROM ad_group WHERE segments.date DURING TODAY ";

    public static string $ACCOUNT_QUERY = "SELECT customer.status, customer.manager, customer.id, customer.descriptive_name, customer.currency_code, customer.time_zone, customer.test_account FROM customer";

    public static string $CAMPAIGN_QUERY = "SELECT campaign.end_date, campaign.id, campaign.name, campaign.start_date, campaign.status, customer.id, segments.date, segments.day_of_week, bidding_strategy.type, campaign_budget.amount_micros, campaign_budget.type FROM campaign WHERE segments.date DURING TODAY ";

    public static string $AD_CAMPAIGN_QUERY = "SELECT campaign.resource_name, campaign.status,  campaign.name, campaign.id, campaign.start_date, campaign.end_date FROM ad_group_ad WHERE campaign.id ";

    public static string $AD_CAMPAIGN_ADSET_QUERY = "SELECT  customer.id, customer.currency_code, ad_group.id, campaign.id, ad_group_ad.ad.id FROM ad_group_ad WHERE ad_group_ad.ad.id = ";

    public static string $ADS_QUERY = "SELECT ad_group_ad.ad.id, ad_group_ad.ad.name, ad_group_ad.status, metrics.active_view_impressions, metrics.clicks, metrics.cost_micros, metrics.impressions, metrics.video_views, segments.day_of_week, customer.currency_code, segments.date, ad_group.id, campaign.id, customer.id FROM ad_group_ad WHERE segments.date DURING TODAY ";
}
