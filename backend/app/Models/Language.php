<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Language extends Model
{
    use HasFactory, SoftDeletes, UuidTrait;

    protected $fillable = ['id', "name", "native", 'dir', 'code'];
    protected static $types = ["active", "inactive", "blocked", "pending", "removed", "warning"];

    public static function getTypes()
    {
        return Language::$types;
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class);
    }

    public function translatedLanguage()
    {
        return $this->hasOne(TranslatedLanguage::class);
    }
}
