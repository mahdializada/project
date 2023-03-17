<?php

namespace App\Repositories\Advertisement;

class PlatformUrl
{
    // FACEBOOK
    static public  $FB_AD_BASE_URL = "https://graph.facebook.com/v14.0";
    static public  $FB_AUTH_BASE_URL = "https://graph.facebook.com/v14.0/oauth/";
    static public  $FB_REDIRECT_URL = "https://apps.smartfriqi.com/advertisement/applications";

    // TEEBALHOOR
    static public  $TEEB_AL_HOOR_BASE_UR = "https://api.teebalhoor.net/public";
    static public  $QATAR_TEEB_AL_HOOR_BASE_UR = "https://qatarapi.teebalhoor.net/public";

    // SNAPCHAT
    static public  $SNAPCHAT_AD_BASE_URL = "https://adsapi.snapchat.com/v1";
    static public  $SNAPCHAT_AUTH_URL = "https://accounts.snapchat.com/login/oauth2";
    static public  $SNAPCHAT_REDIRECT_URL = "https://apps.smartfriqi.com/advertisement/applications";

    // TIKTOK
    static public $TIKTOK_V3_BASE_URL = "https://business-api.tiktok.com/open_api/v1.3/";
    static public $TIKTOK_V2_BASE_URL = "https://business-api.tiktok.com/open_api/v1.2/";
    static public $TIKTOK_BASE_AUTH_URL = "https://business-api.tiktok.com/open_api/oauth2";
    static public $TIKTOK_ACCOUNT_V3_BASE_URL = "https://business-api.tiktok.com/open_api/v1.3/oauth2/";
    static public $TIKTOK_REDIRECT_URL = "https://apps.smartfriqi.com/advertisement/applications";


    // GOOGLE
    static public $GOOGLE_ADS_AUTH_BASE_URL = "https://www.googleapis.com/oauth2/v3/";
    static public $GOOGLE_ADS_BASE_URL = "https://googleads.googleapis.com/v11/";
    static public $GOOGLE_ANALYTICS_BASE_URL = "https://analyticsdata.googleapis.com/v1beta/";
    static public $GOOGLE_REDIRECT_URL = "https://apps.smartfriqi.com/advertisement/applications";
}
