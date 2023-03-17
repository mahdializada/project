<?php

namespace App\Models;

use App\Models\User;
use App\Models\State;
use App\Models\Reason;
use App\Models\Company;
use App\Models\Country;
use App\Traits\UuidTrait;
use Illuminate\Support\Carbon;
// use App\Models\BusinessLocation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BusinessLocation extends Model
{
    use HasFactory, UuidTrait, SoftDeletes;


    protected $fillable = [
        "name", "status", "location_type", "email", "address",
        "note", "code", "map_link",
        "state_id", "company_id", "country_id", "updated_by", "created_by",
    ];

    protected static $types = ["active", "inactive", "blocked", "pending", "removed", "warning"];

    public static function getTypes()
    {
        return BusinessLocation::$types;
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function reasons()
    {
        return $this->belongsToMany(Reason::class, 'business_location_reason', 'business_location_id', 'reason_id')->withPivot('status', 'changed_by')->withTimestamps();
    }

    public function changedBy()
    {
        return $this->belongsToMany(User::class, 'business_location_reason', 'business_location_id', 'changed_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, "updated_by");
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }
}
