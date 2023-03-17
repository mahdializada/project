<?php

namespace App\Models\Advertisement;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PlatformName extends Model
{
    use HasFactory, UuidTrait;
    protected $fillable = [
        "name",
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

        $icon =  $icons[$this->name];
        return env("APP_URL") . Storage::url($icon);
    }

}
