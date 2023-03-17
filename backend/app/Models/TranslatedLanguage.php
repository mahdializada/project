<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TranslatedLanguage extends Model
{
    use HasFactory, SoftDeletes, UuidTrait;

    protected $fillable = [
        "created_by",
        "country_language_id",
        "updated_by",
        "code",
        "status",
        "direction",
        "updated_at"
    ];
    protected $with = [];

    protected static $types = ["active", "inactive", "blocked", "pending", "removed"];

    public static function getTypes()
    {
        return TranslatedLanguage::$types;
    }


    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function languagePhrase()
    {
        return $this->belongsTo(LanguagePhrase::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, "updated_by");
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }
    public function countryLanguage()
    {
        return $this->belongsTo(CountryLanguage::class);
    }
}
