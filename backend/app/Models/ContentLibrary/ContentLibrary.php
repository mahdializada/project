<?php

namespace App\Models\ContentLibrary;

use App\Models\Company;
use App\Models\Country;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentLibrary extends Model
{
    use HasFactory, UuidTrait;
    protected $fillable = ['code', 'item_code', 'item_name', 'status', 'requested_when', 'content_source', 'content_type', 'content_language', 'season', 'risk_palicy', 'main_or_customer', 'info_offer', 'content_direction', 'voice_over', 'music', 'shooting', 'people', 'graphics','sales_type', 'country_id', 'company_id'];

    public function contentLibraryMedias()
    {
        return $this->hasMany(ContentLibraryMedia::class);
    }
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
