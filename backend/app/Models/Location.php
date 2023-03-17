<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;

class Location extends Model
{
    use HasFactory, UuidTrait;
    protected $fillable = [
        'location_type',
        'address',
        'map_link',
        'location_phone',
        'crowd_status',
        'contact_staff_name',
        'notes',
        'country_id',
        'state_id',
        'supplier_id'
    ];



    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
