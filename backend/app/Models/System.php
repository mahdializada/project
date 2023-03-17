<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class System extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = ["name", "logo", "phrase"];

    protected static $types = ["active", "inactive"];

    public static function getTypes()
    {
        return System::$types;
    }

    protected $hidden = ["pivot"];


    public function subSystems()
    {
        return $this->hasMany(SubSystem::class);
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class);
    }
}
