<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $table='countries';
    protected $fillable = [
        'iso2',
        'name',
        'capital',
        'native',
        'phone_code',
        'currency_name',
        'currency_symbol',
        'region',
        'subregion',
        'status',
        'timezones',

    ];
    public function locations(){
        return $this->hasMany(Location::class);
    }

    public function companies(){
        return $this->hasManyThrough(related: Company::class, through: Location::class );
    }

}
