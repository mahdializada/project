<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    protected $table='locations';
    protected $fillable = [
        'id',
        'longitude',
        'latitude',
        'country_id',
        'state_district',
        'city',
        'address_line',
    ];
    public function country(){
        return $this->belongsTo(Country::class);
    }
    public function companies(){
        return $this->hasMany(Company::class);
    }
    public function branch(){
        return $this->belongsTo(Branche::class);
    }
}
