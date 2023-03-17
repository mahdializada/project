<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LanguagePhrase extends Model
{
    use HasFactory;


    protected $fillable = [
        'translation',
        'phrase_id',
        'translated_language_id',
        'created_by',
        'updated_by',
        'code'
    ];


    public function phrase()
    {
        return $this->belongsTo(Phrase::class);
    }

    public function translatedLanguage()
    {
        return $this->hasOne(TranslatedLanguage::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function UpdatedBy()
    {
        return $this->belongsTo(User::class, "updated_by");
    }
}
