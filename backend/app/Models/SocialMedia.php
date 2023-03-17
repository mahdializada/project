<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UuidTrait;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SocialMedia extends Model
{
    use HasFactory, SoftDeletes, UuidTrait;
    protected $fillable = [
        'code',
        "name",
        "website",
        "logo",
        'sample_url_account',
        'status',
        'created_by',
        'updated_by'
    ];
    protected static $types = ["active", "inactive", "blocked", "pending", "removed"];

    public static function getTypes()
    {
        return SocialMedia::$types;
    }


    public function getLogoAttribute($value): string
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        return env("APP_URL") . Storage::url($value);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, "updated_by");
    }

}
