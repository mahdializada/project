export default function (appContext) {
  return {
    //facebook
    facebook_campaign: [
      { value: "campaign_id", text: "Campaign ID" },
      { value: "name", text: "Campaign name" },
      { value: "status", text: "Campaign status" },
      { value: "delivery_status", text: "Delivery status" },
      { value: "objective", text: "Objective" },
    ],
    facebook_ad_set: [
      { value: "adset_id", text: "Ad Set Id" },
      { value: "adset_id", text: "Ad Set Id" },
      { value: "name", text: "Ad Set name" },
    ],
    facebook_ad: [
      { value: "adset_id", text: "Ad Set Id" },
      { value: "adset_id", text: "Ad Set Id" },
      { value: "name", text: "Ad Set name" },
    ],
    //google analytics
    google_campaign: [
      { value: "campaign_id", text: "Campaign ID" },
      { value: "name", text: "Campaign name" },
      { value: "status", text: "Status" },
      { value: "delivery_status", text: "Delivery status" },
      { value: "objective", text: "Objective" },
      { value: "budget", text: "Budget" },
      { value: "budget_mode", text: "Budget type" },
    ],
    google_ad_set: [
      { value: "adset_id", text: "Ad Set Id" },
      { value: "status", text: "Status" },
      { value: "server_campaign_id", text: "Campaign ID" },
      { value: "server_campaign_id", text: "Campaign ID" },
    ],
    google_ad: [
      { value: "ad_id", text: "Ad ID" },
      { value: "ad_name", text: "ad_name" },
      { value: "server_adset_id", text: "Ad Group" },
      { value: "server_campaign_id", text: "Campaign ID" },
      { value: "status", text: "Status" },
      { value: "impressions", text: "Impressions" },
      { value: "spend", text: "Spend" },
      { value: "clicks", text: "Clicks" },
      { value: "viewable_impressions", text: "Viewable Impressions" },
      { value: "total_page_views", text: "Total Page Views" },
      { value: "video_views", text: "Video Views" },
    ],
    //snap chat
    snapchat_campaign: [
      { value: "campaign_id", text: "Campaign ID" },
      { value: "name", text: "Campaign name" },
      { value: "status", text: "Campaign status" },
      { value: "delivery_status", text: "Delivery status" },
      { value: "objective", text: "Objective" },
    ],
    snapchat_ad_set: [
      { value: "adset_id", text: "Ad Set Id" },
      { value: "adset_id", text: "Ad Set Id" },
      { value: "name", text: "Ad Set name" },
    ],
    //tiktock
    tiktok_campaign: [
      { value: "campaign_id", text: "Campaign ID" },
      { value: "name", text: "Campaign name" },
      { value: "status", text: "Primary status" },
      { value: "delivery_status", text: "Delivery status" },
      { value: "objective", text: "Objective" },
      { value: "budget", text: "Campaign Budget" },
      { value: "created_date", text: "Date Created" },
    ],
    tiktok_ad_set: [
      { value: "adset_id", text: "Ad Set Id" },
      { value: "adset_id", text: "Ad Set Id" },
      { value: "name", text: "Ad Set name" },
    ],
  };
}
